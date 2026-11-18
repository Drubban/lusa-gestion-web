<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoAgendamiento;
use App\Http\Controllers\Controller;
use App\Models\AgendamientoMantenimiento;
use Illuminate\Http\Request;

/**
 * Exportaciones CSV. Se elimino la exportacion "Excel" que generaba HTML sin escapar (XSS);
 * el CSV con BOM abre correctamente en Excel.
 */
class ExportacionController extends Controller
{
    public function agendamientos(Request $request)
    {
        $soloPendientes = $request->query('alcance', 'pendientes') === 'pendientes';

        $query = AgendamientoMantenimiento::with('unidad.zona')
            ->when($soloPendientes, fn ($q) => $q->where('estado', EstadoAgendamiento::Pendiente))
            ->orderBy('fecha_agendada');

        $nombre = ($soloPendientes ? 'mantenimientos_pendientes_' : 'agendamientos_') . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['UNIDAD', 'ZONA', 'FECHA AGENDADA', 'ESTADO', 'FECHA CUMPLIMIENTO', 'OBSERVACIONES']);
            $query->chunk(500, function ($filas) use ($out) {
                foreach ($filas as $a) {
                    fputcsv($out, array_map([$this, 'seguroCsv'], [
                        $a->unidad?->numero_economico,
                        $a->unidad?->zona?->nombre,
                        $a->fecha_agendada?->format('d/m/Y'),
                        $a->estado?->etiqueta(),
                        $a->fecha_cumplimiento?->format('d/m/Y'),
                        $a->observaciones,
                    ]));
                }
            });
            fclose($out);
        }, $nombre, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Evita inyeccion de formulas al abrir el CSV en Excel. */
    public function seguroCsv(?string $valor): string
    {
        $valor = (string) $valor;

        return preg_match('/^[=+\-@\t\r]/', $valor) ? "'" . $valor : $valor;
    }
}
