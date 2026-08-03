<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoDepartamento;
use App\Models\Departamento;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    public function index(Request $request)
    {
        $query = MovimientoDepartamento::with(['unidad', 'departamento', 'usuario']);

        // Filtro por tipo (entrada/salida)
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por fecha (YYYY-MM-DD)
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_hora', $request->fecha);
        }

        // Filtro por número económico de unidad
        if ($request->filled('unidad')) {
            $query->whereHas('unidad', function ($q) use ($request) {
                $q->where('numero_economico', 'LIKE', '%' . $request->unidad . '%');
            });
        }

        // Filtro por clave de operador (se busca a través de la asignación vigente a la fecha del movimiento)
        // Para simplificar, podemos buscar en la tabla asignaciones relacionando con la unidad y la fecha
        if ($request->filled('clave_operador')) {
            $clave = $request->clave_operador;
            $query->whereExists(function ($q) use ($clave) {
                $q->select(DB::raw(1))
                    ->from('asignacion_operador_unidad as a')
                    ->join('operadores as o', 'o.id', '=', 'a.operador_id')
                    ->whereRaw('a.unidad_id = movimiento_departamento.unidad_id')
                    ->where('o.clave_operador', 'LIKE', "%{$clave}%")
                    ->whereRaw('a.fecha_inicio <= movimiento_departamento.fecha_hora')
                    ->where(function ($sub) {
                        $sub->whereNull('a.fecha_fin')
                            ->orWhereRaw('a.fecha_fin >= movimiento_departamento.fecha_hora');
                    });
            });
        }

        $movimientos = $query->orderBy('fecha_hora', 'desc')->paginate(20);
        $departamentos = Departamento::all();
        
        return view('admin.movimientos.index', compact('movimientos', 'departamentos'));
    }

    public function show($id)
    {
        $movimiento = MovimientoDepartamento::with(['unidad', 'departamento', 'usuario'])->findOrFail($id);
        // Obtener operador asignado a la unidad en la fecha del movimiento
        $operador = $this->getOperadorEnFecha($movimiento->unidad_id, $movimiento->fecha_hora);
        return view('admin.movimientos.show', compact('movimiento', 'operador'));
    }

    public function edit($id)
    {
        $movimiento = MovimientoDepartamento::findOrFail($id);
        $departamentos = Departamento::all();
        $unidades = Unidad::where('activo', true)->get();
        return view('admin.movimientos.edit', compact('movimiento', 'departamentos', 'unidades'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'unidad_id' => 'required|exists:unidades,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'tipo' => 'required|in:entrada,salida',
            'fecha_hora' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $movimiento = MovimientoDepartamento::findOrFail($id);
        $movimiento->update($request->only(['unidad_id', 'departamento_id', 'tipo', 'fecha_hora', 'observaciones']));

        return redirect()->route('admin.movimientos.index')->with('success', 'Movimiento actualizado correctamente.');
    }

    public function destroy($id)
    {
        $movimiento = MovimientoDepartamento::findOrFail($id);
        $movimiento->delete();
        return redirect()->route('admin.movimientos.index')->with('success', 'Movimiento eliminado.');
    }

    /**
     * Obtener el operador asignado a una unidad en una fecha determinada.
     */
    private function getOperadorEnFecha($unidadId, $fechaHora)
    {
        return DB::table('asignacion_operador_unidad as a')
            ->join('operadores as o', 'o.id', '=', 'a.operador_id')
            ->where('a.unidad_id', $unidadId)
            ->where('a.fecha_inicio', '<=', $fechaHora)
            ->where(function ($q) use ($fechaHora) {
                $q->whereNull('a.fecha_fin')
                  ->orWhere('a.fecha_fin', '>=', $fechaHora);
            })
            ->select('o.id', 'o.nombre_completo', 'o.clave_operador')
            ->first();
    }
}