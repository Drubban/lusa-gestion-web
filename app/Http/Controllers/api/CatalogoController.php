<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AsignacionOperadorUnidad;
use App\Models\Operador;
use App\Models\Unidad;

class CatalogoController extends Controller
{
    public function operadores()
    {
        $operadores = Operador::where('activo', true)
            ->with('asignacionVigente.unidad')
            ->get(['id', 'nombre_completo', 'clave_operador']);

        return response()->json($operadores);
    }

    public function unidades()
    {
        $unidades = Unidad::where('activo', true)
            ->with('zona')
            ->get(['id', 'numero_economico', 'nombre_unidad']);

        return response()->json($unidades);
    }

    public function asignaciones()
    {
        $asignaciones = AsignacionOperadorUnidad::with(['operador', 'unidad'])
            ->where('vigente', true)
            ->get(['id', 'operador_id', 'unidad_id']);

        return response()->json($asignaciones);
    }
}
