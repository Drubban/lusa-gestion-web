<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentoCapacitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentoCapacitacionController extends Controller
{
    public function store(Request $request)
    {
        Log::info('API Capacitación - Datos recibidos:', $request->all());

        try {
            $request->validate([
                'asignacion_id' => 'required|exists:asignacion_operador_unidad,id',
                'zona' => 'required|string',
                'fecha' => 'required|date',
                'hora' => 'required',
                'firma_operador' => 'required|string',
                'firma_ing' => 'required|string',
            ]);

            $documento = DocumentoCapacitacion::create([
                'asignacion_id' => $request->asignacion_id,
                'zona' => $request->zona,
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'vigente' => true,
                'firma_operador' => $request->firma_operador,
                'firma_ing' => $request->firma_ing,
            ]);

            return response()->json(['message' => 'Documento creado', 'id' => $documento->id], 201);
        } catch (\Exception $e) {
            Log::error('Error en API capacitación: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}