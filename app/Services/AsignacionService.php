<?php

namespace App\Services;

use App\Models\AsignacionOperadorUnidad;
use App\Models\Operador;
use App\Models\Unidad;
use Illuminate\Support\Facades\DB;

/**
 * Punto unico para cambiar que operador maneja que unidad.
 * Antes esta logica estaba copiada en UnidadController (x3), OperadorController (x3) e ImportacionController.
 */
class AsignacionService
{
    /**
     * Deja a la unidad con el operador indicado (o sin operador si es null).
     * Cierra la asignacion vigente de la unidad y la del operador en otra unidad.
     */
    public function asignar(Unidad $unidad, ?Operador $operador): void
    {
        $vigente = $unidad->asignacionVigente()->first();
        if ($vigente && $operador && $vigente->operador_id === $operador->id) {
            return;
        }

        DB::transaction(function () use ($unidad, $operador) {
            $this->cerrar(AsignacionOperadorUnidad::where('unidad_id', $unidad->id));

            if (! $operador) {
                return;
            }

            $this->cerrar(AsignacionOperadorUnidad::where('operador_id', $operador->id));

            AsignacionOperadorUnidad::create([
                'operador_id' => $operador->id,
                'unidad_id' => $unidad->id,
                'fecha_inicio' => now()->toDateString(),
                'vigente' => true,
            ]);
        });
    }

    public function liberarOperador(Operador $operador): void
    {
        $this->cerrar(AsignacionOperadorUnidad::where('operador_id', $operador->id));
    }

    public function liberarUnidad(Unidad $unidad): void
    {
        $this->cerrar(AsignacionOperadorUnidad::where('unidad_id', $unidad->id));
    }

    private function cerrar($query): void
    {
        $query->where('vigente', true)->update([
            'vigente' => false,
            'fecha_fin' => now()->toDateString(),
            'updated_at' => now(),
        ]);
    }
}
