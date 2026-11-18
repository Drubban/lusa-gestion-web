<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TipoTecnologia;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportarCsvRequest;
use App\Models\Operador;
use App\Models\Tecnologia;
use App\Models\Unidad;
use App\Models\Zona;
use App\Services\AsignacionService;
use App\Services\BitacoraService;
use App\Support\LectorCsv;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ImportacionController extends Controller
{
    public function __construct(private readonly AsignacionService $asignaciones) {}

    public function index()
    {
        return view('admin.importacion.index', ['tipos' => TipoTecnologia::cases()]);
    }

    public function unidades(ImportarCsvRequest $request)
    {
        return $this->procesar($request, ['numero_economico', 'zona'], function (array $fila, int $linea) {
            if ($fila['numero_economico'] === '') {
                return "Linea {$linea}: numero economico vacio.";
            }
            if (Unidad::where('numero_economico', $fila['numero_economico'])->exists()) {
                return "Linea {$linea}: la unidad {$fila['numero_economico']} ya existe.";
            }
            $zona = Zona::whereRaw('LOWER(nombre) = ?', [mb_strtolower($fila['zona'])])->first();
            if (! $zona) {
                return "Linea {$linea}: la zona '{$fila['zona']}' no existe. Dala de alta antes de importar.";
            }
            Unidad::create([
                'numero_economico' => $fila['numero_economico'],
                'nombre_unidad' => $fila['nombre_unidad'] ?? null ?: null,
                'zona_id' => $zona->id,
                'activo' => LectorCsv::booleano($fila['activo'] ?? null),
            ]);

            return null;
        }, 'unidades');
    }

    public function operadores(ImportarCsvRequest $request)
    {
        return $this->procesar($request, ['clave_operador', 'nombre_completo'], function (array $fila, int $linea) {
            if ($fila['clave_operador'] === '' || $fila['nombre_completo'] === '') {
                return "Linea {$linea}: clave o nombre vacio.";
            }
            if (Operador::where('clave_operador', $fila['clave_operador'])->exists()) {
                return "Linea {$linea}: el operador {$fila['clave_operador']} ya existe.";
            }
            $operador = Operador::create([
                'clave_operador' => $fila['clave_operador'],
                'nombre_completo' => $fila['nombre_completo'],
                'activo' => LectorCsv::booleano($fila['activo'] ?? null),
            ]);
            $economico = $fila['numero_economico'] ?? '';
            if ($economico !== '') {
                $unidad = Unidad::where('numero_economico', $economico)->first();
                if (! $unidad) {
                    return "Linea {$linea}: operador creado, pero la unidad {$economico} no existe.";
                }
                $this->asignaciones->asignar($unidad, $operador);
            }

            return null;
        }, 'operadores');
    }

    public function tecnologias(ImportarCsvRequest $request)
    {
        return $this->procesar($request, ['numero_economico', 'tipo'], function (array $fila, int $linea) use ($request) {
            $unidad = Unidad::where('numero_economico', $fila['numero_economico'])->first();
            if (! $unidad) {
                return "Linea {$linea}: unidad '{$fila['numero_economico']}' no encontrada.";
            }
            $tipo = TipoTecnologia::tryFrom(mb_strtolower($fila['tipo']));
            if (! $tipo) {
                return "Linea {$linea}: tipo '{$fila['tipo']}' invalido.";
            }
            if (Tecnologia::where('unidad_id', $unidad->id)->where('tipo', $tipo->value)->exists()) {
                return "Linea {$linea}: la unidad ya tiene {$tipo->etiqueta()}.";
            }
            $tecnologia = Tecnologia::create([
                'unidad_id' => $unidad->id,
                'tipo' => $tipo,
                'nombre' => $fila['nombre'] ?? null ?: null,
                'activo' => LectorCsv::booleano($fila['activo'] ?? null),
                'created_by' => $request->user()->id,
            ]);
            $detalle = [];
            foreach (array_keys($tipo->campos()) as $campo) {
                $valor = $fila[$campo] ?? '';
                $detalle[$campo] = $valor === '' ? null : $valor;
            }
            $tecnologia->{$tipo->relacion()}()->create($detalle);

            return null;
        }, 'tecnologias');
    }

    public function plantillaTecnologias()
    {
        $columnas = ['numero_economico', 'tipo', 'nombre', 'activo'];
        foreach (TipoTecnologia::cases() as $tipo) {
            $columnas = array_merge($columnas, array_keys($tipo->campos()));
        }
        $columnas = array_values(array_unique($columnas));

        return response()->streamDownload(function () use ($columnas) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $columnas);
            fputcsv($out, array_map(fn ($c) => match ($c) {
                'numero_economico' => '405', 'tipo' => 'gps', 'activo' => '1', 'imei_gps' => '860000000000000', default => '',
            }, $columnas));
            fclose($out);
        }, 'plantilla_tecnologias.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Recorre el CSV fila por fila; cada fila corre en su propia transaccion
     * para que un error no revierta las filas correctas.
     */
    private function procesar(ImportarCsvRequest $request, array $requeridas, callable $porFila, string $entidad)
    {
        $lector = new LectorCsv($request->file('archivo')->getRealPath());
        $importadas = 0;
        $errores = [];

        try {
            foreach ($lector->filas($requeridas) as $linea => $fila) {
                try {
                    $error = DB::transaction(fn () => $porFila($fila, $linea));
                    $error ? $errores[] = $error : $importadas++;
                } catch (\Throwable $e) {
                    report($e);
                    $errores[] = "Linea {$linea}: no se pudo guardar.";
                }
            }
        } catch (RuntimeException $e) {
            return back()->withErrors(['archivo' => $e->getMessage()]);
        }

        BitacoraService::registrar("importacion.{$entidad}", null, ['importadas' => $importadas, 'errores' => count($errores)]);

        return back()
            ->with($importadas ? 'success' : 'error', "Se importaron {$importadas} {$entidad}." . ($errores ? ' Revisa los avisos.' : ''))
            ->with('errores', array_slice($errores, 0, 200));
    }
}
