<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Operador;
use App\Models\Unidad;
use App\Models\AsignacionOperadorUnidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OperadorController extends Controller
{
    public function index(Request $request)
    {
        Log::info('=== OperadorController@index ===');

        // Obtener parámetros de filtro y orden
        $search = $request->input('search', '');
        // $estado = $request->input('estado', '');
        $zona = $request->input('zona', '');
        $sort = $request->input('sort', 'clave_operador');
        $direction = $request->input('direction', 'asc');

        // Construir la consulta base
        $query = Operador::query();

        // 🔍 JOIN con asignación vigente y unidad para ordenamiento por número económico
        $query->leftJoin('asignacion_operador_unidad', function ($join) {
            $join->on('operadores.id', '=', 'asignacion_operador_unidad.operador_id')
                ->where('asignacion_operador_unidad.vigente', true);
        })->leftJoin('unidades', 'asignacion_operador_unidad.unidad_id', '=', 'unidades.id');

        // Seleccionar los campos de operadores
        $query->select('operadores.*');

        // Aplicar filtro de búsqueda
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('operadores.clave_operador', 'LIKE', "%{$search}%")
                    ->orWhere('operadores.nombre_completo', 'LIKE', "%{$search}%")
                    ->orWhere('unidades.numero_economico', 'LIKE', "%{$search}%");
            });
        }

        // Aplicar filtro por estado
        // if ($estado !== '') {
            // $query->where('operadores.activo', $estado == 'activo' ? 1 : 0);
        // }

        // Aplicar filtro por zona
        if (!empty($zona)) {
            $query->whereHas('asignacionVigente.unidad.zona', function ($q) use ($zona) {
                $q->where('nombre', $zona);
            });
        }

        // Aplicar ordenamiento
        switch ($sort) {
            case 'clave_operador':
                $query->orderBy('operadores.clave_operador', $direction);
                break;
            case 'nombre_completo':
                $query->orderBy('operadores.nombre_completo', $direction);
                break;
            case 'unidad_numero':
                // 🔥 Ordenar por el número económico de la unidad actual
                $query->orderBy('unidades.numero_economico', $direction);
                break;
            default:
                $query->orderBy('operadores.clave_operador', $direction);
                break;
        }

        // Cargar relaciones y paginar
        $operadores = $query->with(['asignacionVigente.unidad.zona'])->paginate(15);

        // Mantener los parámetros en la paginación
        $operadores->appends([
            'search' => $search,
            // 'estado' => $estado,
            'zona' => $zona,
            'sort' => $sort,
            'direction' => $direction
        ]);

        return view('admin.operadores.index', compact('operadores', 'search', 'zona', 'sort', 'direction'));
    }

    public function show($id)
    {
        Log::info("=== OperadorController@show ID: $id ===");
        try {
            $operador = Operador::with(['asignaciones.unidad'])->findOrFail($id);
            $unidadActual = $operador->asignacionVigente->unidad ?? null;
            Log::debug("Operador encontrado: {$operador->nombre_completo}");
            return view('admin.operadores.show', compact('operador', 'unidadActual'));
        } catch (\Exception $e) {
            Log::error("Error en show ID $id: " . $e->getMessage());
            throw $e;
        }
    }

    public function create()
    {
        $unidades = Unidad::where('activo', true)->get();
        return view('admin.operadores.create', compact('unidades'));
    }

    public function store(Request $request)
    {
        Log::info('=== OperadorController@store ===');
        Log::debug('Datos recibidos: ', $request->all());

        // Convertir 'activo' a booleano
        if ($request->has('activo')) {
            $request->merge(['activo' => filter_var($request->activo, FILTER_VALIDATE_BOOLEAN)]);
        } else {
            $request->merge(['activo' => false]);
        }

        $validator = validator($request->all(), [
            'clave_operador' => 'required|string|unique:operadores',
            'nombre_completo' => 'required|string|max:255',
            'activo' => 'boolean',
            'unidad_id' => 'nullable|exists:unidades,id',
        ]);

        if ($validator->fails()) {
            Log::error('Validación fallida: ' . json_encode($validator->errors()->all()));
            return back()->withErrors($validator)->withInput();
        }

        try {
            $operador = Operador::create([
                'clave_operador' => $request->clave_operador,
                'nombre_completo' => $request->nombre_completo,
                'activo' => $request->activo,
            ]);

            Log::info("Operador creado con ID: {$operador->id}");

            if ($request->filled('unidad_id')) {
                AsignacionOperadorUnidad::where('operador_id', $operador->id)
                    ->where('vigente', true)
                    ->update(['fecha_fin' => now(), 'vigente' => false]);

                AsignacionOperadorUnidad::create([
                    'operador_id' => $operador->id,
                    'unidad_id' => $request->unidad_id,
                    'fecha_inicio' => now(),
                    'vigente' => true,
                ]);
                Log::info("Unidad asignada");
            }

            return redirect()->route('admin.operadores.index')->with('success', 'Operador creado correctamente.');
        } catch (\Exception $e) {
            Log::error('Excepción: ' . $e->getMessage());
            return back()->withErrors('Error interno: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        Log::info("=== OperadorController@edit ID: $id ===");
        try {
            $operador = Operador::findOrFail($id);
            $unidades = Unidad::where('activo', true)->get();
            $unidadActual = $operador->asignacionVigente->unidad ?? null;
            Log::debug("Operador cargado para edición: {$operador->nombre_completo}");
            return view('admin.operadores.edit', compact('operador', 'unidades', 'unidadActual'));
        } catch (\Exception $e) {
            Log::error("Error en edit ID $id: " . $e->getMessage());
            throw $e;
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('=== OperadorController@update ===');
        Log::info('ID a actualizar: ' . $id);
        Log::info('Datos recibidos: ', $request->all());

        $operador = Operador::findOrFail($id);

        // Convertir 'activo' de checkbox a booleano ANTES de la validación
        $activo = $request->has('activo');
        $request->merge(['activo' => $activo]);

        $request->validate([
            'clave_operador' => 'required|string|unique:operadores,clave_operador,' . $id,
            'nombre_completo' => 'required|string|max:255',
            'activo' => 'boolean',
            'unidad_id' => 'nullable|exists:unidades,id',
        ]);

        Log::info('Validación pasada, activo = ' . ($activo ? 'true' : 'false'));

        $operador->update([
            'clave_operador' => $request->clave_operador,
            'nombre_completo' => $request->nombre_completo,
            'activo' => $activo,
        ]);

        // Manejo de asignación de unidad
        if ($request->filled('unidad_id')) {
            $asignacionVigente = $operador->asignacionVigente;
            if ($asignacionVigente && $asignacionVigente->unidad_id != $request->unidad_id) {
                $asignacionVigente->update(['fecha_fin' => now(), 'vigente' => false]);
                AsignacionOperadorUnidad::create([
                    'operador_id' => $operador->id,
                    'unidad_id' => $request->unidad_id,
                    'fecha_inicio' => now(),
                    'vigente' => true,
                ]);
            } elseif (!$asignacionVigente) {
                AsignacionOperadorUnidad::create([
                    'operador_id' => $operador->id,
                    'unidad_id' => $request->unidad_id,
                    'fecha_inicio' => now(),
                    'vigente' => true,
                ]);
            }
        } else {
            $asignacionVigente = $operador->asignacionVigente;
            if ($asignacionVigente) {
                $asignacionVigente->update(['fecha_fin' => now(), 'vigente' => false]);
            }
        }

        return redirect()->route('admin.operadores.index')
            ->with('success', 'Operador actualizado correctamente.');
    }

    public function destroy($id)
    {
        $operador = Operador::findOrFail($id);
        AsignacionOperadorUnidad::where('operador_id', $id)->where('vigente', true)
            ->update(['fecha_fin' => now(), 'vigente' => false]);
        $operador->delete();

        return redirect()->route('admin.operadores.index')
            ->with('success', 'Operador eliminado correctamente.');
    }
}
