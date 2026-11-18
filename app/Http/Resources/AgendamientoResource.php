<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendamientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hashid,
            'fecha_agendada' => $this->fecha_agendada?->toDateString(),
            'dias_restantes' => $this->dias_restantes,
            'estado' => $this->estado?->value,
            'estado_etiqueta' => $this->estado?->etiqueta(),
            'motivo' => $this->motivo_no_presentado,
            'motivo_etiqueta' => $this->motivo_etiqueta,
            'observaciones' => $this->observaciones,
            'unidad' => $this->whenLoaded('unidad', fn () => new UnidadResource($this->unidad)),
        ];
    }
}
