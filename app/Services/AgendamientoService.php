<?php

namespace App\Services;

use App\Enums\EstadoAgendamiento;
use App\Enums\MotivoNoPresentado;
use App\Exceptions\ReglaNegocioException;
use App\Models\AgendamientoMantenimiento;
use App\Models\Unidad;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Reglas de agendamiento de mantenimiento. Regla central: una unidad solo puede
 * tener un agendamiento pendiente a la vez.
 */
class AgendamientoService
{
    public function agendar(Unidad $unidad, string $fecha, ?string $observaciones = null): AgendamientoMantenimiento
    {
        $pendiente = $unidad->agendamientoPendiente()->first();
        if ($pendiente) {
            throw new ReglaNegocioException(
                "La unidad {$unidad->numero_economico} ya tiene mantenimiento agendado para el {$pendiente->fecha_agendada->format('d/m/Y')}.",
            );
        }

        return $this->crearPendiente($unidad->id, $fecha, $observaciones);
    }

    /**
     * Agenda varias unidades. Si la unidad tiene un pendiente ya vencido, se marca
     * como no cumplido y se reagenda; si esta vigente, se omite.
     *
     * @return array{agendadas: int, omitidas: int}
     */
    public function agendarMasivo(array $unidadIds, string $fecha, ?string $observaciones = null): array
    {
        $agendadas = 0;
        $omitidas = 0;

        DB::transaction(function () use ($unidadIds, $fecha, $observaciones, &$agendadas, &$omitidas) {
            $pendientes = AgendamientoMantenimiento::pendientes()->whereIn('unidad_id', $unidadIds)->get()->keyBy('unidad_id');

            foreach ($unidadIds as $unidadId) {
                $pendiente = $pendientes->get($unidadId);
                if ($pendiente && ! $pendiente->fecha_agendada->isPast()) {
                    $omitidas++;
                    continue;
                }
                if ($pendiente) {
                    $pendiente->update([
                        'estado' => EstadoAgendamiento::NoCumplido,
                        'observaciones' => $this->anexar($pendiente->observaciones, 'Vencido, reagendado automaticamente'),
                        'reportado_por' => Auth::id(),
                    ]);
                }
                $this->crearPendiente($unidadId, $fecha, $observaciones ?: 'Agendamiento masivo');
                $agendadas++;
            }
        });

        return compact('agendadas', 'omitidas');
    }

    public function marcarCumplido(AgendamientoMantenimiento $agendamiento, ?string $nota = null): void
    {
        $agendamiento->update([
            'estado' => EstadoAgendamiento::Cumplido,
            'fecha_cumplimiento' => now()->toDateString(),
            'observaciones' => $nota ? $this->anexar($agendamiento->observaciones, $nota) : $agendamiento->observaciones,
        ]);
        BitacoraService::registrar('agendamiento.cumplido', $agendamiento);
    }

    /** Se llama al registrar un documento de mantenimiento para cerrar lo pendiente. */
    public function cumplirPendientesDeUnidad(int $unidadId): int
    {
        return AgendamientoMantenimiento::pendientes()->where('unidad_id', $unidadId)->update([
            'estado' => EstadoAgendamiento::Cumplido->value,
            'fecha_cumplimiento' => now()->toDateString(),
            'updated_at' => now(),
        ]);
    }

    public function reportarNoPresentado(
        AgendamientoMantenimiento $agendamiento,
        MotivoNoPresentado $motivo,
        ?string $fechaReprogramada = null,
        ?string $observaciones = null,
    ): ?AgendamientoMantenimiento {
        return DB::transaction(function () use ($agendamiento, $motivo, $fechaReprogramada, $observaciones) {
            $agendamiento->update([
                'estado' => EstadoAgendamiento::NoCumplido,
                'motivo_no_presentado' => $motivo->value,
                'fecha_reprogramada' => $fechaReprogramada,
                'reportado_por' => Auth::id(),
                'observaciones' => $this->anexar($agendamiento->observaciones, $observaciones ?: 'No presentado: ' . $motivo->etiqueta()),
            ]);
            BitacoraService::registrar('agendamiento.no_presentado', $agendamiento, ['motivo' => $motivo->value]);

            return $fechaReprogramada
                ? $this->crearPendiente($agendamiento->unidad_id, $fechaReprogramada, "Reprogramado. Motivo: {$motivo->etiqueta()}")
                : null;
        });
    }

    public function reagendar(AgendamientoMantenimiento $agendamiento, string $nuevaFecha, ?string $motivo = null): AgendamientoMantenimiento
    {
        if (Carbon::parse($nuevaFecha)->isBefore(today())) {
            throw new ReglaNegocioException('La nueva fecha no puede estar en el pasado.');
        }

        return DB::transaction(function () use ($agendamiento, $nuevaFecha, $motivo) {
            $agendamiento->update([
                'estado' => EstadoAgendamiento::Reagendado,
                'fecha_reprogramada' => $nuevaFecha,
                'observaciones' => $this->anexar($agendamiento->observaciones, 'Reagendado para ' . Carbon::parse($nuevaFecha)->format('d/m/Y') . ($motivo ? ". Motivo: {$motivo}" : '')),
            ]);

            return $this->crearPendiente($agendamiento->unidad_id, $nuevaFecha, $motivo ? "Reagendado. Motivo: {$motivo}" : 'Reagendado');
        });
    }

    private function crearPendiente(int $unidadId, string $fecha, ?string $observaciones): AgendamientoMantenimiento
    {
        return AgendamientoMantenimiento::create([
            'unidad_id' => $unidadId,
            'fecha_agendada' => $fecha,
            'estado' => EstadoAgendamiento::Pendiente,
            'observaciones' => $observaciones,
            'created_by' => Auth::id(),
        ]);
    }

    private function anexar(?string $actual, string $nuevo): string
    {
        return trim(($actual ? $actual . "\n" : '') . $nuevo);
    }
}
