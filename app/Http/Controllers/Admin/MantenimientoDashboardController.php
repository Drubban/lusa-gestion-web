<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use App\Models\DocumentoMantenimiento;
use App\Models\AsignacionOperadorUnidad;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MantenimientoDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Obtener todas las unidades activas con su asignacion vigente
        $unidades = Unidad::with(['zona', 'asignacionVigente.operador'])
            ->where('activo', true)
            ->orderBy('numero_economico')
            ->get();

        $dashboard = [];

        foreach ($unidades as $unidad) {
            // Obtener el ultimo mantenimiento de la unidad
            $ultimoMantenimiento = DocumentoMantenimiento::whereHas('asignacion', function ($query) use ($unidad) {
                $query->where('unidad_id', $unidad->id);
            })
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->first();

            // Calcular dias desde el ultimo mantenimiento
            $diasDesde = null;
            $estado = 'Sin mantenimiento';
            $color = 'secondary';
            $proximoMantenimiento = null;

            if ($ultimoMantenimiento) {
                // 🔥 CORREGIDO: Parsear correctamente fecha y hora por separado
                try {
                    // Crear fecha combinando fecha y hora
                    $fechaStr = $ultimoMantenimiento->fecha . ' ' . $ultimoMantenimiento->hora;
                    $fechaUltimo = Carbon::parse($fechaStr);
                    
                    $diasDesde = $fechaUltimo->diffInDays(now());

                    // Determinar estado segun los dias transcurridos
                    if ($diasDesde <= 7) {
                        $estado = 'Reciente';
                        $color = 'success';
                    } elseif ($diasDesde <= 14) {
                        $estado = 'Atencion media';
                        $color = 'warning';
                    } elseif ($diasDesde <= 21) {
                        $estado = 'Requiere atencion';
                        $color = 'orange';
                    } else {
                        $estado = 'Atencion urgente';
                        $color = 'danger';
                    }

                    // Calcular proximo mantenimiento (3 semanas desde el ultimo)
                    $proximoMantenimiento = $fechaUltimo->addDays(21)->format('d/m/Y');
                } catch (\Exception $e) {
                    // Si falla el parseo, usar la fecha sola
                    $fechaUltimo = Carbon::parse($ultimoMantenimiento->fecha);
                    $diasDesde = $fechaUltimo->diffInDays(now());
                    
                    if ($diasDesde <= 7) {
                        $estado = 'Reciente';
                        $color = 'success';
                    } elseif ($diasDesde <= 14) {
                        $estado = 'Atencion media';
                        $color = 'warning';
                    } elseif ($diasDesde <= 21) {
                        $estado = 'Requiere atencion';
                        $color = 'orange';
                    } else {
                        $estado = 'Atencion urgente';
                        $color = 'danger';
                    }
                    
                    $proximoMantenimiento = Carbon::parse($ultimoMantenimiento->fecha)->addDays(21)->format('d/m/Y');
                }
            }

            // Tecnologias de la unidad
            $tecnologias = $unidad->tecnologias->pluck('tipo')->toArray();

            $dashboard[] = [
                'unidad' => $unidad,
                'ultimo_mantenimiento' => $ultimoMantenimiento,
                'dias_desde' => $diasDesde,
                'estado' => $estado,
                'color' => $color,
                'proximo_mantenimiento' => $proximoMantenimiento,
                'tecnologias' => $tecnologias,
                'operador' => $unidad->asignacionVigente->operador ?? null,
            ];
        }

        // Estadisticas resumen
        $stats = [
            'total_unidades' => $unidades->count(),
            'sin_mantenimiento' => collect($dashboard)->filter(fn($d) => $d['ultimo_mantenimiento'] === null)->count(),
            'atencion_urgente' => collect($dashboard)->filter(fn($d) => $d['color'] === 'danger')->count(),
            'recientes' => collect($dashboard)->filter(fn($d) => $d['color'] === 'success')->count(),
        ];

        return view('admin.mantenimiento.dashboard', compact('dashboard', 'stats'));
    }

    public function show($id)
    {
        $unidad = Unidad::with(['zona', 'asignacionVigente.operador', 'tecnologias'])
            ->findOrFail($id);

        $mantenimientos = DocumentoMantenimiento::whereHas('asignacion', function ($query) use ($id) {
            $query->where('unidad_id', $id);
        })
        ->orderBy('fecha', 'desc')
        ->orderBy('hora', 'desc')
        ->get();

        return view('admin.mantenimiento.detalle', compact('unidad', 'mantenimientos'));
    }
}