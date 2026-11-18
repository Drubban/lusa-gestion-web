<?php

namespace App\Http\Controllers\Api;

use App\Enums\EstadoAgendamiento;
use App\Enums\MotivoNoPresentado;
use App\Exceptions\ReglaNegocioException;
use App\Http\Controllers\Controller;
use App\Http\Requests\NoPresentadoRequest;
use App\Http\Resources\AgendamientoResource;
use App\Models\AgendamientoMantenimiento;
use App\Services\AgendamientoService;
use Illuminate\Http\Request;

class AgendamientoController extends Controller
{
    public function __construct(private readonly AgendamientoService $servicio) {}

    /** vista: hoy | proximos | vencidos */
    public function index(Request $request)
    {
        $vista = $request->query('vista', 'hoy');
        $query = AgendamientoMantenimiento::with(['unidad.zona', 'unidad.asignacionVigente.operador'])->pendientes();

        match ($vista) {
            'vencidos' => $query->whereDate('fecha_agendada', '<', today()),
            'proximos' => $query->whereDate('fecha_agendada', '>', today())->whereDate('fecha_agendada', '<=', today()->addDays(14)),
            default => $query->whereDate('fecha_agendada', today()),
        };

        return AgendamientoResource::collection($query->orderBy('fecha_agendada')->limit(300)->get());
    }

    public function cumplir(Request $request, AgendamientoMantenimiento $agendamiento)
    {
        $this->validarAcceso($request, $agendamiento);
        $this->servicio->marcarCumplido($agendamiento, 'Marcado desde la app');

        return new AgendamientoResource($agendamiento->fresh('unidad'));
    }

    public function noPresentado(NoPresentadoRequest $request, AgendamientoMantenimiento $agendamiento)
    {
        $this->validarAcceso($request, $agendamiento);
        $this->servicio->reportarNoPresentado(
            $agendamiento,
            MotivoNoPresentado::from($request->validated('motivo')),
            $request->validated('fecha_reprogramada'),
            $request->validated('observaciones'),
        );

        return new AgendamientoResource($agendamiento->fresh('unidad'));
    }

    public function motivos()
    {
        return response()->json(MotivoNoPresentado::opciones());
    }

    private function validarAcceso(Request $request, AgendamientoMantenimiento $agendamiento): void
    {
        abort_unless($request->user()->tokenCan('agendamientos:escribir'), 403, 'No tienes permiso para modificar agendamientos.');
        if ($agendamiento->estado !== EstadoAgendamiento::Pendiente) {
            throw new ReglaNegocioException('El agendamiento ya no esta pendiente.');
        }
    }
}
