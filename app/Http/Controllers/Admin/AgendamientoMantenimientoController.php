<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use App\Models\AgendamientoMantenimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AgendamientoMantenimientoController extends Controller
{
    public function index(Request $request)
    {
        $query = AgendamientoMantenimiento::with(['unidad', 'creador'])
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

        $agendamientos = $query->paginate(20);
        $unidades = Unidad::where('activo', true)->orderBy('numero_economico')->get();

        return view('admin.agendamientos.index', compact('agendamientos', 'unidades'));
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
            ]);

            // Verificar si la unidad ya tiene un agendamiento pendiente
            $pendiente = AgendamientoMantenimiento::where('unidad_id', $validated['unidad_id'])
                ->where('estado', 'pendiente')
                ->first();

            if ($pendiente) {
                return back()->withErrors([
                    'error' => 'Esta unidad ya tiene un agendamiento pendiente para el día ' . $pendiente->fecha_agendada->format('d/m/Y')
                ])->withInput();
            }

            $agendamiento = AgendamientoMantenimiento::create([
                'unidad_id' => $validated['unidad_id'],
                'fecha_agendada' => $validated['fecha_agendada'],
                'estado' => 'pendiente',
                'observaciones' => $validated['observaciones'] ?? null,
                'created_by' => Auth::id(),
            ]);

            Log::info("Agendamiento creado ID: {$agendamiento->id} - Unidad: {$validated['unidad_id']} - Fecha: {$validated['fecha_agendada']}");

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

        return view('admin.agendamientos.edit', compact('agendamiento', 'unidades'));
    }

    public function update(Request $request, $id)
    {
        try {
            $agendamiento = AgendamientoMantenimiento::findOrFail($id);

            $validated = $request->validate([
                'fecha_agendada' => 'required|date|after_or_equal:today',
                'estado' => 'required|in:pendiente,cumplido,no_cumplido,reagendado',
                'observaciones' => 'nullable|string',
            ]);

            // Si se marca como cumplido, guardar fecha de cumplimiento
            $fechaCumplimiento = null;
            if ($validated['estado'] === 'cumplido') {
                $fechaCumplimiento = now()->toDateString();
            }

            $agendamiento->update([
                'fecha_agendada' => $validated['fecha_agendada'],
                'estado' => $validated['estado'],
                'fecha_cumplimiento' => $fechaCumplimiento,
                'observaciones' => $validated['observaciones'] ?? $agendamiento->observaciones,
            ]);

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

    // Método para marcar como cumplido desde el dashboard
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

    // Método para reagendar
    public function reagendar(Request $request, $id)
    {
        try {
            $agendamiento = AgendamientoMantenimiento::findOrFail($id);

            $validated = $request->validate([
                'nueva_fecha' => 'required|date|after:today',
                'motivo' => 'nullable|string',
            ]);

            // Marcar el anterior como reagendado
            $agendamiento->update([
                'estado' => 'reagendado',
                'observaciones' => ($agendamiento->observaciones ?? '') . "\nReagendado para: {$validated['nueva_fecha']}. Motivo: {$validated['motivo']}",
            ]);

            // Crear nuevo agendamiento con la nueva fecha
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