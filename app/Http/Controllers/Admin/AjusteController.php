<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ajuste;
use App\Models\Operador;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AjusteController extends Controller
{
    public function index(Request $request)
    {
        $query = Ajuste::with(['operador', 'unidad', 'creador']);

        // Filtros
        if ($request->filled('search')) {
            $query->buscar($request->search);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->entreFechas($request->fecha_inicio, $request->fecha_fin);
        }

        if ($request->filled('firmado') && $request->firmado !== '') {
            $query->firmados($request->firmado);
        }

        $ajustes = $query->orderBy('created_at', 'desc')->paginate(15);
        $ajustes->appends($request->all());

        return view('admin.ajustes.index', compact('ajustes'));
    }

    public function create()
    {
        $operadores = Operador::where('activo', true)->orderBy('nombre_completo')->get();
        $unidades = Unidad::where('activo', true)->orderBy('numero_economico')->get();
        
        $fecha = date('Y-m-d');
        $hora = date('H:i');

        return view('admin.ajustes.create', compact('operadores', 'unidades', 'fecha', 'hora'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'zona' => 'nullable|string|max:100',
                'fecha' => 'required|date',
                'hora' => 'required',
                'monto_total' => 'required|numeric|min:0',
                'folio' => 'required|string|max:50|unique:ajustes,folio',
                'operador_id' => 'required|exists:operadores,id',
                'clave_operador' => 'required|string|max:50',
                'unidad_id' => 'required|exists:unidades,id',
                'firmado' => 'sometimes|boolean',
            ]);

            $ajuste = Ajuste::create([
                'zona' => $validated['zona'] ?? null,
                'fecha' => $validated['fecha'],
                'hora' => $validated['hora'],
                'monto_total' => $validated['monto_total'],
                'folio' => $validated['folio'],
                'operador_id' => $validated['operador_id'],
                'clave_operador' => $validated['clave_operador'],
                'unidad_id' => $validated['unidad_id'],
                'firmado' => $request->has('firmado'),
                'created_by' => Auth::id(),
            ]);

            Log::info("Ajuste creado ID: {$ajuste->id} - Folio: {$ajuste->folio}");

            return redirect()->route('admin.ajustes.index')
                ->with('success', "Ajuste {$ajuste->folio} creado exitosamente.");

        } catch (\Exception $e) {
            Log::error('Error al crear ajuste: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $ajuste = Ajuste::with(['operador', 'unidad', 'creador'])->findOrFail($id);
        return view('admin.ajustes.show', compact('ajuste'));
    }

    public function edit($id)
    {
        $ajuste = Ajuste::findOrFail($id);
        $operadores = Operador::where('activo', true)->orderBy('nombre_completo')->get();
        $unidades = Unidad::where('activo', true)->orderBy('numero_economico')->get();

        return view('admin.ajustes.edit', compact('ajuste', 'operadores', 'unidades'));
    }

    public function update(Request $request, $id)
    {
        try {
            $ajuste = Ajuste::findOrFail($id);

            $validated = $request->validate([
                'zona' => 'nullable|string|max:100',
                'fecha' => 'required|date',
                'hora' => 'required',
                'monto_total' => 'required|numeric|min:0',
                'folio' => 'required|string|max:50|unique:ajustes,folio,' . $id,
                'operador_id' => 'required|exists:operadores,id',
                'clave_operador' => 'required|string|max:50',
                'unidad_id' => 'required|exists:unidades,id',
                'firmado' => 'sometimes|boolean',
            ]);

            $ajuste->update([
                'zona' => $validated['zona'] ?? null,
                'fecha' => $validated['fecha'],
                'hora' => $validated['hora'],
                'monto_total' => $validated['monto_total'],
                'folio' => $validated['folio'],
                'operador_id' => $validated['operador_id'],
                'clave_operador' => $validated['clave_operador'],
                'unidad_id' => $validated['unidad_id'],
                'firmado' => $request->has('firmado'),
            ]);

            Log::info("Ajuste actualizado ID: {$ajuste->id} - Folio: {$ajuste->folio}");

            return redirect()->route('admin.ajustes.index')
                ->with('success', "Ajuste {$ajuste->folio} actualizado exitosamente.");

        } catch (\Exception $e) {
            Log::error('Error al actualizar ajuste: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $ajuste = Ajuste::findOrFail($id);
            $folio = $ajuste->folio;
            $ajuste->delete();

            Log::info("Ajuste eliminado ID: {$id} - Folio: {$folio}");

            return redirect()->route('admin.ajustes.index')
                ->with('success', "Ajuste {$folio} eliminado exitosamente.");

        } catch (\Exception $e) {
            Log::error('Error al eliminar ajuste: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    // API para obtener operadores (para autocomplete)
    public function getOperadores(Request $request)
    {
        $search = $request->get('q', '');
        $operadores = Operador::where('activo', true)
            ->where(function ($query) use ($search) {
                $query->where('nombre_completo', 'LIKE', "%{$search}%")
                    ->orWhere('clave_operador', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'nombre_completo', 'clave_operador']);

        return response()->json($operadores);
    }

    // API para obtener unidades (para autocomplete)
    public function getUnidades(Request $request)
    {
        $search = $request->get('q', '');
        $unidades = Unidad::where('activo', true)
            ->where(function ($query) use ($search) {
                $query->where('numero_economico', 'LIKE', "%{$search}%")
                    ->orWhere('nombre_unidad', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'numero_economico', 'nombre_unidad']);

        return response()->json($unidades);
    }
}