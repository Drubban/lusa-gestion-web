<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovimientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hashid,
            'uuid' => $this->uuid,
            'tipo' => $this->tipo->value,
            'fecha_hora' => $this->fecha_hora?->toIso8601String(),
            'duracion_minutos' => $this->duracion_minutos,
            'observaciones' => $this->observaciones,
            'unidad' => $this->whenLoaded('unidad', fn () => new UnidadResource($this->unidad)),
            'departamento' => $this->whenLoaded('departamento', fn () => new DepartamentoResource($this->departamento)),
            'registrado_por' => $this->whenLoaded('usuario', fn () => $this->usuario?->nombre_visible),
        ];
    }
}
