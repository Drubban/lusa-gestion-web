<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tecnologia;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TecnologiaController extends Controller
{
    public function index(Request $request)
    {
        $query = Tecnologia::with(['unidad', 'barras', 'telpo', 'gps', 'mdvr']);

        if ($request->filled('tipo')) {
            $query->porTipo($request->tipo);
        }

        if ($request->filled('unidad_id')) {
            $query->porUnidad($request->unidad_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('unidad', function ($q) use ($search) {
                $q->where('numero_economico', 'LIKE', "%{$search}%")
                  ->orWhere('nombre_unidad', 'LIKE', "%{$search}%");
            });
        }

        $tecnologias = $query->orderBy('created_at', 'desc')->paginate(15);
        $tecnologias->appends($request->all());

        $tipos = Tecnologia::TIPOS;
        $unidades = Unidad::where('activo', true)->orderBy('numero_economico')->get();

        return view('admin.tecnologias.index', compact('tecnologias', 'tipos', 'unidades'));
    }

    public function create()
    {
        $unidades = Unidad::where('activo', true)->orderBy('numero_economico')->get();
        $tipos = Tecnologia::TIPOS;
        
        return view('admin.tecnologias.create', compact('unidades', 'tipos'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'unidad_id' => 'required|exists:unidades,id',
                'tipo' => 'required|in:' . implode(',', array_keys(Tecnologia::TIPOS)),
                'nombre' => 'nullable|string|max:100',
                'activo' => 'sometimes|boolean',
            ]);

            // Verificar que no exista ya esta tecnología para esta unidad
            $exists = Tecnologia::where('unidad_id', $validated['unidad_id'])
                ->where('tipo', $validated['tipo'])
                ->exists();

            if ($exists) {
                return back()->withErrors(['error' => 'Esta unidad ya tiene esta tecnología asignada.'])->withInput();
            }

            $tecnologia = Tecnologia::create([
                'unidad_id' => $validated['unidad_id'],
                'tipo' => $validated['tipo'],
                'nombre' => $validated['nombre'] ?? null,
                'activo' => $request->has('activo'),
                'created_by' => Auth::id(),
            ]);

            // Crear el registro específico según el tipo
            $this->crearDatosEspecificos($tecnologia, $request);

            Log::info("Tecnología creada ID: {$tecnologia->id} - Tipo: {$tecnologia->tipo}");

            return redirect()->route('admin.tecnologias.index')
                ->with('success', "Tecnología {$tecnologia->tipo_nombre} creada exitosamente.");

        } catch (\Exception $e) {
            Log::error('Error al crear tecnología: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $tecnologia = Tecnologia::with(['unidad', 'barras', 'telpo', 'gps', 'mdvr', 'creador'])->findOrFail($id);
        return view('admin.tecnologias.show', compact('tecnologia'));
    }

    public function edit($id)
    {
        $tecnologia = Tecnologia::with(['barras', 'telpo', 'gps', 'mdvr'])->findOrFail($id);
        $unidades = Unidad::where('activo', true)->orderBy('numero_economico')->get();
        $tipos = Tecnologia::TIPOS;
        
        return view('admin.tecnologias.edit', compact('tecnologia', 'unidades', 'tipos'));
    }

    public function update(Request $request, $id)
    {
        try {
            $tecnologia = Tecnologia::findOrFail($id);

            $validated = $request->validate([
                'unidad_id' => 'required|exists:unidades,id',
                'tipo' => 'required|in:' . implode(',', array_keys(Tecnologia::TIPOS)),
                'nombre' => 'nullable|string|max:100',
                'activo' => 'sometimes|boolean',
            ]);

            // Verificar que no exista ya esta tecnología para esta unidad (excluyendo la actual)
            $exists = Tecnologia::where('unidad_id', $validated['unidad_id'])
                ->where('tipo', $validated['tipo'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return back()->withErrors(['error' => 'Esta unidad ya tiene esta tecnología asignada.'])->withInput();
            }

            $tecnologia->update([
                'unidad_id' => $validated['unidad_id'],
                'tipo' => $validated['tipo'],
                'nombre' => $validated['nombre'] ?? null,
                'activo' => $request->has('activo'),
            ]);

            // Actualizar los datos específicos según el tipo
            $this->actualizarDatosEspecificos($tecnologia, $request);

            Log::info("Tecnología actualizada ID: {$tecnologia->id}");

            return redirect()->route('admin.tecnologias.index')
                ->with('success', "Tecnología {$tecnologia->tipo_nombre} actualizada exitosamente.");

        } catch (\Exception $e) {
            Log::error('Error al actualizar tecnología: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $tecnologia = Tecnologia::findOrFail($id);
            $nombre = $tecnologia->tipo_nombre;
            
            // Eliminar los datos específicos según el tipo
            $this->eliminarDatosEspecificos($tecnologia);
            
            $tecnologia->delete();

            Log::info("Tecnología eliminada ID: {$id}");

            return redirect()->route('admin.tecnologias.index')
                ->with('success', "Tecnología {$nombre} eliminada exitosamente.");

        } catch (\Exception $e) {
            Log::error('Error al eliminar tecnología: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    // Métodos privados para manejar datos específicos
    private function crearDatosEspecificos(Tecnologia $tecnologia, Request $request)
    {
        switch ($tecnologia->tipo) {
            case 'barras':
                $tecnologia->barras()->create([
                    'id_barra' => $request->id_barra,
                    'barras' => $request->barras,
                    'telefono' => $request->telefono_barras,
                    'plan' => $request->plan_barras,
                ]);
                break;

            case 'telpo':
                $tecnologia->telpo()->create([
                    'imei_antes' => $request->imei_antes,
                    'v_apk' => $request->v_apk,
                    'telpo' => $request->telpo,
                    'imei_telpo' => $request->imei_telpo,
                    'telefono' => $request->telefono_telpo,
                    'plan' => $request->plan_telpo,
                    'costo_plan' => $request->costo_plan,
                ]);
                break;

            case 'gps':
                $tecnologia->gps()->create([
                    'imei_gps' => $request->imei_gps,
                    'telefono' => $request->telefono_gps,
                    'plan' => $request->plan_gps,
                ]);
                break;

            case 'mdvr':
                $tecnologia->mdvr()->create([
                    'dvr' => $request->dvr,
                    'modelo' => $request->modelo,
                    'camaras' => $request->camaras,
                    'memoria' => $request->memoria,
                ]);
                break;
        }
    }

    private function actualizarDatosEspecificos(Tecnologia $tecnologia, Request $request)
    {
        switch ($tecnologia->tipo) {
            case 'barras':
                $tecnologia->barras()->updateOrCreate(
                    ['tecnologia_id' => $tecnologia->id],
                    [
                        'id_barra' => $request->id_barra,
                        'barras' => $request->barras,
                        'telefono' => $request->telefono_barras,
                        'plan' => $request->plan_barras,
                    ]
                );
                break;

            case 'telpo':
                $tecnologia->telpo()->updateOrCreate(
                    ['tecnologia_id' => $tecnologia->id],
                    [
                        'imei_antes' => $request->imei_antes,
                        'v_apk' => $request->v_apk,
                        'telpo' => $request->telpo,
                        'imei_telpo' => $request->imei_telpo,
                        'telefono' => $request->telefono_telpo,
                        'plan' => $request->plan_telpo,
                        'costo_plan' => $request->costo_plan,
                    ]
                );
                break;

            case 'gps':
                $tecnologia->gps()->updateOrCreate(
                    ['tecnologia_id' => $tecnologia->id],
                    [
                        'imei_gps' => $request->imei_gps,
                        'telefono' => $request->telefono_gps,
                        'plan' => $request->plan_gps,
                    ]
                );
                break;

            case 'mdvr':
                $tecnologia->mdvr()->updateOrCreate(
                    ['tecnologia_id' => $tecnologia->id],
                    [
                        'dvr' => $request->dvr,
                        'modelo' => $request->modelo,
                        'camaras' => $request->camaras,
                        'memoria' => $request->memoria,
                    ]
                );
                break;
        }
    }

    private function eliminarDatosEspecificos(Tecnologia $tecnologia)
    {
        switch ($tecnologia->tipo) {
            case 'barras':
                $tecnologia->barras()->delete();
                break;
            case 'telpo':
                $tecnologia->telpo()->delete();
                break;
            case 'gps':
                $tecnologia->gps()->delete();
                break;
            case 'mdvr':
                $tecnologia->mdvr()->delete();
                break;
        }
    }
}