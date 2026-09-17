<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use App\Models\DocumentoMantenimiento;
use App\Models\AsignacionOperadorUnidad;
use App\Models\AgendamientoMantenimiento;
use App\Models\Tecnologia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MantenimientoDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Aplicar filtros
        $query = Unidad::with([
            'zona',
            'asignacionVigente.operador',
            'tecnologias'
        ])->where('activo', true);

        // Filtro por zona
        if ($request->filled('zona')) {
            $query->whereHas('zona', function ($q) use ($request) {
                $q->where('nombre', $request->zona);
            });
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_economico', 'LIKE', "%{$search}%")
                    ->orWhere('nombre_unidad', 'LIKE', "%{$search}%")
                    ->orWhereHas('asignacionVigente.operador', function ($q2) use ($search) {
                        $q2->where('nombre_completo', 'LIKE', "%{$search}%");
                    });
            });
        }

        $unidades = $query->orderBy('numero_economico')->get();
        $unidadIds = $unidades->pluck('id')->toArray();

        // Ultimos mantenimientos
        $ultimosMantenimientos = DocumentoMantenimiento::whereHas('asignacion', function ($q) use ($unidadIds) {
            $q->whereIn('unidad_id', $unidadIds);
        })
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get()
            ->groupBy('asignacion.unidad_id')
            ->map(function ($group) {
                return $group->first();
            });

        // Agendamientos pendientes
        $agendamientosPendientes = AgendamientoMantenimiento::whereIn('unidad_id', $unidadIds)
            ->where('estado', 'pendiente')
            ->get()
            ->keyBy('unidad_id');

        // Tecnologias
        $tecnologiasPorUnidad = Tecnologia::whereIn('unidad_id', $unidadIds)
            ->get()
            ->groupBy('unidad_id')
            ->map(function ($group) {
                return $group->pluck('tipo')->toArray();
            });

        // Procesar datos
        $dashboard = [];
        $totalUnidades = $unidades->count();
        $conMantenimiento = 0;
        $sinMantenimiento = 0;
        $conAgendamiento = 0;
        $vencidos = 0;
        $aTiempo = 0;
        $urgentes = 0;
        $recientes = 0;
        $atencionMedia = 0;
        $requiereAtencion = 0;

        foreach ($unidades as $unidad) {
            $ultimoMantenimiento = $ultimosMantenimientos[$unidad->id] ?? null;
            $agendamientoPendiente = $agendamientosPendientes[$unidad->id] ?? null;
            $tecnologias = $tecnologiasPorUnidad[$unidad->id] ?? [];

            $diasDesde = null;
            $estado = 'Sin mantenimiento';
            $color = 'secondary';
            $proximoMantenimiento = null;
            $fechaAgendada = null;
            $estadoAgendamiento = null;
            $diasRestantes = null;
            $tieneMantenimiento = false;

            if ($ultimoMantenimiento) {
                $tieneMantenimiento = true;
                $conMantenimiento++;
                try {
                    $fechaUltimo = Carbon::parse($ultimoMantenimiento->fecha . ' ' . $ultimoMantenimiento->hora);
                    $diasDesde = $fechaUltimo->diffInDays(now());

                    if ($diasDesde <= 7) {
                        $estado = 'Reciente';
                        $color = 'success';
                        $recientes++;
                    } elseif ($diasDesde <= 14) {
                        $estado = 'Atencion media';
                        $color = 'warning';
                        $atencionMedia++;
                    } elseif ($diasDesde <= 21) {
                        $estado = 'Requiere atencion';
                        $color = 'orange';
                        $requiereAtencion++;
                        $urgentes++;
                    } else {
                        $estado = 'Atencion urgente';
                        $color = 'danger';
                        $urgentes++;
                    }

                    $proximoMantenimiento = $fechaUltimo->addDays(21)->format('d/m/Y');
                } catch (\Exception $e) {
                    $fechaUltimo = Carbon::parse($ultimoMantenimiento->fecha);
                    $diasDesde = $fechaUltimo->diffInDays(now());

                    if ($diasDesde <= 7) {
                        $estado = 'Reciente';
                        $color = 'success';
                        $recientes++;
                    } elseif ($diasDesde <= 14) {
                        $estado = 'Atencion media';
                        $color = 'warning';
                        $atencionMedia++;
                    } elseif ($diasDesde <= 21) {
                        $estado = 'Requiere atencion';
                        $color = 'orange';
                        $requiereAtencion++;
                        $urgentes++;
                    } else {
                        $estado = 'Atencion urgente';
                        $color = 'danger';
                        $urgentes++;
                    }

                    $proximoMantenimiento = Carbon::parse($ultimoMantenimiento->fecha)->addDays(21)->format('d/m/Y');
                }
            } else {
                $sinMantenimiento++;
            }

            if ($agendamientoPendiente) {
                $conAgendamiento++;
                $fechaAgendada = $agendamientoPendiente->fecha_agendada;
                $diasRestantes = now()->diffInDays($fechaAgendada, false);
                $estadoAgendamiento = $agendamientoPendiente->estado;

                if ($diasRestantes < 0) {
                    $vencidos++;
                } else {
                    $aTiempo++;
                }
            }

            $dashboard[] = [
                'unidad' => $unidad,
                'ultimo_mantenimiento' => $ultimoMantenimiento,
                'dias_desde' => $diasDesde,
                'estado' => $estado,
                'color' => $color,
                'proximo_mantenimiento' => $proximoMantenimiento,
                'tecnologias' => $tecnologias,
                'operador' => $unidad->asignacionVigente->operador ?? null,
                'agendamiento' => $agendamientoPendiente,
                'fecha_agendada' => $fechaAgendada,
                'dias_restantes' => $diasRestantes,
                'estado_agendamiento' => $estadoAgendamiento,
                'tiene_mantenimiento' => $tieneMantenimiento,
                'selected' => false,
            ];
        }

        // Aplicar filtro de agendamiento
        if ($request->filled('agendamiento')) {
            if ($request->agendamiento === 'con') {
                $dashboard = array_filter($dashboard, fn($item) => $item['agendamiento'] !== null);
            } elseif ($request->agendamiento === 'sin') {
                $dashboard = array_filter($dashboard, fn($item) => $item['agendamiento'] === null);
            }
        }

        // Aplicar filtro de estado
        if ($request->filled('estado') && $request->estado !== '') {
            $estadoFiltro = $request->estado;
            $dashboard = array_filter($dashboard, fn($item) => $item['estado'] === $estadoFiltro);
        }

        $dashboard = array_values($dashboard);

        // ============================================================
        // UNIDADES NO PRESENTADAS - CON FILTRO DE FECHAS
        // ============================================================
        // Rango de fechas para el reporte de no presentadas
        $fechaNoPresentadasDesde = $request->filled('np_desde')
            ? Carbon::parse($request->np_desde)->startOfDay()
            : Carbon::today()->startOfDay();

        $fechaNoPresentadasHasta = $request->filled('np_hasta')
            ? Carbon::parse($request->np_hasta)->endOfDay()
            : Carbon::today()->endOfDay();

        // Modo de vista: 'rango' o 'especifico'
        $modoNoPresentadas = $request->get('np_modo', 'hoy');

        // Consulta de agendamientos no cumplidos en el rango seleccionado
        $noPresentadasQuery = AgendamientoMantenimiento::with(['unidad.zona', 'unidad.asignacionVigente.operador'])
            ->where(function ($q) use ($fechaNoPresentadasDesde, $fechaNoPresentadasHasta, $modoNoPresentadas) {

                if ($modoNoPresentadas === 'hoy') {
                    // Solo HOY y que aun no se hayan revisado
                    $q->whereDate('fecha_agendada', Carbon::today());
                } elseif ($modoNoPresentadas === 'rango') {
                    // Rango de fechas
                    $q->whereDate('fecha_agendada', '>=', $fechaNoPresentadasDesde)
                        ->whereDate('fecha_agendada', '<=', $fechaNoPresentadasHasta);
                } elseif ($modoNoPresentadas === 'vencidos') {
                    // Solo vencidos (fecha agendada < hoy)
                    $q->whereDate('fecha_agendada', '<', Carbon::today());
                } else {
                    $q->whereDate('fecha_agendada', Carbon::today());
                }
            })
            ->whereIn('estado', ['pendiente', 'no_cumplido']);

        // Si es "hoy", excluir las que ya se presentaron (Reciente)
        if ($modoNoPresentadas === 'hoy') {
            $unidadesRecientes = collect($dashboard)
                ->filter(fn($item) => $item['estado'] === 'Reciente')
                ->pluck('unidad.id')
                ->toArray();

            if (!empty($unidadesRecientes)) {
                $noPresentadasQuery->whereNotIn('unidad_id', $unidadesRecientes);
            }
        }

        $noPresentadas = $noPresentadasQuery->orderBy('fecha_agendada', 'desc')->get();

        // Contadores
        $totalNoPresentadasHoy = $noPresentadas->filter(function ($item) {
            return $item->fecha_agendada && Carbon::parse($item->fecha_agendada)->isToday();
        })->count();

        $totalNoPresentadasVencidas = $noPresentadas->filter(function ($item) {
            return $item->fecha_agendada && Carbon::parse($item->fecha_agendada)->isPast()
                && !Carbon::parse($item->fecha_agendada)->isToday();
        })->count();

        $totalNoPresentadas = $noPresentadas->count();

        // Estadísticas
        $totalDashboard = count($dashboard);
        $porcentajeConMantenimiento = $totalUnidades > 0 ? round(($conMantenimiento / $totalUnidades) * 100, 1) : 0;
        $porcentajeSinMantenimiento = $totalUnidades > 0 ? round(($sinMantenimiento / $totalUnidades) * 100, 1) : 0;
        $porcentajeVencidos = $totalUnidades > 0 ? round(($vencidos / $totalUnidades) * 100, 1) : 0;
        $porcentajeATiempo = $totalUnidades > 0 ? round(($aTiempo / $totalUnidades) * 100, 1) : 0;
        $porcentajeUrgentes = $totalUnidades > 0 ? round(($urgentes / $totalUnidades) * 100, 1) : 0;

        $estadosData = [
            'Reciente' => $recientes,
            'Atencion media' => $atencionMedia,
            'Requiere atencion' => $requiereAtencion,
            'Atencion urgente' => $urgentes,
            'Sin mantenimiento' => $sinMantenimiento,
        ];

        $zonasData = Unidad::where('activo', true)
            ->select('zona_id', DB::raw('count(*) as total'))
            ->groupBy('zona_id')
            ->with('zona')
            ->get()
            ->map(fn($item) => [
                'zona' => $item->zona->nombre ?? 'Sin zona',
                'total' => $item->total
            ])
            ->toArray();

        $tendenciaAgendamientos = AgendamientoMantenimiento::where('created_at', '>=', now()->subDays(6))
            ->select(DB::raw("DATE(created_at) as fecha"), DB::raw("COUNT(*) as total"))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->map(fn($item) => [
                'fecha' => Carbon::parse($item->fecha)->format('d/m'),
                'total' => $item->total
            ])
            ->toArray();

        $stats = [
            'total_unidades' => $totalUnidades,
            'con_mantenimiento' => $conMantenimiento,
            'sin_mantenimiento' => $sinMantenimiento,
            'con_agendamiento' => $conAgendamiento,
            'vencidos' => $vencidos,
            'a_tiempo' => $aTiempo,
            'urgentes' => $urgentes,
            'recientes' => $recientes,
            'porcentaje_con_mantenimiento' => $porcentajeConMantenimiento,
            'porcentaje_sin_mantenimiento' => $porcentajeSinMantenimiento,
            'porcentaje_vencidos' => $porcentajeVencidos,
            'porcentaje_a_tiempo' => $porcentajeATiempo,
            'porcentaje_urgentes' => $porcentajeUrgentes,
        ];

        return view('admin.mantenimiento.dashboard', compact(
            'dashboard',
            'stats',
            'estadosData',
            'zonasData',
            'tendenciaAgendamientos',
            'noPresentadas',
            'totalNoPresentadasHoy',
            'totalNoPresentadasVencidas',
            'totalNoPresentadas',
            'fechaNoPresentadasDesde',
            'fechaNoPresentadasHasta',
            'modoNoPresentadas'
        ));
    }

    public function show($id)
    {
        $unidad = Unidad::with([
            'zona',
            'asignacionVigente.operador',
            'tecnologias',
            'agendamientos' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])
            ->findOrFail($id);

        $mantenimientos = DocumentoMantenimiento::whereHas('asignacion', function ($query) use ($id) {
            $query->where('unidad_id', $id);
        })
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        return view('admin.mantenimiento.detalle', compact('unidad', 'mantenimientos'));
    }

    public function agendarMasivo(Request $request)
    {
        try {
            Log::info('=== AGENDAMIENTO MASIVO ===');
            Log::info('Datos recibidos:', $request->all());

            $validated = $request->validate([
                'unidades' => 'required|array',
                'unidades.*' => 'exists:unidades,id',
                'fecha_agendada' => 'required|date|after_or_equal:today',
                'observaciones' => 'nullable|string',
            ]);

            $agendados = 0;
            $omitidos = 0;

            foreach ($validated['unidades'] as $unidadId) {
                // Verificar si tiene agendamiento pendiente
                $existente = AgendamientoMantenimiento::where('unidad_id', $unidadId)
                    ->where('estado', 'pendiente')
                    ->first();

                if ($existente) {
                    // Verificar si la fecha del agendamiento ya pasó
                    if ($existente->fecha_agendada < now()->toDateString()) {
                        // Si la fecha ya pasó, actualizar a no_cumplido y crear nuevo
                        $existente->update([
                            'estado' => 'no_cumplido',
                            'observaciones' => ($existente->observaciones ?? '') . ' - Vencido automáticamente',
                            'reportado_por' => Auth::id(),
                        ]);

                        // Crear nuevo agendamiento
                        AgendamientoMantenimiento::create([
                            'unidad_id' => $unidadId,
                            'fecha_agendada' => $validated['fecha_agendada'],
                            'estado' => 'pendiente',
                            'observaciones' => $validated['observaciones'] ?? 'Reagendado automáticamente',
                            'created_by' => Auth::id(),
                        ]);

                        $agendados++;
                        Log::info("Unidad {$unidadId} tenía agendamiento vencido, se reagendó automáticamente");
                    } else {
                        $omitidos++;
                        Log::info("Unidad {$unidadId} ya tiene agendamiento pendiente para {$existente->fecha_agendada}");
                    }
                    continue;
                }

                // Verificar si tiene agendamiento no_cumplido (vencido)
                $vencido = AgendamientoMantenimiento::where('unidad_id', $unidadId)
                    ->where('estado', 'no_cumplido')
                    ->first();

                if ($vencido) {
                    // Si tiene no_cumplido y tiene fecha reprogramada, usar esa
                    if ($vencido->fecha_reprogramada) {
                        AgendamientoMantenimiento::create([
                            'unidad_id' => $unidadId,
                            'fecha_agendada' => $vencido->fecha_reprogramada,
                            'estado' => 'pendiente',
                            'observaciones' => 'Reprogramado desde estado no_cumplido',
                            'created_by' => Auth::id(),
                        ]);

                        $vencido->update(['estado' => 'reagendado']);
                        $agendados++;
                    } else {
                        // Crear nuevo agendamiento normal
                        AgendamientoMantenimiento::create([
                            'unidad_id' => $unidadId,
                            'fecha_agendada' => $validated['fecha_agendada'],
                            'estado' => 'pendiente',
                            'observaciones' => $validated['observaciones'] ?? 'Agendamiento masivo',
                            'created_by' => Auth::id(),
                        ]);
                        $agendados++;
                    }
                    continue;
                }

                // Crear nuevo agendamiento
                AgendamientoMantenimiento::create([
                    'unidad_id' => $unidadId,
                    'fecha_agendada' => $validated['fecha_agendada'],
                    'estado' => 'pendiente',
                    'observaciones' => $validated['observaciones'] ?? 'Agendamiento masivo',
                    'created_by' => Auth::id(),
                ]);

                $agendados++;
            }

            $mensaje = "Se agendaron {$agendados} unidades para el dia " . Carbon::parse($validated['fecha_agendada'])->format('d/m/Y');
            if ($omitidos > 0) {
                $mensaje .= ". {$omitidos} unidades ya tenian agendamiento pendiente y fueron omitidas.";
            }

            Log::info('Resultado: ' . $mensaje);

            return redirect()->route('admin.mantenimiento.dashboard')
                ->with('success', $mensaje);
        } catch (\Exception $e) {
            Log::error('Error al agendar: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->withErrors(['error' => 'Error al agendar: ' . $e->getMessage()]);
        }
    }
}
