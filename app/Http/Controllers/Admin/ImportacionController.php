<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionOperadorUnidad;
use App\Models\Operador;
use App\Models\Unidad;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportacionController extends Controller
{
    /**
     * Limpiar texto para UTF-8 válido
     */
    private function limpiarUtf8($texto)
    {
        if ($texto === null) {
            return '';
        }
        $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
        $texto = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $texto);

        return trim($texto);
    }

    public function index()
    {
        return view('admin.importacion.index');
    }

    public function importarUnidades(Request $request)
    {
        Log::info('Iniciando importación de unidades');

        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $archivo = $request->file('archivo')->getRealPath();
        Log::info('Archivo temporal: ' . $archivo);

        if (! file_exists($archivo) || ! is_readable($archivo)) {
            Log::error('Archivo no existe o no se puede leer');

            return back()->with('error', 'No se pudo leer el archivo. Verifica permisos.');
        }

        $handle = fopen($archivo, 'r');
        if ($handle === false) {
            Log::error('No se pudo abrir el archivo con fopen');

            return back()->with('error', 'Error al abrir el archivo.');
        }

        $cabeceras = fgetcsv($handle, 0, ',');
        if (! $cabeceras) {
            fclose($handle);
            Log::error('El CSV no tiene cabeceras');

            return back()->with('error', 'El archivo CSV está vacío o no tiene cabeceras.');
        }

        $cabeceras = array_map(function ($col) {
            return strtolower(trim($col, " \t\n\r\0\x0B\xEF\xBB\xBF"));
        }, $cabeceras);
        Log::info('Cabeceras detectadas: ', $cabeceras);

        $requeridas = ['numero_economico', 'zona'];
        foreach ($requeridas as $req) {
            if (! in_array($req, $cabeceras)) {
                fclose($handle);
                Log::error("Falta la columna requerida: {$req}");

                return back()->with('error', "El archivo debe contener la columna '{$req}'. Columnas detectadas: " . implode(', ', $cabeceras));
            }
        }

        $importadas = 0;
        $errores = [];
        $linea = 1;
        $zonasPermitidas = ['reyes', 'apaxco', 'citrus'];

        while (($datos = fgetcsv($handle, 0, ',')) !== false) {
            $linea++;
            if (count($datos) < count($cabeceras)) {
                $errores[] = "Línea {$linea}: número de columnas insuficiente (esperado " . count($cabeceras) . ', obtenido ' . count($datos) . ').';

                continue;
            }

            $fila = array_combine($cabeceras, array_slice($datos, 0, count($cabeceras)));

            if (empty($fila['numero_economico'])) {
                $errores[] = "Línea {$linea}: número económico vacío.";

                continue;
            }

            if (Unidad::where('numero_economico', $fila['numero_economico'])->exists()) {
                $errores[] = "Línea {$linea}: unidad {$fila['numero_economico']} ya existe. Se omite.";

                continue;
            }

            // Buscar o crear zona (insensible a mayúsculas)
            $zonaNombre = trim($fila['zona']);
            $zonaNombreLower = strtolower($zonaNombre);

            if (! in_array($zonaNombreLower, $zonasPermitidas)) {
                $errores[] = "Línea {$linea}: zona '{$fila['zona']}' no válida. Debe ser reyes, apaxco o citrus.";

                continue;
            }

            $zona = Zona::whereRaw('LOWER(nombre) = ?', [$zonaNombreLower])->first();
            if (! $zona) {
                $zona = Zona::create([
                    'nombre' => $zonaNombreLower,
                    'activo' => true,
                ]);
                Log::info("Zona '{$zonaNombreLower}' creada automáticamente.");
            }

            try {
                Unidad::create([
                    'numero_economico' => $this->limpiarUtf8($fila['numero_economico']),
                    'nombre_unidad' => isset($fila['nombre_unidad']) ? $this->limpiarUtf8($fila['nombre_unidad']) : null,
                    'zona_id' => $zona->id,
                    'activo' => isset($fila['activo']) ? filter_var($fila['activo'], FILTER_VALIDATE_BOOLEAN) : true,
                    'codigo_qr' => (string) Str::uuid(),
                    'token_qr' => Str::random(20),
                ]);
                $importadas++;
            } catch (\Exception $e) {
                $errores[] = "Línea {$linea}: error al guardar - " . $e->getMessage();
            }
        }

        fclose($handle);

        Log::info('Importadas: ' . $importadas);
        Log::info('Errores: ', $errores);

        if ($importadas > 0) {
            return back()->with('success', "Se importaron {$importadas} unidades.")->with('errores', $errores);
        } else {
            return back()->with('error', 'No se importó ninguna unidad. Revisa los errores.')->with('errores', $errores);
        }
    }

    public function importarOperadores(Request $request)
    {
        Log::info('=== INICIANDO IMPORTACIÓN DE OPERADORES ===');

        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $archivo = $request->file('archivo');
        Log::info('Archivo recibido: ' . $archivo->getClientOriginalName());
        Log::info('Tamaño: ' . $archivo->getSize() . ' bytes');

        // Leer primera línea para detectar separador
        $contenido = file_get_contents($archivo->getRealPath());

        // Detectar separador (coma, punto y coma, o TAB)
        $primeraLinea = strtok($contenido, "\n");
        $separador = $this->detectarSeparador($primeraLinea);
        Log::info('Separador detectado: ' . ($separador === "\t" ? 'TAB' : ($separador === ';' ? 'PUNTO Y COMA' : 'COMA')));

        // Guardar archivo temporal para debugging
        $tempPath = storage_path('logs/import_operadores_' . date('Ymd_His') . '.csv');
        file_put_contents($tempPath, $contenido);
        Log::info('Archivo guardado para depuración: ' . $tempPath);

        // Leer líneas
        $lineas = preg_split('/\r\n|\r|\n/', $contenido);
        if (empty($lineas)) {
            return back()->with('error', 'El archivo está vacío.');
        }

        // Procesar cabeceras con el separador detectado
        $cabeceras = str_getcsv(trim($lineas[0]), $separador);
        $cabeceras = array_map(function ($col) {
            $col = preg_replace('/^\xEF\xBB\xBF/', '', $col);
            return strtolower(trim($col));
        }, $cabeceras);

        Log::info('Cabeceras detectadas:', $cabeceras);

        // Mapeo de columnas alternativas
        $mapaColumnas = [
            'clave_operador' => ['clave_operador', 'clave', 'codigo', 'id_operador'],
            'nombre_completo' => ['nombre_completo', 'nombre', 'operador', 'nombre_operador'],
            'unidad' => ['unidad', 'numero_economico', 'unidad_id', 'economico'],
            'zona_nombre' => ['zona_nombre', 'zona', 'area'],
            'activo' => ['activo', 'estado', 'status'],
        ];

        // Encontrar las columnas reales
        $columnaClave = null;
        $columnaNombre = null;
        $columnaUnidad = null;
        $columnaZona = null;
        $columnaActivo = null;

        foreach ($cabeceras as $col) {
            if (in_array($col, $mapaColumnas['clave_operador'])) $columnaClave = $col;
            if (in_array($col, $mapaColumnas['nombre_completo'])) $columnaNombre = $col;
            if (in_array($col, $mapaColumnas['unidad'])) $columnaUnidad = $col;
            if (in_array($col, $mapaColumnas['zona_nombre'])) $columnaZona = $col;
            if (in_array($col, $mapaColumnas['activo'])) $columnaActivo = $col;
        }

        // Validar columnas requeridas (solo clave y nombre son obligatorios)
        $faltantes = [];
        if (!$columnaClave) $faltantes[] = 'clave_operador';
        if (!$columnaNombre) $faltantes[] = 'nombre_completo';

        if (!empty($faltantes)) {
            return back()->with('error', 'El archivo debe contener las columnas: ' . implode(', ', $faltantes) .
                '. Detectadas: ' . implode(', ', $cabeceras));
        }

        $importadas = 0;
        $errores = [];
        $lineaNumero = 1;

        // Procesar cada línea
        for ($i = 1; $i < count($lineas); $i++) {
            $lineaNumero++;
            $linea = trim($lineas[$i]);
            if (empty($linea)) {
                continue;
            }

            $datos = str_getcsv($linea, $separador);

            if (count($datos) < count($cabeceras)) {
                $errores[] = "Línea {$lineaNumero}: Número de columnas insuficiente.";
                continue;
            }

            // Crear array asociativo
            $fila = [];
            for ($j = 0; $j < count($cabeceras); $j++) {
                $fila[$cabeceras[$j]] = $datos[$j] ?? '';
            }

            // Obtener valores usando las columnas detectadas
            $clave = trim($fila[$columnaClave] ?? '');
            $nombreCompleto = trim($fila[$columnaNombre] ?? '');
            $numeroEconomico = $columnaUnidad ? trim($fila[$columnaUnidad] ?? '') : '';
            $zonaNombre = $columnaZona ? trim($fila[$columnaZona] ?? '') : '';

            Log::info("Procesando línea {$lineaNumero}: Clave='{$clave}', Nombre='{$nombreCompleto}', Unidad='{$numeroEconomico}', Zona='{$zonaNombre}'");

            // Validar clave (4 o 5 dígitos)
            if (empty($clave)) {
                $errores[] = "Línea {$lineaNumero}: Clave de operador vacía.";
                continue;
            }

            if (!preg_match('/^\d{4,5}$/', $clave)) {
                $errores[] = "Línea {$lineaNumero}: Clave '{$clave}' debe tener 4 o 5 dígitos.";
                continue;
            }

            if (Operador::where('clave_operador', $clave)->exists()) {
                $errores[] = "Línea {$lineaNumero}: Operador con clave {$clave} ya existe.";
                continue;
            }

            // Validar nombre
            if (empty($nombreCompleto)) {
                $errores[] = "Línea {$lineaNumero}: Nombre completo vacío.";
                continue;
            }

            // Buscar unidad por número económico (si se proporcionó)
            $unidad = null;
            if (!empty($numeroEconomico)) {
                $unidad = Unidad::where('numero_economico', $numeroEconomico)->first();
                if (!$unidad) {
                    $errores[] = "Línea {$lineaNumero}: Unidad '{$numeroEconomico}' no existe. El operador se creará sin unidad asignada.";
                    // No continuamos, permitimos crear operador sin unidad
                }
            }

            // Validar zona (si se proporciona y se puede validar)
            if (!empty($zonaNombre)) {
                $zonaNombreLower = strtolower($zonaNombre);
                $zonasPermitidas = ['reyes', 'apaxco', 'citrus'];
                if (!in_array($zonaNombreLower, $zonasPermitidas)) {
                    $errores[] = "Línea {$lineaNumero}: Zona '{$zonaNombre}' no válida. Debe ser reyes, apaxco o citrus.";
                    continue;
                }
            }

            // Activo (si existe la columna)
            $activo = true;
            if ($columnaActivo && isset($fila[$columnaActivo])) {
                $valor = strtolower(trim($fila[$columnaActivo]));
                $activo = in_array($valor, ['1', 'true', 'si', 'activo', 'sí', 'yes']);
            }

            try {
                $operador = Operador::create([
                    'clave_operador' => $clave,
                    'nombre_completo' => $nombreCompleto,
                    'activo' => $activo,
                ]);

                Log::info("✓ Operador creado ID: {$operador->id}, Clave: {$clave}");

                // Asignar unidad solo si existe y se proporcionó
                if ($unidad) {
                    // Finalizar asignaciones anteriores del operador
                    AsignacionOperadorUnidad::where('operador_id', $operador->id)
                        ->where('vigente', true)
                        ->update(['fecha_fin' => now(), 'vigente' => false]);

                    AsignacionOperadorUnidad::create([
                        'operador_id' => $operador->id,
                        'unidad_id' => $unidad->id,
                        'fecha_inicio' => now(),
                        'vigente' => true,
                    ]);
                    Log::info("✓ Operador {$clave} asignado a unidad {$numeroEconomico}");
                } else {
                    Log::info("✓ Operador {$clave} creado sin unidad asignada");
                }

                $importadas++;
            } catch (\Exception $e) {
                $errores[] = "Línea {$lineaNumero}: Error al guardar - " . $e->getMessage();
                Log::error("Error línea {$lineaNumero}: " . $e->getMessage());
            }
        }

        Log::info("=== IMPORTACIÓN FINALIZADA: {$importadas} operadores importados, " . count($errores) . " errores ===");

        $mensaje = "Se importaron {$importadas} operadores.";
        if (!empty($errores)) {
            $mensaje .= " Hubo " . count($errores) . " errores.";
            return back()->with('success', $mensaje)->with('errores', $errores);
        }

        return back()->with('success', $mensaje);
    }

    /**
     * Detecta el separador de una línea CSV
     */
    private function detectarSeparador($linea)
    {
        $separadores = [',', ';', "\t"];
        $cuentas = [];

        foreach ($separadores as $sep) {
            $cuentas[$sep] = substr_count($linea, $sep);
        }

        arsort($cuentas);
        return key($cuentas);
    }
}
