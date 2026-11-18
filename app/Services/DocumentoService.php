<?php

namespace App\Services;

use App\Models\DocumentoCapacitacion;
use App\Models\DocumentoMantenimiento;
use App\Models\Unidad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Alta y edicion de documentos (mantenimiento y capacitacion) para panel y app.
 * Los datos llegan ya validados por su FormRequest con los ids decodificados.
 */
class DocumentoService
{
    public function __construct(private readonly AgendamientoService $agendamientos) {}

    public function guardarMantenimiento(array $datos, ?DocumentoMantenimiento $documento = null): DocumentoMantenimiento
    {
        if (! $documento && ! empty($datos['uuid']) && $existente = DocumentoMantenimiento::where('uuid', $datos['uuid'])->first()) {
            return $existente;
        }

        return DB::transaction(function () use ($datos, $documento) {
            $unidad = Unidad::with('asignacionVigente')->findOrFail($datos['unidad_id']);

            $atributos = [
                'unidad_id' => $unidad->id,
                // Si no se envia operador se toma el asignado a la unidad en este momento
                'operador_id' => $datos['operador_id'] ?? $unidad->asignacionVigente?->operador_id,
                'tecnologia_reportada' => implode(',', $datos['tecnologias'] ?? []),
                'estado_camaras' => $this->resumenCamaras($datos['camaras'] ?? []),
                'prueba_barras' => $datos['prueba_barras'] ?? null,
                'comentarios' => $datos['comentarios'] ?? null,
                'fecha' => $datos['fecha'],
                'hora' => $datos['hora'],
                'veces_adeudo' => $datos['veces_adeudo'] ?? 0,
                'observaciones_adeudo' => $datos['observaciones_adeudo'] ?? null,
                'vigente' => $datos['vigente'] ?? true,
            ];

            if ($documento) {
                $documento->update($atributos);
            } else {
                $documento = DocumentoMantenimiento::create($atributos + [
                    'uuid' => $datos['uuid'] ?? null,
                    'usuario_id' => Auth::id(),
                ]);
                $this->agendamientos->cumplirPendientesDeUnidad($unidad->id);
            }

            foreach (array_keys(DocumentoMantenimiento::FIRMAS) as $tipo) {
                $documento->guardarFirma($tipo, $datos['firmas'][$tipo] ?? null);
            }

            BitacoraService::registrar('documento_mantenimiento.guardado', $documento);

            return $documento;
        });
    }

    public function guardarCapacitacion(array $datos, ?DocumentoCapacitacion $documento = null): DocumentoCapacitacion
    {
        if (! $documento && ! empty($datos['uuid']) && $existente = DocumentoCapacitacion::where('uuid', $datos['uuid'])->first()) {
            return $existente;
        }

        return DB::transaction(function () use ($datos, $documento) {
            $unidad = Unidad::with('asignacionVigente')->findOrFail($datos['unidad_id']);

            $atributos = [
                'unidad_id' => $unidad->id,
                'operador_id' => $datos['operador_id'] ?? $unidad->asignacionVigente?->operador_id,
                'tipo_capacitacion' => $datos['tipo_capacitacion'] ?? null,
                'fecha' => $datos['fecha'],
                'hora' => $datos['hora'],
                'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
                'instructor' => $datos['instructor'] ?? null,
                'duracion_horas' => $datos['duracion_horas'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null,
                'vigente' => $datos['vigente'] ?? true,
            ];

            if ($documento) {
                $documento->update($atributos);
            } else {
                $documento = DocumentoCapacitacion::create($atributos + [
                    'uuid' => $datos['uuid'] ?? null,
                    'usuario_id' => Auth::id(),
                ]);
            }

            foreach (array_keys(DocumentoCapacitacion::FIRMAS) as $tipo) {
                $documento->guardarFirma($tipo, $datos['firmas'][$tipo] ?? null);
            }

            BitacoraService::registrar('documento_capacitacion.guardado', $documento);

            return $documento;
        });
    }

    private function resumenCamaras(array $camaras): ?string
    {
        if (empty($camaras)) {
            return null;
        }
        $ok = array_map(fn ($n) => "Camara {$n} OK", array_map('intval', $camaras));

        return implode(', ', $ok);
    }
}
