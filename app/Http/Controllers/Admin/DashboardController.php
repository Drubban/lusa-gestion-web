<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use App\Models\Operador;
use App\Models\DocumentoMantenimiento;
use App\Models\DocumentoCapacitacion;
use App\Models\MovimientoDepartamento;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalUnidades = Unidad::count();
        $totalOperadores = Operador::count();
        $totalMantenimientos = DocumentoMantenimiento::count();
        $totalCapacitaciones = DocumentoCapacitacion::count();

        // Movimientos de los últimos 7 días (para gráfico)
        $movimientosPorDia = MovimientoDepartamento::select(
                DB::raw("DATE(fecha_hora) as fecha"),
                DB::raw("COUNT(*) as total")
            )
            ->where('fecha_hora', '>=', now()->subDays(6))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->pluck('total', 'fecha')
            ->toArray(); // ← convertido a array

        // Rellenar fechas faltantes con 0
        $fechas = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->toDateString();
            $fechas[$fecha] = $movimientosPorDia[$fecha] ?? 0;
        }
        // $fechas ya es un array, no una Collection

        // Movimientos por departamento (últimos 30 días)
        $movimientosPorDepto = MovimientoDepartamento::select('departamento_id', DB::raw('COUNT(*) as total'))
            ->where('fecha_hora', '>=', now()->subDays(30))
            ->with('departamento')
            ->groupBy('departamento_id')
            ->get()
            ->map(fn($item) => [
                'nombre' => $item->departamento->nombre,
                'total' => $item->total
            ])
            ->toArray(); // ← convertido a array

        // Documentos de mantenimiento por mes (últimos 6 meses)
        $docPorMes = DocumentoMantenimiento::select(
                DB::raw("DATE_TRUNC('month', fecha) as mes"),
                DB::raw("COUNT(*) as total")
            )
            ->where('fecha', '>=', now()->subMonths(5))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn($item) => [
                'mes' => \Carbon\Carbon::parse($item->mes)->format('M Y'),
                'total' => $item->total
            ])
            ->toArray(); // ← convertido a array

        return view('admin.dashboard.index', compact(
            'totalUnidades', 'totalOperadores', 'totalMantenimientos', 'totalCapacitaciones',
            'fechas', 'movimientosPorDepto', 'docPorMes'
        ));
    }
}