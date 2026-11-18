<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgendamientoMantenimiento;
use App\Models\MovimientoDepartamento;
use App\Models\RevisionOptocontrol;
use App\Services\EstadoMantenimientoService;
use Illuminate\Http\Request;

/** Indicadores para la pantalla de inicio de la app. */
class ResumenController extends Controller
{
    public function __invoke(Request $request, EstadoMantenimientoService $estado)
    {
        $usuario = $request->user();
        $departamentoId = $usuario->departamento_id;

        $datos = [
            'dentro_departamento' => MovimientoDepartamento::estanciasAbiertas()->when($departamentoId, fn ($q, $d) => $q->where('departamento_id', $d))->count(),
            'movimientos_hoy' => MovimientoDepartamento::whereDate('fecha_hora', today())->when($departamentoId, fn ($q, $d) => $q->where('departamento_id', $d))->count(),
            'agendados_hoy' => AgendamientoMantenimiento::pendientes()->whereDate('fecha_agendada', today())->count(),
            'agendados_vencidos' => AgendamientoMantenimiento::pendientes()->whereDate('fecha_agendada', '<', today())->count(),
            'revisiones_abiertas' => RevisionOptocontrol::whereIn('status', ['pendiente', 'en_proceso'])->count(),
        ];

        if ($usuario->tokenCan('resumen:leer')) {
            $datos['mantenimiento'] = $estado->resumen();
        }

        return response()->json($datos);
    }
}
