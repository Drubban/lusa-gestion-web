<?php

namespace App\Http\Controllers\Api;

use App\Enums\DispositivoRevision;
use App\Enums\EstadoRevision;
use App\Http\Controllers\Controller;
use App\Http\Requests\RevisionRequest;
use App\Http\Resources\RevisionResource;
use App\Models\RevisionOptocontrol;
use App\Models\Unidad;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class RevisionController extends Controller
{
    public function index(Request $request)
    {
        $revisiones = RevisionOptocontrol::with('unidad')
            ->filtrar($request->only(['status', 'disp', 'search']) + ['unidad_id' => Unidad::decodeHashid($request->query('unidad_id'))])
            ->when(! $request->query('status'), fn ($q) => $q->whereIn('status', ['pendiente', 'en_proceso']))
            ->latest('fecha_reporte')->latest('id')
            ->paginate(30);

        return RevisionResource::collection($revisiones);
    }

    public function catalogos()
    {
        return response()->json(['dispositivos' => DispositivoRevision::opciones(), 'estados' => EstadoRevision::opciones()]);
    }

    public function store(RevisionRequest $request)
    {
        abort_unless($request->user()->tokenCan('revisiones:escribir'), 403);
        $datos = $request->validated();
        $datos['ruta'] ??= Unidad::with('zona')->find($datos['unidad_id'])?->zona?->nombre;
        $datos['tiempo_estancia'] = $this->horas($datos['hora_entrada'], $datos['hora_salida'] ?? null);
        $revision = RevisionOptocontrol::create($datos + ['created_by' => $request->user()->id]);
        BitacoraService::registrar('revision.creada', $revision);

        return (new RevisionResource($revision->load('unidad')))->response()->setStatusCode(201);
    }

    /** Actualizacion rapida desde la app: estado, solucion y hora de salida. */
    public function update(Request $request, RevisionOptocontrol $revision)
    {
        abort_unless($request->user()->tokenCan('revisiones:escribir'), 403);
        $datos = $request->validate([
            'status' => ['required', Rule::enum(EstadoRevision::class)],
            'solucion' => ['nullable', 'string', 'max:3000'],
            'hora_salida' => ['nullable', 'date_format:H:i'],
        ]);
        $datos['tiempo_estancia'] = $this->horas(substr((string) $revision->hora_entrada, 0, 5), $datos['hora_salida'] ?? null);
        $revision->update($datos);
        BitacoraService::registrar('revision.actualizada', $revision);

        return new RevisionResource($revision->load('unidad'));
    }

    private function horas(?string $entrada, ?string $salida): ?float
    {
        if (! $entrada || ! $salida) {
            return null;
        }
        $e = Carbon::createFromFormat('H:i', $entrada);
        $s = Carbon::createFromFormat('H:i', $salida);
        if ($s->lessThan($e)) {
            $s->addDay();
        }

        return round($e->diffInMinutes($s) / 60, 2);
    }
}
