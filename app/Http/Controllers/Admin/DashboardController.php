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

        // Nuevos módulos
        $totalInventario = Inventario::count();
        $totalAjustes = Ajuste::count();
        $totalTecnologias = Tecnologia::count();
        $tecnologiasPorTipo = Tecnologia::select('tipo', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo')
            ->get()
            ->map(fn($item) => [
                'tipo' => $item->tipo,
                'total' => $item->total
            ])
            ->toArray();
        $totalBarras = Barra::count();
        $totalTelpos = Telpo::count();
        $totalGps = Gps::count();
        $totalMdvr = Mdvr::count();

        // Asignaciones vigentes
        $asignacionesVigentes = AsignacionOperadorUnidad::where('vigente', true)->count();

        // ============================================================
        // 2. MOVIMIENTOS ULTIMOS 7 DIAS (grafico de lineas)
        // ============================================================
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

        // ============================================================
        // 3. MOVIMIENTOS POR DEPARTAMENTO (grafico de dona)
        // ============================================================
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

        // ============================================================
        // 4. DOCUMENTOS DE MANTENIMIENTO POR MES (grafico de barras)
        // ============================================================
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

        // ============================================================
        // 5. INVENTARIO POR CATEGORIA (grafico de barras o dona)
        // ============================================================
        $inventarioPorCategoria = Inventario::select('categoria', DB::raw('COUNT(*) as total'))
            ->groupBy('categoria')
            ->get()
            ->map(fn($item) => [
                'categoria' => $item->categoria,
                'total' => $item->total
            ])
            ->toArray();

        // ============================================================
        // 6. TECNOLOGIAS POR TIPO (grafico de dona)
        // ============================================================
        $tecnologiasPorTipo = Tecnologia::select('tipo', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo')
            ->get()
            ->map(fn($item) => [
                'tipo' => $item->tipo,
                'total' => $item->total
            ])
            ->toArray();

        // ============================================================
        // 7. AJUSTES POR MES (grafico de barras)
        // ============================================================
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

        // ============================================================
        // 8. REGISTROS RECIENTES
        // ============================================================
        $ultimosMantenimientos = DocumentoMantenimiento::with(['asignacion.unidad', 'asignacion.operador'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ultimasCapacitaciones = DocumentoCapacitacion::with(['asignacion.unidad', 'asignacion.operador'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ultimosInventarios = Inventario::with(['departamento', 'zona', 'creador'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ultimosAjustes = Ajuste::with(['operador', 'unidad', 'creador'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ============================================================
        // 9. INDICADORES ADICIONALES
        // ============================================================
        $unidadesSinAsignar = Unidad::whereDoesntHave('asignaciones', function ($query) {
            $query->where('vigente', true);
        })->count();

        $unidadesConTelpo = Tecnologia::where('tipo', 'telpo')->count();
        $unidadesConGps = Tecnologia::where('tipo', 'gps')->count();
        $unidadesConBarras = Tecnologia::where('tipo', 'barras')->count();
        $unidadesConMdvr = Tecnologia::where('tipo', 'mdvr')->count();

        // ============================================================
        // 10. EQUIPOS ASIGNADOS (ET, EG, EB)
        // ============================================================
        $unidadesConET = Unidad::whereNotNull('equipo_telpo')->where('equipo_telpo', '!=', '')->count();
        $unidadesConEG = Unidad::whereNotNull('equipo_gps')->where('equipo_gps', '!=', '')->count();
        $unidadesConEB = Unidad::whereNotNull('equipo_barras')->where('equipo_barras', '!=', '')->count();

        // ============================================================
        // 11. MONTO TOTAL DE AJUSTES
        // ============================================================
        $montoTotalAjustes = Ajuste::sum('monto_total');

        // ============================================================
        // 12. INVENTARIO POR DEPARTAMENTO
        // ============================================================
        $inventarioPorDepto = Inventario::select('departamento_id', DB::raw('COUNT(*) as total'))
            ->with('departamento')
            ->groupBy('departamento_id')
            ->get()
            ->map(fn($item) => [
                'nombre' => $item->departamento->nombre ?? 'Sin departamento',
                'total' => $item->total
            ])
            ->toArray();

        return view('admin.dashboard.index', compact(
            // Estadisticas generales
            'totalUnidades',
            'totalOperadores',
            'totalMantenimientos',
            'totalCapacitaciones',
            'totalInventario',
            'totalAjustes',
            'totalTecnologias',
            'totalBarras',
            'totalTelpos',
            'totalGps',
            'totalMdvr',
            'asignacionesVigentes',

            // Graficos
            'fechas',
            'movimientosPorDepto',
            'docPorMes',
            'inventarioPorCategoria',
            'tecnologiasPorTipo',
            'ajustesPorMes',

            // Registros recientes
            'ultimosMantenimientos',
            'ultimasCapacitaciones',
            'ultimosInventarios',
            'ultimosAjustes',

            // Indicadores
            'unidadesSinAsignar',
            'unidadesConTelpo',
            'unidadesConGps',
            'unidadesConBarras',
            'unidadesConMdvr',
            'unidadesConET',
            'unidadesConEG',
            'unidadesConEB',
            'montoTotalAjustes',
            'inventarioPorDepto'
        ));
    }
}
