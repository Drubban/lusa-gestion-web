<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgendamientoMantenimiento;
use App\Models\DocumentoCapacitacion;
use App\Models\DocumentoMantenimiento;
use App\Models\Inventario;
use App\Models\MovimientoDepartamento;
use App\Models\Operador;
use App\Models\RevisionOptocontrol;
use App\Models\Tecnologia;
use App\Models\Unidad;
use App\Services\EstadoMantenimientoService;
use Illuminate\Support\Facades\DB;

/**
 * Inicio del panel: indicadores clave y actividad reciente.
 * Se redujo de ~50 variables y una consulta por unidad a pocas consultas agregadas.
 */
class DashboardController extends Controller
{
    public function __invoke(EstadoMantenimientoService $estado)
    {
        $resumenMto = $estado->resumen();

        $movimientos7 = MovimientoDepartamento::where('fecha_hora', '>=', today()->subDays(6))
            ->selectRaw('DATE(fecha_hora) as dia, COUNT(*) as total')
            ->groupBy('dia')->pluck('total', 'dia');
        $serieMovimientos = collect(range(6, 0))->mapWithKeys(function ($i) use ($movimientos7) {
            $dia = today()->subDays($i)->toDateString();

            return [today()->subDays($i)->format('d/m') => (int) ($movimientos7[$dia] ?? 0)];
        });

        $porDepartamento = MovimientoDepartamento::join('departamentos', 'departamentos.id', '=', 'movimiento_departamento.departamento_id')
            ->where('fecha_hora', '>=', now()->subDays(30))
            ->selectRaw('departamentos.nombre, COUNT(*) as total, ROUND(AVG(duracion_minutos)) as promedio')
            ->groupBy('departamentos.nombre')->orderByDesc('total')->get();

        return view('admin.dashboard.index', [
            'kpis' => [
                'unidades' => Unidad::where('activo', true)->count(),
                'operadores' => Operador::where('activo', true)->count(),
                'dentro' => MovimientoDepartamento::estanciasAbiertas()->count(),
                'agendados_hoy' => AgendamientoMantenimiento::pendientes()->whereDate('fecha_agendada', today())->count(),
                'vencidos' => AgendamientoMantenimiento::pendientes()->whereDate('fecha_agendada', '<', today())->count(),
                'revisiones_abiertas' => RevisionOptocontrol::whereIn('status', ['pendiente', 'en_proceso'])->count(),
                'mantenimientos_mes' => DocumentoMantenimiento::where('fecha', '>=', today()->startOfMonth())->count(),
                'capacitaciones_mes' => DocumentoCapacitacion::where('fecha', '>=', today()->startOfMonth())->count(),
                'inventario_mes' => Inventario::where('fecha_entrega', '>=', today()->startOfMonth())->count(),
            ],
            'resumenMto' => $resumenMto,
            'serieMovimientos' => $serieMovimientos,
            'porDepartamento' => $porDepartamento,
            'tecnologias' => Tecnologia::selectRaw('tipo, COUNT(*) as total')->groupBy('tipo')->pluck('total', 'tipo'),
            'ultimosMovimientos' => MovimientoDepartamento::with(['unidad', 'departamento', 'usuario'])->latest('fecha_hora')->limit(8)->get(),
            'proximos' => AgendamientoMantenimiento::with('unidad')->pendientes()->orderBy('fecha_agendada')->limit(6)->get(),
        ]);
    }
}
