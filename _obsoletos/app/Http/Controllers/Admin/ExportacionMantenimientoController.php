<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgendamientoMantenimiento;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportacionMantenimientoController extends Controller
{
    /**
     * Exportar mantenimientos programables a CSV
     */
    public function exportarCSV(Request $request)
    {
        // Obtener los agendamientos pendientes con sus relaciones
        $agendamientos = AgendamientoMantenimiento::with(['unidad.zona'])
            ->where('estado', 'pendiente')
            ->orderBy('fecha_agendada', 'asc')
            ->get();

        // Nombre del archivo
        $filename = 'mantenimientos_programables_' . date('Y-m-d_H-i-s') . '.csv';

        // Cabeceras del CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Crear el contenido CSV
        $callback = function() use ($agendamientos) {
            $handle = fopen('php://output', 'w');

            // Escribir BOM para UTF-8 (compatibilidad con Excel)
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Cabeceras de columnas
            fputcsv($handle, [
                'UNIDAD',
                'ZONA',
                'FECHA AGENDADA',
                'OBSERVACIONES',
                'FECHA REVISADO'
            ]);

            // Datos
            foreach ($agendamientos as $item) {
                fputcsv($handle, [
                    $item->unidad->numero_economico ?? 'N/A',
                    ucfirst($item->unidad->zona->nombre ?? 'N/A'),
                    $item->fecha_agendada ? $item->fecha_agendada->format('d/m/Y') : 'N/A',
                    $item->observaciones ?? 'Sin observaciones',
                    '', // Campo vacío para fecha de revisado
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exportar mantenimientos programables a Excel (usando HTML table)
     */
    public function exportarExcel(Request $request)
    {
        $agendamientos = AgendamientoMantenimiento::with(['unidad.zona'])
            ->where('estado', 'pendiente')
            ->orderBy('fecha_agendada', 'asc')
            ->get();

        $html = '
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                table {
                    border-collapse: collapse;
                    width: 100%;
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }
                th {
                    background-color: #0d6efd;
                    color: white;
                    padding: 10px;
                    border: 1px solid #ddd;
                    text-align: left;
                }
                td {
                    padding: 8px;
                    border: 1px solid #ddd;
                }
                tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                .title {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 15px;
                    text-align: center;
                }
                .footer {
                    margin-top: 15px;
                    font-size: 10px;
                    color: #666;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="title">LISTADO DE MANTENIMIENTOS PROGRAMABLES</div>
            <p>Fecha de generación: ' . date('d/m/Y H:i:s') . '</p>
            <table>
                <thead>
                    <tr>
                        <th>UNIDAD</th>
                        <th>ZONA</th>
                        <th>FECHA AGENDADA</th>
                        <th>OBSERVACIONES</th>
                        <th>FECHA REVISADO</th>
                    </tr>
                </thead>
                <tbody>';

        if ($agendamientos->isEmpty()) {
            $html .= '<tr><td colspan="5" style="text-align:center; color:#999;">No hay mantenimientos programables</td></tr>';
        } else {
            foreach ($agendamientos as $item) {
                $html .= '<tr>
                    <td>' . ($item->unidad->numero_economico ?? 'N/A') . '</td>
                    <td>' . ucfirst($item->unidad->zona->nombre ?? 'N/A') . '</td>
                    <td>' . ($item->fecha_agendada ? $item->fecha_agendada->format('d/m/Y') : 'N/A') . '</td>
                    <td>' . ($item->observaciones ?? 'Sin observaciones') . '</td>
                    <td></td>
                </tr>';
            }
        }

        $html .= '
                </tbody>
            </table>
            <div class="footer">
                Total de registros: ' . $agendamientos->count() . ' | Generado por Sistema Lusa
            </div>
        </body>
        </html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="mantenimientos_programables_' . date('Y-m-d_H-i-s') . '.xls"',
        ]);
    }

    /**
     * Exportar todos los agendamientos (incluyendo históricos)
     */
    public function exportarTodos(Request $request)
    {
        $agendamientos = AgendamientoMantenimiento::with(['unidad.zona'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'todos_agendamientos_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($agendamientos) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'UNIDAD',
                'ZONA',
                'FECHA AGENDADA',
                'ESTADO',
                'FECHA CUMPLIMIENTO',
                'OBSERVACIONES',
                'REGISTRADO EL'
            ]);

            foreach ($agendamientos as $item) {
                $estadoMap = [
                    'pendiente' => 'Pendiente',
                    'cumplido' => 'Cumplido',
                    'no_cumplido' => 'No cumplido',
                    'reagendado' => 'Reagendado',
                ];

                fputcsv($handle, [
                    $item->unidad->numero_economico ?? 'N/A',
                    ucfirst($item->unidad->zona->nombre ?? 'N/A'),
                    $item->fecha_agendada ? $item->fecha_agendada->format('d/m/Y') : 'N/A',
                    $estadoMap[$item->estado] ?? $item->estado,
                    $item->fecha_cumplimiento ? $item->fecha_cumplimiento->format('d/m/Y') : '',
                    $item->observaciones ?? 'Sin observaciones',
                    $item->created_at ? $item->created_at->format('d/m/Y H:i') : 'N/A',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}