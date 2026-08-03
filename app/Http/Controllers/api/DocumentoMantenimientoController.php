<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentoMantenimiento;
use App\Models\AsignacionOperadorUnidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentoMantenimientoController extends Controller
{
    public function store(Request $request)
    {
        Log::info('API Mantenimiento - Datos recibidos:', $request->all());

        try {
            $request->validate([
                'asignacion_id' => 'required|exists:asignacion_operador_unidad,id',
                'rol' => 'required|string',
                'tecnologia' => 'required|array',
                'fecha' => 'required|date',
                'hora' => 'required',
                'firma_operador' => 'required',
                'firma_ing' => 'required',
                'firma_tabulacion' => 'required',
            ]);

            $documento = DocumentoMantenimiento::create([
                'asignacion_id' => $request->asignacion_id,
                'rol' => $request->rol,
                'tecnologia_reportada' => implode(',', $request->tecnologia),
                'prueba_barras' => $request->prueba_barras,
                'comentarios' => $request->comentarios,
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'veces_adeudo' => $request->veces_adeudo ?? 0,
                'observaciones_adeudo' => $request->observaciones_adeudo,
                'vigente' => $request->vigente ?? true,
            ]);

            return response()->json(['message' => 'Documento creado', 'id' => $documento->id], 201);
        } catch (\Exception $e) {
            Log::error('Error en API mantenimiento: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}