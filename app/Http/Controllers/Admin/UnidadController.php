<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use App\Models\Operador;
use App\Models\AsignacionOperadorUnidad;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Zona;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnidadController extends Controller
{
    public function index(Request $request)
    {
        Log::info('=== UnidadController@index ===');

        // Obtener parámetros de filtro y orden
        $search = $request->input('search', '');
        // $estado = $request->input('estado', '');
        $zona = $request->input('zona', '');
        $sort = $request->input('sort', 'numero_economico');
        $direction = $request->input('direction', 'asc');

        // Construir la consulta base
        $query = Unidad::with(['zona', 'asignacionVigente.operador']);

        // Aplicar filtro de búsqueda (número económico, nombre, operador)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_economico', 'LIKE', "%{$search}%")
                    ->orWhere('nombre_unidad', 'LIKE', "%{$search}%")
                    ->orWhereHas('asignacionVigente.operador', function ($q2) use ($search) {
                        $q2->where('nombre_completo', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Aplicar filtro por estado
        // if ($estado !== '') {
        //     $query->where('activo', $estado == 'activo' ? 1 : 0);
        // }

        // Aplicar filtro por zona
        if (!empty($zona)) {
            $query->whereHas('zona', function ($q) use ($zona) {
                $q->where('nombre', $zona);
            });
        }

        // Aplicar ordenamiento
        switch ($sort) {
            case 'numero_economico':
                $query->orderBy('numero_economico', $direction);
                break;
            case 'nombre_unidad':
                $query->orderBy('nombre_unidad', $direction);
                break;
            case 'zona':
                $query->leftJoin('zonas', 'unidades.zona_id', '=', 'zonas.id')
                    ->orderBy('zonas.nombre', $direction)
                    ->select('unidades.*');
                break;
            case 'operador':
                $query->leftJoin('asignacion_operador_unidad', function ($join) {
                    $join->on('unidades.id', '=', 'asignacion_operador_unidad.unidad_id')
                        ->where('asignacion_operador_unidad.vigente', true);
                })
                    ->leftJoin('operadores', 'asignacion_operador_unidad.operador_id', '=', 'operadores.id')
                    ->orderBy('operadores.nombre_completo', $direction)
                    ->select('unidades.*');
                break;
            default:
                $query->orderBy('numero_economico', $direction);
                break;
        }

        $unidades = $query->paginate(15);

        // Mantener los parámetros en la paginación
        $unidades->appends([
            'search' => $search,
            // 'estado' => $estado,
            'zona' => $zona,
            'sort' => $sort,
            'direction' => $direction
        ]);

        $zonas = \App\Models\Zona::all();

        return view('admin.unidades.index', compact('unidades', 'search', 'zona', 'sort', 'direction', 'zonas'));
    }

    public function show($id)
    {
        $unidad = Unidad::with([
            'zona',
            'asignaciones.operador',
            'movimientos'  // ← Agregar esta relación
        ])->findOrFail($id);

        $operadorActual = $unidad->asignacionVigente->operador ?? null;

        // También puedes obtener los movimientos ordenados
        $movimientos = $unidad->movimientos()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.unidades.show', compact('unidad', 'operadorActual', 'movimientos'));
    }

    public function create()
    {
        $zonas = Zona::all();  // Obtener todas las zonas
        $operadores = Operador::where('activo', true)->get();
        return view('admin.unidades.create', compact('zonas', 'operadores'));
    }

    public function store(Request $request)
    {
        Log::info('=== INTENTO DE CREAR UNIDAD ===');
        Log::info('Datos recibidos:', $request->all());

        try {
            // Cambiar la validación de 'activo'
            $validated = $request->validate([
                'numero_economico' => 'required|string|max:20|unique:unidades,numero_economico',
                'nombre_unidad' => 'nullable|string|max:255',
                'zona_id' => 'required|exists:zonas,id',
                'activo' => 'sometimes|boolean', // ← Cambiar a 'sometimes|boolean'
                'operador_id' => 'nullable|exists:operadores,id',
            ]);

            Log::info('✅ Validación pasada correctamente');

            // Crear la unidad - el checkbox envía 'on' o null
            $unidad = Unidad::create([
                'numero_economico' => $validated['numero_economico'],
                'nombre_unidad' => $validated['nombre_unidad'] ?? null,
                'zona_id' => $validated['zona_id'],
                'codigo_qr' => (string) \Illuminate\Support\Str::uuid(),
                'token_qr' => \Illuminate\Support\Str::random(20),
                'activo' => $request->has('activo'), // ← Esto convierte 'on' a true
            ]);

            Log::info('✅ Unidad creada con ID: ' . $unidad->id);

            // Asignar operador si se selecciona
            if ($request->filled('operador_id')) {
                // Finalizar asignaciones anteriores del operador
                AsignacionOperadorUnidad::where('operador_id', $request->operador_id)
                    ->where('vigente', true)
                    ->update(['fecha_fin' => now(), 'vigente' => false]);

                AsignacionOperadorUnidad::create([
                    'operador_id' => $request->operador_id,
                    'unidad_id' => $unidad->id,
                    'fecha_inicio' => now(),
                    'vigente' => true,
                ]);
            }

            return redirect()->route('admin.unidades.index')
                ->with('success', 'Unidad creada correctamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Error de validación:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('❌ Error al crear unidad: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear la unidad: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $unidad = Unidad::findOrFail($id);
        $zonas = Zona::all();                                 // ← nuevo
        $operadores = Operador::where('activo', true)->get();
        $operadorActual = $unidad->asignacionVigente->operador ?? null;
        return view('admin.unidades.edit', compact('unidad', 'zonas', 'operadores', 'operadorActual'));
    }

    public function update(Request $request, $id)
    {
        Log::info('=== UnidadController@update ===');
        Log::info('ID a actualizar: ' . $id);
        Log::info('Datos recibidos: ', $request->all());

        $unidad = Unidad::findOrFail($id);
        Log::info('Unidad encontrada: ' . $unidad->numero_economico);

        $validator = validator($request->all(), [
            'numero_economico' => 'required|string|max:20|unique:unidades,numero_economico,' . $id,
            'nombre_unidad' => 'nullable|string|max:255',
            'zona_id' => 'required|exists:zonas,id',
            'activo' => 'sometimes|boolean',
            'operador_id' => 'nullable|exists:operadores,id',
        ]);

        if ($validator->fails()) {
            Log::error('Validación fallida: ' . json_encode($validator->errors()->all()));
            return back()->withErrors($validator)->withInput();
        }

        Log::info('Validación pasada correctamente');

        try {
            // Convertir activo a booleano
            $activo = $request->has('activo');

            $unidad->update([
                'numero_economico' => $request->numero_economico,
                'nombre_unidad' => $request->nombre_unidad,
                'zona_id' => $request->zona_id,
                'activo' => $activo,
            ]);

            Log::info('Unidad actualizada en BD');

            // Manejo de asignación de operador
            if ($request->filled('operador_id')) {
                Log::info('Asignando operador ID: ' . $request->operador_id);

                $asignacionVigente = $unidad->asignacionVigente;

                if ($asignacionVigente && $asignacionVigente->operador_id != $request->operador_id) {
                    $asignacionVigente->update(['fecha_fin' => now(), 'vigente' => false]);
                    Log::info('Asignación anterior finalizada');

                    AsignacionOperadorUnidad::where('operador_id', $request->operador_id)
                        ->where('vigente', true)
                        ->update(['fecha_fin' => now(), 'vigente' => false]);

                    AsignacionOperadorUnidad::create([
                        'operador_id' => $request->operador_id,
                        'unidad_id' => $unidad->id,
                        'fecha_inicio' => now(),
                        'vigente' => true,
                    ]);
                    Log::info('Nueva asignación creada');
                } elseif (!$asignacionVigente) {
                    AsignacionOperadorUnidad::where('operador_id', $request->operador_id)
                        ->where('vigente', true)
                        ->update(['fecha_fin' => now(), 'vigente' => false]);

                    AsignacionOperadorUnidad::create([
                        'operador_id' => $request->operador_id,
                        'unidad_id' => $unidad->id,
                        'fecha_inicio' => now(),
                        'vigente' => true,
                    ]);
                    Log::info('Asignación creada por primera vez');
                }
            } else {
                $asignacionVigente = $unidad->asignacionVigente;
                if ($asignacionVigente) {
                    $asignacionVigente->update(['fecha_fin' => now(), 'vigente' => false]);
                    Log::info('Asignación vigente finalizada por falta de operador');
                }
            }

            Log::info('=== UNIDAD ACTUALIZADA CON ÉXITO ===');

            return redirect()->route('admin.unidades.index')
                ->with('success', 'Unidad actualizada correctamente.');
        } catch (\Exception $e) {
            Log::error('ERROR en update: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->withErrors('Error interno: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $unidad = Unidad::findOrFail($id);
            Log::info('Intentando eliminar unidad ID: ' . $id . ' - ' . $unidad->numero_economico);

            // Verificar si tiene asignaciones activas
            $asignacionesActivas = AsignacionOperadorUnidad::where('unidad_id', $id)
                ->where('vigente', true)
                ->count();

            if ($asignacionesActivas > 0) {
                // Opción 1: Finalizar las asignaciones activas
                AsignacionOperadorUnidad::where('unidad_id', $id)
                    ->where('vigente', true)
                    ->update([
                        'fecha_fin' => now(),
                        'vigente' => false
                    ]);

                Log::info('Asignaciones activas finalizadas: ' . $asignacionesActivas);
            }

            // Verificar si tiene asignaciones históricas
            $asignacionesHistoricas = AsignacionOperadorUnidad::where('unidad_id', $id)->count();

            if ($asignacionesHistoricas > 0) {
                // Opción 2: Eliminar TODAS las asignaciones (históricas y activas)
                // Esto permite eliminar la unidad limpia
                AsignacionOperadorUnidad::where('unidad_id', $id)->delete();
                Log::info('Asignaciones históricas eliminadas: ' . $asignacionesHistoricas);
            }

            // Ahora eliminar la unidad
            $unidad->delete();

            DB::commit();

            Log::info('✅ Unidad eliminada exitosamente ID: ' . $id);

            return redirect()->route('admin.unidades.index')
                ->with('success', "Unidad {$unidad->numero_economico} eliminada exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error al eliminar unidad ID ' . $id . ': ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->withErrors(['error' => 'Error al eliminar la unidad: ' . $e->getMessage()]);
        }
    }

    // Método para regenerar el token QR (opcional)
    public function regenerarToken($id)
    {
        $unidad = Unidad::findOrFail($id);
        $unidad->update(['token_qr' => Str::random(20)]);
        return redirect()->route('admin.unidades.show', $unidad)
            ->with('success', 'Token QR regenerado.');
    }
}
