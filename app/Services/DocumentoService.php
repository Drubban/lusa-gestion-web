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
     * Crear un documento de mantenimiento.
     * Mapea los nombres del request a las columnas reales de la tabla.
     */
    public function crearDocumentoMantenimiento(array $data): DocumentoMantenimiento
    {
        Log::info('=== SERVICE LLAMADO ===');
        Log::info('Datos del Service:', $data);
        Log::info('fecha_mantenimiento:', ['valor' => $data['fecha_mantenimiento'] ?? 'NO EXISTE']);
        try {
            DB::beginTransaction();

            $asignacion = AsignacionOperadorUnidad::where('operador_id', $data['operador_id'])
                ->where('unidad_id', $data['unidad_id'])
                ->where('vigente', true)
                ->first();

            if (!$asignacion) {
                throw new Exception('El operador no esta asignado actualmente a esta unidad');
            }

            // ============ LOGS DE DIAGNOSTICO ============
            Log::info('=== DIAGNOSTICO MANTENIMIENTO ===');
            Log::info('$data recibido:', $data);
            Log::info('$fillable del modelo:', (new DocumentoMantenimiento)->getFillable());

            $payload = [
                'unidad_id'             => $data['unidad_id'],
                'operador_id'           => $data['operador_id'],
                'asignacion_id'         => $asignacion->id,
                'fecha'                 => $data['fecha_mantenimiento'] ?? null,
                'hora'                  => $data['hora_mantenimiento'] ?? now()->format('H:i'),
                'rol'                   => $data['rol'] ?? $data['tipo_mantenimiento'] ?? null,
                'comentarios'           => $data['descripcion'] ?? $data['comentarios'] ?? null,
                'tecnologia_reportada'  => $data['tecnologia_reportada'] ?? null,
                'prueba_barras'         => $data['prueba_barras'] ?? null,
                'veces_adeudo'          => $data['veces_adeudo'] ?? 0,
                'observaciones_adeudo'  => $data['observaciones_adeudo'] ?? $data['observaciones'] ?? null,
                'vigente'               => true,
                'firma_operador'        => $data['firma_operador'] ?? null,
                'firma_ing'             => $data['firma_ing'] ?? null,
                'firma_tabulacion'      => $data['firma_tabulacion'] ?? null,
            ];
            Log::info('$payload a insertar:', $payload);
            // ============================================

            $documento = DocumentoMantenimiento::create($payload);

            DB::commit();
            return $documento;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear documento de mantenimiento: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crear un documento de capacitacion.
     */
    public function crearDocumentoCapacitacion(array $data): DocumentoCapacitacion
    {
        try {
            DB::beginTransaction();

            $asignacion = AsignacionOperadorUnidad::where('operador_id', $data['operador_id'])
                ->where('vigente', true)
                ->first();

            $documento = DocumentoCapacitacion::create([
                'operador_id'         => $data['operador_id'],
                'unidad_id'           => $data['unidad_id']
                    ?? $asignacion?->unidad_id
                    ?? null,
                'asignacion_id'       => $asignacion?->id ?? null,

                'tipo_capacitacion'   => $data['tipo_capacitacion'] ?? null,
                'fecha_capacitacion'  => $data['fecha_capacitacion'],
                'fecha_vencimiento'   => $data['fecha_vencimiento'],
                'instructor'          => $data['instructor'] ?? null,
                'duracion_horas'      => $data['duracion_horas'] ?? null,
                'observaciones'       => $data['observaciones'] ?? null,

                // Firmas (si las agregas al modelo en el futuro)
                'firma_operador'      => $data['firma_operador'] ?? null,
                'firma_instructor'    => $data['firma_instructor'] ?? null,
            ]);

            DB::commit();
            return $documento;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear documento de capacitacion: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAsignacionActual(int $operadorId): ?AsignacionOperadorUnidad
    {
        return AsignacionOperadorUnidad::where('operador_id', $operadorId)
            ->where('vigente', true)
            ->first();
    }

    public function verificarAsignacion(int $operadorId, int $unidadId): bool
    {
        return AsignacionOperadorUnidad::where('operador_id', $operadorId)
            ->where('unidad_id', $unidadId)
            ->where('vigente', true)
            ->exists();
    }
}
