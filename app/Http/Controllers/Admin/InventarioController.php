<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\Departamento;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventario::with(['departamento', 'zona', 'creador']);

        // Filtros
        if ($request->filled('categoria')) {
            $query->porCategoria($request->categoria);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->entreFechas($request->fecha_inicio, $request->fecha_fin);
        }

        if ($request->filled('search')) {
            $query->buscar($request->search);
        }

        $inventarios = $query->orderBy('created_at', 'desc')->paginate(15);
        $inventarios->appends($request->all());

        $categorias = Inventario::CATEGORIAS;

        return view('admin.inventario.index', compact('inventarios', 'categorias'));
    }

    public function create()
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $categorias = Inventario::CATEGORIAS;
        $categoriasEquipo = Inventario::CATEGORIAS_EQUIPO;
        $categoriasProducto = Inventario::CATEGORIAS_PRODUCTO;

        return view('admin.inventario.create', compact(
            'departamentos',
            'categorias',
            'categoriasEquipo',
            'categoriasProducto'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateRequest($request);

            // Subir imagen si existe
            $imagenPath = null;
            if ($request->hasFile('imagen')) {
                $imagenPath = $request->file('imagen')->store('inventario', 'public');
            }

            $inventario = Inventario::create([
                'fecha_entrega' => $validated['fecha_entrega'],
                'departamento_id' => $validated['departamento_id'],
                // 'zona_id' => $validated['zona_id'],
                'area' => $validated['area'] ?? null,
                'nombre_recibe' => $validated['nombre_recibe'],
                'clave_empleado' => $validated['clave_empleado'],
                'categoria' => $validated['categoria'],
                'nombre_equipo' => $validated['nombre_equipo'] ?? null,
                'marca' => $validated['marca'] ?? null,
                'modelo' => $validated['modelo'] ?? null,
                'numero_serie' => $validated['numero_serie'] ?? null,
                'datos_extra' => $validated['datos_extra'] ?? null,
                'nombre_producto' => $validated['nombre_producto'] ?? null,
                'marca_producto' => $validated['marca_producto'] ?? null,
                'cantidad' => $validated['cantidad'] ?? null,
                'descripcion' => $validated['descripcion'] ?? null,
                'imagen' => $imagenPath,
                'created_by' => Auth::id(),
            ]);

            Log::info("Inventario creado ID: {$inventario->id} por usuario: " . Auth::id());

            return redirect()->route('admin.inventario.index')
                ->with('success', 'Registro de inventario creado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al crear inventario: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $inventario = Inventario::with(['departamento', 'zona', 'creador'])->findOrFail($id);
        return view('admin.inventario.show', compact('inventario'));
    }

    public function edit($id)
    {
        $inventario = Inventario::findOrFail($id);
        $departamentos = Departamento::orderBy('nombre')->get();
        $categorias = Inventario::CATEGORIAS;
        $categoriasEquipo = Inventario::CATEGORIAS_EQUIPO;
        $categoriasProducto = Inventario::CATEGORIAS_PRODUCTO;

        return view('admin.inventario.edit', compact(
            'inventario',
            'departamentos',
            'categorias',
            'categoriasEquipo',
            'categoriasProducto'
        ));
    }

    public function update(Request $request, $id)
    {
        try {
            $inventario = Inventario::findOrFail($id);
            $validated = $this->validateRequest($request, $id);

            $data = [
                'fecha_entrega' => $validated['fecha_entrega'],
                'departamento_id' => $validated['departamento_id'],
                // 'zona_id' => $validated['zona_id'],
                'area' => $validated['area'] ?? null,
                'nombre_recibe' => $validated['nombre_recibe'],
                'clave_empleado' => $validated['clave_empleado'],
                'categoria' => $validated['categoria'],
                'nombre_equipo' => $validated['nombre_equipo'] ?? null,
                'marca' => $validated['marca'] ?? null,
                'modelo' => $validated['modelo'] ?? null,
                'numero_serie' => $validated['numero_serie'] ?? null,
                'datos_extra' => $validated['datos_extra'] ?? null,
                'nombre_producto' => $validated['nombre_producto'] ?? null,
                'marca_producto' => $validated['marca_producto'] ?? null,
                'cantidad' => $validated['cantidad'] ?? null,
                'descripcion' => $validated['descripcion'] ?? null,
            ];

            // Subir nueva imagen si existe
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior
                if ($inventario->imagen) {
                    Storage::disk('public')->delete($inventario->imagen);
                }
                $data['imagen'] = $request->file('imagen')->store('inventario', 'public');
            }

            $inventario->update($data);

            Log::info("Inventario actualizado ID: {$inventario->id}");

            return redirect()->route('admin.inventario.index')
                ->with('success', 'Registro de inventario actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar inventario: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $inventario = Inventario::findOrFail($id);
            
            // Eliminar imagen si existe
            if ($inventario->imagen) {
                Storage::disk('public')->delete($inventario->imagen);
            }
            
            $inventario->delete();

            Log::info("Inventario eliminado ID: {$id}");

            return redirect()->route('admin.inventario.index')
                ->with('success', 'Registro de inventario eliminado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar inventario: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    // Validación centralizada
    private function validateRequest(Request $request, $id = null)
    {
        $rules = [
            'fecha_entrega' => 'required|date',
            'departamento_id' => 'required|exists:departamentos,id',
            // 'zona_id' => 'nullable|exists:zonas,id',
            'area' => 'nullable|string|max:100',
            'nombre_recibe' => 'required|string|max:100',
            'clave_empleado' => 'required|string|max:50',
            'categoria' => 'required|in:' . implode(',', array_keys(Inventario::CATEGORIAS)),
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:5120', // 5MB
        ];

        // Reglas condicionales según categoría
        $categoria = $request->categoria;

        if (in_array($categoria, Inventario::CATEGORIAS_EQUIPO)) {
            $rules['nombre_equipo'] = 'required|string|max:100';
            $rules['marca'] = 'required|string|max:100';
            $rules['modelo'] = 'required|string|max:100';
            $rules['numero_serie'] = 'required|string|max:100|unique:inventario,numero_serie,' . ($id ?? 'NULL') . ',id';
            $rules['datos_extra'] = 'nullable|string';
        }

        if (in_array($categoria, Inventario::CATEGORIAS_PRODUCTO)) {
            $rules['nombre_producto'] = 'required|string|max:100';
            $rules['marca_producto'] = 'required|string|max:100';
            $rules['cantidad'] = 'required|integer|min:1';
            $rules['descripcion'] = 'nullable|string';
        }

        return $request->validate($rules);
    }

    public function getCategorias()
    {
        return response()->json(Inventario::CATEGORIAS);
    }
}