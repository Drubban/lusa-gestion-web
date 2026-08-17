<?php

namespace App\Services;

use App\Models\DocumentoMantenimiento;
use App\Models\DocumentoCapacitacion;
use App\Models\AsignacionOperadorUnidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class DocumentoService
{
    /**
     * Crear un documento de mantenimiento
     */
    public function crearDocumentoMantenimiento(array $data): DocumentoMantenimiento
    {
        try {
            DB::beginTransaction();

            // Verificar que el operador y unidad están asignados
            $asignacion = AsignacionOperadorUnidad::where('operador_id', $data['operador_id'])
                ->where('unidad_id', $data['unidad_id'])
                ->where('vigente', true)
                ->first();

            if (!$asignacion) {
                throw new Exception('El operador no está asignado actualmente a esta unidad');
            }

            // Crear el documento con los datos de la asignación
            $documento = DocumentoMantenimiento::create([
                'unidad_id' => $data['unidad_id'],
                'operador_id' => $data['operador_id'],
                'asignacion_id' => $asignacion->id, // Guardamos referencia
                'fecha_mantenimiento' => $data['fecha_mantenimiento'],
                'tipo_mantenimiento' => $data['tipo_mantenimiento'],
                'descripcion' => $data['descripcion'],
                'costo' => $data['costo'] ?? null,
                'kilometraje' => $data['kilometraje'] ?? null,
                'proveedor' => $data['proveedor'] ?? null,
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            DB::commit();
            return $documento;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear documento de mantenimiento: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crear un documento de capacitación
     */
    public function crearDocumentoCapacitacion(array $data): DocumentoCapacitacion
    {
        try {
            DB::beginTransaction();

            // Obtener la asignación actual del operador (si tiene)
            $asignacion = AsignacionOperadorUnidad::where('operador_id', $data['operador_id'])
                ->where('vigente', true)
                ->first();

            $documento = DocumentoCapacitacion::create([
                'operador_id' => $data['operador_id'],
                'unidad_id' => $asignacion?->unidad_id ?? null, // Si no está asignado, queda null
                'asignacion_id' => $asignacion?->id ?? null,
                'tipo_capacitacion' => $data['tipo_capacitacion'],
                'fecha_capacitacion' => $data['fecha_capacitacion'],
                'fecha_vencimiento' => $data['fecha_vencimiento'],
                'instructor' => $data['instructor'] ?? null,
                'duracion_horas' => $data['duracion_horas'] ?? null,
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            DB::commit();
            return $documento;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear documento de capacitación: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener la asignación actual de un operador
     */
    public function getAsignacionActual(int $operadorId): ?AsignacionOperadorUnidad
    {
        return AsignacionOperadorUnidad::where('operador_id', $operadorId)
            ->where('vigente', true)
            ->first();
    }

    /**
     * Verificar si un operador está asignado a una unidad específica
     */
    public function verificarAsignacion(int $operadorId, int $unidadId): bool
    {
        return AsignacionOperadorUnidad::where('operador_id', $operadorId)
            ->where('unidad_id', $unidadId)
            ->where('vigente', true)
            ->exists();
    }
}