<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use App\Models\AgendamientoMantenimiento;
use App\Models\DocumentoMantenimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AgendamientoMantenimientoController extends Controller
{
    public function index(Request $request)
    {
        $query = AgendamientoMantenimiento::with(['unidad', 'creador', 'reportadoPor'])
            ->orderBy('fecha_agendada', 'asc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('unidad_id')) {
            $query->where('unidad_id', $request->unidad_id);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_agendada', [$request->fecha_inicio, $request->fecha_fin]);
        }

        if ($request->filled('motivo')) {
            $query->where('motivo_no_presentado', $request->motivo);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('unidad', function ($q) use ($search) {
                $q->where('numero_economico', 'LIKE', "%{$search}%")
                  ->orWhere('nombre_unidad', 'LIKE', "%{$search}%");
            });
        }

        $agendamientos = $query->paginate(20);
        $unidades = Unidad::where('activo', true)->orderBy('numero_economico')->get();

        $motivos = [
            'taller' => 'En taller',
            'averia' => 'Averia mecanica',
            'falta_operador' => 'Falta de operador',
            'documentacion' => 'Falta de documentacion',
            'cliente' => 'Unidad en ruta con cliente',
            'otros' => 'Otro motivo',
        ];

        $estados = [
            'pendiente' => 'Pendiente',
            'cumplido' => 'Cumplido',
            'no_cumplido' => 'No cumplido',
            'reagendado' => 'Reagendado',
        ];

        return view('admin.agendamientos.index', compact('agendamientos', 'unidades', 'motivos', 'estados'));
    }

    public function create(Request $request)
    {
        $unidades = Unidad::where('activo', true)->orderBy('numero_economico')->get();
        $unidadSeleccionada = null;

        if ($request->has('unidad')) {
            $unidadSeleccionada = Unidad::find($request->unidad);
        }

        return view('admin.agendamientos.create', compact('unidades', 'unidadSeleccionada'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'unidad_id' => 'required|exists:unidades,id',
                'fecha_agendada' => 'required|date|after_or_equal:today',
                'observaciones' => 'nullable|string',
                'motivo_no_presentado' => 'nullable|string|in:taller,averia,falta_operador,documentacion,cliente,otros',
                'fecha_reprogramada' => 'nullable|date|after_or_equal:today',
            ]);

            // Verificar si ya tiene agendamiento pendiente
            $existente = AgendamientoMantenimiento::where('unidad_id', $validated['unidad_id'])
                ->whereIn('estado', ['pendiente', 'no_cumplido'])
                ->first();

            // Si viene de un reporte de no presentado (tiene motivo)
            if ($request->filled('motivo_no_presentado')) {
                // Si existe un agendamiento pendiente, marcarlo como no cumplido
                if ($existente) {
                    $existente->update([
                        'estado' => 'no_cumplido',
                        'motivo_no_presentado' => $validated['motivo_no_presentado'],
                        'observaciones' => ($existente->observaciones ?? '') . ' - No presentado: ' . $validated['motivo_no_presentado'],
                        'reportado_por' => Auth::id(),
                        'fecha_reprogramada' => $validated['fecha_reprogramada'] ?? null,
                    ]);
                    Log::info("Agendamiento ID: {$existente->id} marcado como no_cumplido");
                }

                // Si se reprograma, crear un nuevo agendamiento
                if ($request->filled('fecha_reprogramada')) {
                    $nuevo = AgendamientoMantenimiento::create([
                        'unidad_id' => $validated['unidad_id'],
                        'fecha_agendada' => $validated['fecha_reprogramada'],
                        'estado' => 'pendiente',
                        'observaciones' => "Reprogramado desde reporte de no presentado. Motivo: {$validated['motivo_no_presentado']}",
                        'created_by' => Auth::id(),
                    ]);
                    Log::info("Nuevo agendamiento creado ID: {$nuevo->id} para unidad {$validated['unidad_id']}");
                    
                    return redirect()->route('admin.agendamientos.index')
                        ->with('success', 'Unidad reportada como no presentada y reagendada exitosamente.');
                }

                return redirect()->route('admin.agendamientos.index')
                    ->with('success', 'Unidad reportada como no presentada correctamente.');
            }

            // Si es un agendamiento normal (sin motivo)
            if ($existente) {
                return back()->withErrors([
                    'error' => 'Esta unidad ya tiene un agendamiento pendiente para el dia ' . $existente->fecha_agendada->format('d/m/Y')
                ])->withInput();
            }

            $agendamiento = AgendamientoMantenimiento::create([
                'unidad_id' => $validated['unidad_id'],
                'fecha_agendada' => $validated['fecha_agendada'],
                'estado' => 'pendiente',
                'observaciones' => $validated['observaciones'] ?? null,
                'created_by' => Auth::id(),
            ]);

            Log::info("Agendamiento creado ID: {$agendamiento->id} - Unidad: {$validated['unidad_id']}");

            return redirect()->route('admin.agendamientos.index')
                ->with('success', 'Agendamiento creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear agendamiento: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $agendamiento = AgendamientoMantenimiento::findOrFail($id);
        $unidades = Unidad::where('activo', true)->orderBy('numero_economico')->get();

        $motivos = [
            'taller' => 'En taller',
            'averia' => 'Averia mecanica',
            'falta_operador' => 'Falta de operador',
            'documentacion' => 'Falta de documentacion',
            'cliente' => 'Unidad en ruta con cliente',
            'otros' => 'Otro motivo',
        ];

        return view('admin.agendamientos.edit', compact('agendamiento', 'unidades', 'motivos'));
    }

    public function update(Request $request, $id)
    {
        try {
            $agendamiento = AgendamientoMantenimiento::findOrFail($id);

            $validated = $request->validate([
                'fecha_agendada' => 'required|date|after_or_equal:today',
                'estado' => 'required|in:pendiente,cumplido,no_cumplido,reagendado',
                'observaciones' => 'nullable|string',
                'motivo_no_presentado' => 'nullable|string|in:taller,averia,falta_operador,documentacion,cliente,otros',
                'fecha_reprogramada' => 'nullable|date|after_or_equal:today',
            ]);

            $fechaCumplimiento = null;
            if ($validated['estado'] === 'cumplido') {
                $fechaCumplimiento = now()->toDateString();
            }

            $agendamiento->update([
                'fecha_agendada' => $validated['fecha_agendada'],
                'estado' => $validated['estado'],
                'fecha_cumplimiento' => $fechaCumplimiento,
                'observaciones' => $validated['observaciones'] ?? $agendamiento->observaciones,
                'motivo_no_presentado' => $validated['motivo_no_presentado'] ?? null,
                'fecha_reprogramada' => $validated['fecha_reprogramada'] ?? null,
                'reportado_por' => $validated['estado'] === 'no_cumplido' ? Auth::id() : null,
            ]);

            // Si se marcó como no cumplido y se reprograma
            if ($validated['estado'] === 'no_cumplido' && $request->filled('fecha_reprogramada')) {
                // Verificar que no haya otro agendamiento pendiente
                $existente = AgendamientoMantenimiento::where('unidad_id', $agendamiento->unidad_id)
                    ->where('estado', 'pendiente')
                    ->first();

                if (!$existente) {
                    $nuevo = AgendamientoMantenimiento::create([
                        'unidad_id' => $agendamiento->unidad_id,
                        'fecha_agendada' => $validated['fecha_reprogramada'],
                        'estado' => 'pendiente',
                        'observaciones' => "Reprogramado desde ID: {$agendamiento->id}. Motivo: {$validated['motivo_no_presentado']}",
                        'created_by' => Auth::id(),
                    ]);
                    Log::info("Agendamiento reprogramado ID: {$id} -> Nuevo ID: {$nuevo->id}");
                }
            }

            Log::info("Agendamiento actualizado ID: {$agendamiento->id} - Estado: {$validated['estado']}");

            return redirect()->route('admin.agendamientos.index')
                ->with('success', 'Agendamiento actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar agendamiento: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $agendamiento = AgendamientoMantenimiento::findOrFail($id);
            $agendamiento->delete();

            Log::info("Agendamiento eliminado ID: {$id}");

            return redirect()->route('admin.agendamientos.index')
                ->with('success', 'Agendamiento eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar agendamiento: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    public function marcarCumplido($id)
    {
        try {
            $agendamiento = AgendamientoMantenimiento::findOrFail($id);
            $agendamiento->update([
                'estado' => 'cumplido',
                'fecha_cumplimiento' => now()->toDateString(),
            ]);

            Log::info("Agendamiento marcado como cumplido ID: {$id}");

            return redirect()->route('admin.agendamientos.index')
                ->with('success', 'Agendamiento marcado como cumplido.');
        } catch (\Exception $e) {
            Log::error('Error al marcar cumplido: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al marcar cumplido: ' . $e->getMessage()]);
        }
    }

    public function reagendar(Request $request, $id)
    {
        try {
            $agendamiento = AgendamientoMantenimiento::findOrFail($id);

            $validated = $request->validate([
                'nueva_fecha' => 'required|date|after:today',
                'motivo' => 'nullable|string',
            ]);

            $agendamiento->update([
                'estado' => 'reagendado',
                'observaciones' => ($agendamiento->observaciones ?? '') . "\nReagendado para: {$validated['nueva_fecha']}. Motivo: {$validated['motivo']}",
            ]);

            $nuevo = AgendamientoMantenimiento::create([
                'unidad_id' => $agendamiento->unidad_id,
                'fecha_agendada' => $validated['nueva_fecha'],
                'estado' => 'pendiente',
                'observaciones' => "Reagendado desde ID: {$agendamiento->id}. Motivo: {$validated['motivo']}",
                'created_by' => Auth::id(),
            ]);

            Log::info("Agendamiento reagendado ID: {$id} -> Nueva fecha: {$validated['nueva_fecha']}");

            return redirect()->route('admin.agendamientos.index')
                ->with('success', 'Agendamiento reagendado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al reagendar: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al reagendar: ' . $e->getMessage()]);
        }
    }
}