<?php

namespace App\Http\Controllers\Api;

use App\Enums\TipoMovimiento;
use App\Exceptions\ReglaNegocioException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MovimientoApiRequest;
use App\Http\Resources\MovimientoResource;
use App\Models\Departamento;
use App\Models\MovimientoDepartamento;
use App\Models\Unidad;
use App\Models\Usuario;
use App\Services\MovimientoService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class MovimientoController extends Controller
{
    public function __construct(private readonly MovimientoService $servicio) {}

    /** Historial reciente del departamento del usuario (o del indicado). */
    public function index(Request $request)
    {
        $departamentoId = Departamento::decodeHashid($request->query('departamento_id')) ?? $request->user()->departamento_id;

        $movimientos = MovimientoDepartamento::with(['unidad', 'departamento', 'usuario'])
            ->when($departamentoId, fn ($q, $d) => $q->where('departamento_id', $d))
            ->when($request->query('fecha'), fn ($q, $f) => $q->whereDate('fecha_hora', $f))
            ->latest('fecha_hora')
            ->paginate(30);

        return MovimientoResource::collection($movimientos);
    }

    /** Unidades que estan dentro del departamento en este momento. */
    public function dentro(Request $request)
    {
        $departamentoId = Departamento::decodeHashid($request->query('departamento_id')) ?? $request->user()->departamento_id;

        return MovimientoResource::collection(
            MovimientoDepartamento::estanciasAbiertas()
                ->with(['unidad', 'departamento'])
                ->when($departamentoId, fn ($q, $d) => $q->where('departamento_id', $d))
                ->orderBy('fecha_hora')
                ->get(),
        );
    }

    public function store(MovimientoApiRequest $request)
    {
        [$movimiento, $creado] = $this->registrar($request->validated(), $request->user());

        return (new MovimientoResource($movimiento->load(['unidad', 'departamento'])))
            ->response()
            ->setStatusCode($creado ? 201 : 200);
    }

    /**
     * Sincronizacion en lote de la cola offline. Cada elemento se procesa por
     * separado y se informa su resultado para que la app sepa que borrar.
     */
    public function sincronizar(Request $request)
    {
        abort_unless($request->user()->tokenCan('movimientos:escribir'), 403);
        $request->validate(['movimientos' => ['required', 'array', 'min:1', 'max:100']]);

        $resultados = [];
        foreach ($request->input('movimientos') as $item) {
            $uuid = is_array($item) ? ($item['uuid'] ?? null) : null;
            $validador = Validator::make(is_array($item) ? $this->decodificar($item) : [], (new MovimientoApiRequest)->rules());
            if ($validador->fails()) {
                $resultados[] = ['uuid' => $uuid, 'estado' => 'rechazado', 'mensaje' => $validador->errors()->first()];
                continue;
            }
            try {
                [$mov, $creado] = $this->registrar($validador->validated(), $request->user());
                $resultados[] = ['uuid' => $uuid, 'estado' => $creado ? 'creado' : 'duplicado', 'id' => $mov->hashid];
            } catch (ReglaNegocioException $e) {
                $resultados[] = ['uuid' => $uuid, 'estado' => 'rechazado', 'mensaje' => $e->getMessage()];
            }
        }

        return response()->json(['resultados' => $resultados]);
    }

    private function registrar(array $datos, Usuario $usuario): array
    {
        $unidad = ! empty($datos['unidad_id'])
            ? Unidad::find($datos['unidad_id'])
            : Unidad::where('token_qr', $datos['token_qr'] ?? '')->first();
        if (! $unidad) {
            throw new ReglaNegocioException('Unidad no encontrada.');
        }

        $departamento = ! empty($datos['departamento_id']) ? Departamento::find($datos['departamento_id']) : $usuario->departamento;
        if (! $departamento) {
            throw new ReglaNegocioException('No se indico departamento y el usuario no tiene uno asignado.');
        }
        // Un usuario de departamento solo registra movimientos de su propio departamento
        if (! $usuario->accedePanel() && $departamento->id !== $usuario->departamento_id) {
            throw new ReglaNegocioException('Solo puedes registrar movimientos de tu departamento.');
        }

        $tipo = ! empty($datos['tipo']) ? TipoMovimiento::from($datos['tipo']) : $this->servicio->tipoSugerido($unidad, $departamento);

        return $this->servicio->registrar(
            $unidad,
            $departamento,
            $tipo,
            $usuario,
            ! empty($datos['fecha_hora']) ? Carbon::parse($datos['fecha_hora']) : null,
            $datos['observaciones'] ?? null,
            $datos['uuid'] ?? null,
        );
    }

    private function decodificar(array $item): array
    {
        foreach (['unidad_id' => Unidad::class, 'departamento_id' => Departamento::class] as $campo => $modelo) {
            if (! empty($item[$campo])) {
                $item[$campo] = $modelo::decodeHashid((string) $item[$campo]) ?? 0;
            }
        }

        return $item;
    }
}
