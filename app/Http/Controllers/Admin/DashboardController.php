<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use App\Models\Operador;
use App\Models\DocumentoMantenimiento;
use App\Models\DocumentoCapacitacion;
use App\Models\MovimientoDepartamento;
use App\Models\Inventario;
use App\Models\Ajuste;
use App\Models\Tecnologia;
use App\Models\Barra;
use App\Models\Telpo;
use App\Models\Gps;
use App\Models\Mdvr;
use App\Models\AsignacionOperadorUnidad;
use App\Models\AgendamientoMantenimiento;
use App\Models\UsuarioDepartamento;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ============================================================
        // 1. ESTADISTICAS GENERALES
        // ============================================================
        $totalUnidades = Unidad::count();
        $totalOperadores = Operador::count();
        $totalMantenimientos = DocumentoMantenimiento::count();
        $totalCapacitaciones = DocumentoCapacitacion::count();
        $totalInventario = Inventario::count();
        $totalAjustes = Ajuste::count();
        $totalTecnologias = Tecnologia::count();
        $asignacionesVigentes = AsignacionOperadorUnidad::where('vigente', true)->count();

        // ============================================================
        // 2. TECNOLOGIAS POR TIPO
        // ============================================================
        $tecnologiasPorTipo = Tecnologia::select('tipo', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo')
            ->get()
            ->pluck('total', 'tipo')
            ->toArray();

        $unidadesConBarras = $tecnologiasPorTipo['barras'] ?? 0;
        $unidadesConTelpo = $tecnologiasPorTipo['telpo'] ?? 0;
        $unidadesConGps = $tecnologiasPorTipo['gps'] ?? 0;
        $unidadesConMdvr = $tecnologiasPorTipo['mdvr'] ?? 0;

        // ============================================================
        // 3. INDICADORES DE MANTENIMIENTO
        // ============================================================
        $unidadesConMantenimientoReciente = DocumentoMantenimiento::where('fecha', '>=', now()->subDays(30))
            ->distinct('asignacion_id')
            ->count('asignacion_id');

        $unidadesSinMantenimiento = Unidad::whereDoesntHave('asignaciones', function($q) {
            $q->whereHas('documentosMantenimiento');
        })->count();

        // Promedio de días entre mantenimientos
        $promedioDiasMantenimiento = null;
        $documentos = DocumentoMantenimiento::with('asignacion')
            ->orderBy('fecha', 'asc')
            ->get()
            ->groupBy('asignacion_id');

        $diasPromedioPorAsignacion = [];
        foreach ($documentos as $asignacionId => $grupo) {
            if ($grupo->count() < 2) continue;
            $fechas = $grupo->pluck('fecha')->sort();
            $totalDias = 0;
            $fechaAnterior = null;
            foreach ($fechas as $fecha) {
                if ($fechaAnterior) {
                    $totalDias += Carbon::parse($fecha)->diffInDays($fechaAnterior);
                }
                $fechaAnterior = $fecha;
            }
            $diasPromedioPorAsignacion[] = $totalDias / ($fechas->count() - 1);
        }

        $promedioDiasMantenimiento = !empty($diasPromedioPorAsignacion) 
            ? round(array_sum($diasPromedioPorAsignacion) / count($diasPromedioPorAsignacion), 1) 
            : null;

        // ============================================================
        // 4. INVENTARIO
        // ============================================================
        $inventarioPorCategoria = Inventario::select('categoria', DB::raw('COUNT(*) as total'))
            ->groupBy('categoria')
            ->get()
            ->map(fn($item) => [
                'categoria' => $item->categoria,
                'total' => $item->total
            ])
            ->toArray();

        $inventarioPorDepto = Inventario::select('departamento_id', DB::raw('COUNT(*) as total'))
            ->with('departamento')
            ->groupBy('departamento_id')
            ->get()
            ->map(fn($item) => [
                'nombre' => $item->departamento->nombre ?? 'Sin departamento',
                'total' => $item->total
            ])
            ->toArray();

        // ============================================================
        // 5. AJUSTES
        // ============================================================
        $montoTotalAjustes = Ajuste::sum('monto_total') ?? 0;
        $promedioAjustes = Ajuste::avg('monto_total') ?? 0;
        $ajustesFirmados = Ajuste::where('firmado', true)->count();
        $ajustesPendientes = Ajuste::where('firmado', false)->count();

        $porcentajeAjustesFirmados = ($totalAjustes + $ajustesPendientes) > 0 
            ? round(($ajustesFirmados / ($totalAjustes + $ajustesPendientes)) * 100, 1) 
            : 0;

        // ============================================================
        // 6. AGENDAMIENTOS
        // ============================================================
        $agendamientosPendientes = AgendamientoMantenimiento::where('estado', 'pendiente')
            ->where('fecha_agendada', '>=', now()->toDateString())
            ->count();

        $agendamientosVencidos = AgendamientoMantenimiento::where('estado', 'pendiente')
            ->where('fecha_agendada', '<', now()->toDateString())
            ->count();

        // ============================================================
        // 7. USUARIOS
        // ============================================================
        $totalUsuariosApp = UsuarioDepartamento::count();
        $usuariosActivos = UsuarioDepartamento::where('activo', true)->count();

        // ============================================================
        // 8. PORCENTAJES (Power BI Style)
        // ============================================================
        $porcentajeUnidadesConMantenimiento = $totalUnidades > 0 
            ? round(($unidadesConMantenimientoReciente / $totalUnidades) * 100, 1) 
            : 0;

        $porcentajeSinMantenimiento = $totalUnidades > 0 
            ? round(($unidadesSinMantenimiento / $totalUnidades) * 100, 1) 
            : 0;

        // ============================================================
        // 9. GRAFICOS
        // ============================================================
        // Movimientos últimos 7 días
        $movimientosPorDia = MovimientoDepartamento::select(
                DB::raw("DATE(fecha_hora) as fecha"),
                DB::raw("COUNT(*) as total")
            )
            ->where('fecha_hora', '>=', now()->subDays(6))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha')
            ->toArray();

        $fechas = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->toDateString();
            $fechas[$fecha] = $movimientosPorDia[$fecha] ?? 0;
        }

        // Movimientos por departamento (últimos 30 días)
        $movimientosPorDepto = MovimientoDepartamento::select('departamento_id', DB::raw('COUNT(*) as total'))
            ->where('fecha_hora', '>=', now()->subDays(30))
            ->with('departamento')
            ->groupBy('departamento_id')
            ->get()
            ->map(fn($item) => [
                'nombre' => $item->departamento->nombre ?? 'Sin departamento',
                'total' => $item->total
            ])
            ->toArray();

        // Documentos de mantenimiento por mes
        $docPorMes = DocumentoMantenimiento::select(
                DB::raw("DATE_TRUNC('month', fecha) as mes"),
                DB::raw("COUNT(*) as total")
            )
            ->where('fecha', '>=', now()->subMonths(5))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn($item) => [
                'mes' => Carbon::parse($item->mes)->format('M Y'),
                'total' => $item->total
            ])
            ->toArray();

        // Documentos de capacitación por mes
        $capPorMes = DocumentoCapacitacion::select(
                DB::raw("DATE_TRUNC('month', fecha) as mes"),
                DB::raw("COUNT(*) as total")
            )
            ->where('fecha', '>=', now()->subMonths(5))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn($item) => [
                'mes' => Carbon::parse($item->mes)->format('M Y'),
                'total' => $item->total
            ])
            ->toArray();

        // Ajustes por mes
        $ajustesPorMes = Ajuste::select(
                DB::raw("DATE_TRUNC('month', fecha) as mes"),
                DB::raw("COUNT(*) as total"),
                DB::raw("SUM(monto_total) as monto_total")
            )
            ->where('fecha', '>=', now()->subMonths(5))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn($item) => [
                'mes' => Carbon::parse($item->mes)->format('M Y'),
                'total' => $item->total,
                'monto_total' => $item->monto_total ?? 0
            ])
            ->toArray();

        // Tecnologías por tipo (para gráfico)
        $tecnologiasGrafico = collect($tecnologiasPorTipo)->map(function($total, $tipo) {
            return [
                'tipo' => $tipo,
                'total' => $total
            ];
        })->values()->toArray();

        // ============================================================
        // 10. REGISTROS RECIENTES (TODAS LAS CATEGORÍAS)
        // ============================================================
        $ultimosMantenimientos = DocumentoMantenimiento::with(['asignacion.unidad', 'asignacion.operador'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ultimasCapacitaciones = DocumentoCapacitacion::with(['asignacion.unidad', 'asignacion.operador'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ultimosInventarios = Inventario::with(['departamento', 'creador'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ultimosAjustes = Ajuste::with(['operador', 'unidad', 'creador'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ultimosMovimientos = MovimientoDepartamento::with(['departamento', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ultimosAgendamientos = AgendamientoMantenimiento::with(['unidad', 'creador'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ultimasUnidades = Unidad::with(['zona'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ultimosOperadores = Operador::with(['zona'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ============================================================
        // 11. ESTADOS DE MANTENIMIENTO (para gráfico Power BI)
        // ============================================================
        $estadosMantenimiento = [
            'Reciente (≤7 días)' => 0,
            'Atención media (8-14 días)' => 0,
            'Requiere atención (15-21 días)' => 0,
            'Atención urgente (>21 días)' => 0,
            'Sin mantenimiento' => 0
        ];

        foreach (Unidad::where('activo', true)->get() as $unidad) {
            $ultimo = DocumentoMantenimiento::whereHas('asignacion', function($q) use ($unidad) {
                $q->where('unidad_id', $unidad->id);
            })->orderBy('fecha', 'desc')->first();

            if (!$ultimo) {
                $estadosMantenimiento['Sin mantenimiento']++;
                continue;
            }

            $dias = Carbon::parse($ultimo->fecha)->diffInDays(now());
            if ($dias <= 7) {
                $estadosMantenimiento['Reciente (≤7 días)']++;
            } elseif ($dias <= 14) {
                $estadosMantenimiento['Atención media (8-14 días)']++;
            } elseif ($dias <= 21) {
                $estadosMantenimiento['Requiere atención (15-21 días)']++;
            } else {
                $estadosMantenimiento['Atención urgente (>21 días)']++;
            }
        }

        // ============================================================
        // 12. TENDENCIAS (para gráficos)
        // ============================================================
        $tendenciaMantenimientos = DocumentoMantenimiento::select(
                DB::raw("DATE_TRUNC('week', fecha) as semana"),
                DB::raw("COUNT(*) as total")
            )
            ->where('fecha', '>=', now()->subWeeks(12))
            ->groupBy('semana')
            ->orderBy('semana')
            ->get()
            ->map(fn($item) => [
                'semana' => Carbon::parse($item->semana)->format('d/m'),
                'total' => $item->total
            ])
            ->toArray();

        $tendenciaInventario = Inventario::select(
                DB::raw("DATE_TRUNC('week', created_at) as semana"),
                DB::raw("COUNT(*) as total")
            )
            ->where('created_at', '>=', now()->subWeeks(12))
            ->groupBy('semana')
            ->orderBy('semana')
            ->get()
            ->map(fn($item) => [
                'semana' => Carbon::parse($item->semana)->format('d/m'),
                'total' => $item->total
            ])
            ->toArray();

        return view('admin.dashboard.index', compact(
            // Estadísticas generales
            'totalUnidades',
            'totalOperadores',
            'totalMantenimientos',
            'totalCapacitaciones',
            'totalInventario',
            'totalAjustes',
            'totalTecnologias',
            'asignacionesVigentes',
            'totalUsuariosApp',
            'usuariosActivos',

            // Tecnologías
            'unidadesConBarras',
            'unidadesConTelpo',
            'unidadesConGps',
            'unidadesConMdvr',
            'tecnologiasGrafico',

            // Mantenimiento
            'unidadesConMantenimientoReciente',
            'unidadesSinMantenimiento',
            'promedioDiasMantenimiento',
            'porcentajeUnidadesConMantenimiento',
            'porcentajeSinMantenimiento',
            'estadosMantenimiento',

            // Ajustes
            'montoTotalAjustes',
            'promedioAjustes',
            'ajustesFirmados',
            'ajustesPendientes',
            'porcentajeAjustesFirmados',

            // Agendamientos
            'agendamientosPendientes',
            'agendamientosVencidos',

            // Gráficos
            'fechas',
            'movimientosPorDepto',
            'docPorMes',
            'capPorMes',
            'inventarioPorCategoria',
            'inventarioPorDepto',
            'ajustesPorMes',
            'tendenciaMantenimientos',
            'tendenciaInventario',

            // Registros recientes
            'ultimosMantenimientos',
            'ultimasCapacitaciones',
            'ultimosInventarios',
            'ultimosAjustes',
            'ultimosMovimientos',
            'ultimosAgendamientos',
            'ultimasUnidades',
            'ultimosOperadores'
        ));
    }
}