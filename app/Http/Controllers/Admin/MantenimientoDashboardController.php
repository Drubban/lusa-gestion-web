<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use App\Models\DocumentoMantenimiento;
use App\Models\AsignacionOperadorUnidad;
use App\Models\AgendamientoMantenimiento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MantenimientoDashboardController extends Controller
{
    public function index(Request $request)
    {
        $unidades = Unidad::with([
            'zona',
            'asignacionVigente.operador',
            'tecnologias',
            'agendamientoPendiente'
        ])
            ->where('activo', true)
            ->orderBy('numero_economico')
            ->get();

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
            $ultimoMantenimiento = DocumentoMantenimiento::whereHas('asignacion', function ($query) use ($unidad) {
                $query->where('unidad_id', $unidad->id);
            })
                ->orderBy('fecha', 'desc')
                ->orderBy('hora', 'desc')
                ->first();

            $agendamientoPendiente = $unidad->agendamientoPendiente;

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
                        $estado = 'Atención media';
                        $color = 'warning';
                        $atencionMedia++;
                    } elseif ($diasDesde <= 21) {
                        $estado = 'Requiere atención';
                        $color = 'orange';
                        $requiereAtencion++;
                        $urgentes++;
                    } else {
                        $estado = 'Atención urgente';
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
                        $estado = 'Atención media';
                        $color = 'warning';
                        $atencionMedia++;
                    } elseif ($diasDesde <= 21) {
                        $estado = 'Requiere atención';
                        $color = 'orange';
                        $requiereAtencion++;
                        $urgentes++;
                    } else {
                        $estado = 'Atención urgente';
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
                } elseif ($diasRestantes <= 7) {
                    $aTiempo++;
                } else {
                    $aTiempo++;
                }
            }

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
                'agendamiento' => $agendamientoPendiente,
                'fecha_agendada' => $fechaAgendada,
                'dias_restantes' => $diasRestantes,
                'estado_agendamiento' => $estadoAgendamiento,
                'tiene_mantenimiento' => $tieneMantenimiento,
                'selected' => false,
            ];
        }

        $porcentajeConMantenimiento = $totalUnidades > 0 ? round(($conMantenimiento / $totalUnidades) * 100, 1) : 0;
        $porcentajeSinMantenimiento = $totalUnidades > 0 ? round(($sinMantenimiento / $totalUnidades) * 100, 1) : 0;
        $porcentajeVencidos = $totalUnidades > 0 ? round(($vencidos / $totalUnidades) * 100, 1) : 0;
        $porcentajeATiempo = $totalUnidades > 0 ? round(($aTiempo / $totalUnidades) * 100, 1) : 0;
        $porcentajeUrgentes = $totalUnidades > 0 ? round(($urgentes / $totalUnidades) * 100, 1) : 0;

        $estadosData = [
            'Reciente' => $recientes,
            'Atención media' => $atencionMedia,
            'Requiere atención' => $requiereAtencion,
            'Atención urgente' => $urgentes,
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

        return view('admin.mantenimiento.dashboard', compact('dashboard', 'stats', 'estadosData', 'zonasData', 'tendenciaAgendamientos'));
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
                $existente = AgendamientoMantenimiento::where('unidad_id', $unidadId)
                    ->where('estado', 'pendiente')
                    ->first();

                if ($existente) {
                    $omitidos++;
                    continue;
                }

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

            // 🔥 REDIRIGIR EN LUGAR DE RESPONDER JSON
            return redirect()->route('admin.mantenimiento.dashboard')
                ->with('success', $mensaje);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validacion: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error al agendar: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->withErrors(['error' => 'Error al agendar: ' . $e->getMessage()]);
        }
    }
}
