<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RevisionOptocontrol;
use App\Models\Unidad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevisionOptocontrolController extends Controller
{
    public function index(Request $request)
    {
        $query = RevisionOptocontrol::with(['unidad', 'creador']);

        if ($request->filled('disp')) {
            $query->where('disp', $request->disp);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('unidad_id')) {
            $query->where('unidad_id', $request->unidad_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_reporte', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_reporte', '<=', $request->fecha_hasta);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('problema', 'ILIKE', "%{$search}%")
                  ->orWhere('solucion', 'ILIKE', "%{$search}%")
                  ->orWhere('nombre_unidad', 'ILIKE', "%{$search}%")
                  ->orWhere('ruta', 'ILIKE', "%{$search}%")
                  ->orWhere('responsable', 'ILIKE', "%{$search}%");
            });
        }

        $revisiones = $query->orderBy('fecha_reporte', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(20)
                            ->withQueryString();

        // Estadisticas para tarjetas superiores
        $stats = [
            'total'      => RevisionOptocontrol::count(),
            'pendientes' => RevisionOptocontrol::where('status', 'pendiente')->count(),
            'resueltos'  => RevisionOptocontrol::where('status', 'resuelto')->count(),
            'mes'        => RevisionOptocontrol::whereMonth('fecha_reporte', now()->month)
                                                ->whereYear('fecha_reporte', now()->year)
                                                ->count(),
        ];

        $unidades = Unidad::where('activo', true)
                          ->orderBy('numero_economico')
                          ->get(['id', 'numero_economico', 'nombre_unidad', 'zona_id']);

        $disp_opciones = RevisionOptocontrol::DISP_OPCIONES;
        $status_opciones = RevisionOptocontrol::STATUS_OPCIONES;

        return view('admin.revisiones-optocontrol.index', compact(
            'revisiones',
            'unidades',
            'disp_opciones',
            'status_opciones',
            'stats'
        ));
    }

    public function create()
    {
        $unidades = Unidad::with('zona')
                          ->where('activo', true)
                          ->orderBy('numero_economico')
                          ->get(['id', 'numero_economico', 'nombre_unidad', 'zona_id']);

        $disp_opciones = RevisionOptocontrol::DISP_OPCIONES;
        $status_opciones = RevisionOptocontrol::STATUS_OPCIONES;

        return view('admin.revisiones-optocontrol.create', compact(
            'unidades',
            'disp_opciones',
            'status_opciones'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'disp'            => 'required|in:' . implode(',', RevisionOptocontrol::DISP_OPCIONES),
            'unidad_id'       => 'required|exists:unidads,id',
            'fecha_reporte'   => 'required|date',
            'hora_entrada'    => 'required',
            'hora_salida'     => 'nullable',
            'ruta'            => 'nullable|string|max:255',
            'problema'        => 'required|string',
            'solucion'        => 'nullable|string',
            'status'          => 'required|in:' . implode(',', RevisionOptocontrol::STATUS_OPCIONES),
            'tiempo_estancia' => 'nullable|numeric|min:0',
            'responsable'     => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $unidad = Unidad::findOrFail($validated['unidad_id']);
            $validated['nombre_unidad'] = $unidad->nombre_unidad;
            $validated['created_by']    = auth()->id();

            // Si no se envio ruta, usar la zona de la unidad
            if (empty($validated['ruta']) && $unidad->zona) {
                $validated['ruta'] = $unidad->zona->nombre;
            }

            // Calcular tiempo de estancia
            if (!empty($validated['hora_entrada']) && !empty($validated['hora_salida'])) {
                $entrada = Carbon::parse($validated['hora_entrada']);
                $salida  = Carbon::parse($validated['hora_salida']);
                if ($salida->lessThan($entrada)) {
                    $salida->addDay();
                }
                $validated['tiempo_estancia'] = round($entrada->diffInMinutes($salida) / 60, 2);
            }

            RevisionOptocontrol::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.revisiones-optocontrol.index')
                ->with('success', 'Revision registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al registrar la revision: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $revision = RevisionOptocontrol::with(['unidad', 'creador'])->findOrFail($id);
        return view('admin.revisiones-optocontrol.show', compact('revision'));
    }

    public function edit($id)
    {
        $revision = RevisionOptocontrol::findOrFail($id);
        $unidades = Unidad::with('zona')
                          ->where('activo', true)
                          ->orderBy('numero_economico')
                          ->get(['id', 'numero_economico', 'nombre_unidad', 'zona_id']);

        $disp_opciones   = RevisionOptocontrol::DISP_OPCIONES;
        $status_opciones = RevisionOptocontrol::STATUS_OPCIONES;

        return view('admin.revisiones-optocontrol.edit', compact(
            'revision',
            'unidades',
            'disp_opciones',
            'status_opciones'
        ));
    }

    public function update(Request $request, $id)
    {
        $revision = RevisionOptocontrol::findOrFail($id);

        $validated = $request->validate([
            'disp'            => 'required|in:' . implode(',', RevisionOptocontrol::DISP_OPCIONES),
            'unidad_id'       => 'required|exists:unidads,id',
            'fecha_reporte'   => 'required|date',
            'hora_entrada'    => 'required',
            'hora_salida'     => 'nullable',
            'ruta'            => 'nullable|string|max:255',
            'problema'        => 'required|string',
            'solucion'        => 'nullable|string',
            'status'          => 'required|in:' . implode(',', RevisionOptocontrol::STATUS_OPCIONES),
            'tiempo_estancia' => 'nullable|numeric|min:0',
            'responsable'     => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $unidad = Unidad::findOrFail($validated['unidad_id']);
            $validated['nombre_unidad'] = $unidad->nombre_unidad;

            if (empty($validated['ruta']) && $unidad->zona) {
                $validated['ruta'] = $unidad->zona->nombre;
            }

            if (!empty($validated['hora_entrada']) && !empty($validated['hora_salida'])) {
                $entrada = Carbon::parse($validated['hora_entrada']);
                $salida  = Carbon::parse($validated['hora_salida']);
                if ($salida->lessThan($entrada)) {
                    $salida->addDay();
                }
                $validated['tiempo_estancia'] = round($entrada->diffInMinutes($salida) / 60, 2);
            }

            $revision->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.revisiones-optocontrol.index')
                ->with('success', 'Revision actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al actualizar la revision: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $revision = RevisionOptocontrol::findOrFail($id);
            $revision->delete();

            return redirect()
                ->route('admin.revisiones-optocontrol.index')
                ->with('success', 'Revision eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la revision: ' . $e->getMessage());
        }
    }

    /**
     * Devuelve datos de la unidad (nombre, ruta) via AJAX
     */
    public function getUnidadData($id)
    {
        $unidad = Unidad::with('zona')->findOrFail($id);
        return response()->json([
            'nombre_unidad' => $unidad->nombre_unidad,
            'ruta'          => $unidad->zona->nombre ?? null,
        ]);
    }

    /**
     * Dashboard estilo Power BI
     */
    public function dashboard(Request $request)
    {
        $query = RevisionOptocontrol::query();

        if ($request->filled('disp'))         $query->where('disp', $request->disp);
        if ($request->filled('status'))       $query->where('status', $request->status);
        if ($request->filled('fecha_desde'))  $query->whereDate('fecha_reporte', '>=', $request->fecha_desde);
        if ($request->filled('fecha_hasta'))  $query->whereDate('fecha_reporte', '<=', $request->fecha_hasta);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('problema', 'ILIKE', "%{$search}%")
                  ->orWhere('solucion', 'ILIKE', "%{$search}%")
                  ->orWhere('nombre_unidad', 'ILIKE', "%{$search}%")
                  ->orWhere('ruta', 'ILIKE', "%{$search}%");
            });
        }

        // KPIs
        $total        = (clone $query)->count();
        $pendientes   = (clone $query)->where('status', 'pendiente')->count();
        $en_proceso   = (clone $query)->where('status', 'en_proceso')->count();
        $resueltos    = (clone $query)->where('status', 'resuelto')->count();
        $cancelados   = (clone $query)->where('status', 'cancelado')->count();
        $ultimos30    = RevisionOptocontrol::where('fecha_reporte', '>=', now()->subDays(30))->count();

        // Recientes
        $recientes = (clone $query)->with(['unidad', 'creador'])
                                   ->orderBy('created_at', 'desc')
                                   ->limit(10)
                                   ->get();

        // Distribucion por dispositivo
        $distribucion_disp = (clone $query)
            ->select('disp', DB::raw('count(*) as total'))
            ->groupBy('disp')
            ->pluck('total', 'disp')
            ->toArray();

        // Distribucion por status
        $distribucion_status = (clone $query)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Tendencia ultimos 30 dias
        $tendenciaRaw = RevisionOptocontrol::where('fecha_reporte', '>=', now()->subDays(30))
            ->select('fecha_reporte', DB::raw('count(*) as total'))
            ->groupBy('fecha_reporte')
            ->orderBy('fecha_reporte')
            ->get();

        $tendencia_labels = $tendenciaRaw->map(fn($r) => Carbon::parse($r->fecha_reporte)->format('d/m'))->toArray();
        $tendencia_data   = $tendenciaRaw->pluck('total')->toArray();

        // Revisiones por ruta
        $rutasRaw = (clone $query)
            ->select('ruta', DB::raw('count(*) as total'))
            ->whereNotNull('ruta')
            ->groupBy('ruta')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $rutas_labels = $rutasRaw->pluck('ruta')->toArray();
        $rutas_data   = $rutasRaw->pluck('total')->toArray();

        // Top 5 unidades
        $unidadesRaw = (clone $query)
            ->select('unidad_id', 'nombre_unidad', DB::raw('count(*) as total'))
            ->groupBy('unidad_id', 'nombre_unidad')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $unidades_labels = $unidadesRaw->map(fn($u) => $u->nombre_unidad ?? ('ID ' . $u->unidad_id))->toArray();
        $unidades_data   = $unidadesRaw->pluck('total')->toArray();

        // Labels legibles
        $labels_disp = array_map(function ($key) {
            $map = [
                'barras'   => 'Barras',
                'telpo'    => 'Telpo',
                'gps'      => 'GPS',
                'camaras'  => 'Camaras',
                'revision' => 'Revision',
                'taller'   => 'Taller',
            ];
            return $map[$key] ?? $key;
        }, array_keys($distribucion_disp));

        $labels_status = array_map(function ($key) {
            $map = [
                'pendiente'  => 'Pendiente',
                'en_proceso' => 'En Proceso',
                'resuelto'   => 'Resuelto',
                'cancelado'  => 'Cancelado',
            ];
            return $map[$key] ?? $key;
        }, array_keys($distribucion_status));

        $disp_opciones   = RevisionOptocontrol::DISP_OPCIONES;
        $status_opciones = RevisionOptocontrol::STATUS_OPCIONES;

        return view('admin.revisiones-optocontrol.dashboard', compact(
            'total', 'pendientes', 'en_proceso', 'resueltos', 'cancelados', 'ultimos30',
            'recientes',
            'distribucion_disp', 'distribucion_status',
            'labels_disp', 'labels_status',
            'tendencia_labels', 'tendencia_data',
            'rutas_labels', 'rutas_data',
            'unidades_labels', 'unidades_data',
            'disp_opciones', 'status_opciones'
        ));
    }
}