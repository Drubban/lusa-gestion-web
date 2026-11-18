<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnidadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hashid,
            'numero_economico' => $this->numero_economico,
            'nombre' => $this->nombre_unidad,
            'etiqueta' => $this->etiqueta,
            'zona' => $this->whenLoaded('zona', fn () => $this->zona?->nombre),
            'activo' => (bool) $this->activo,
            'operador' => $this->whenLoaded('asignacionVigente', fn () => $this->asignacionVigente?->operador ? new OperadorResource($this->asignacionVigente->operador) : null),
            'tecnologias' => $this->whenLoaded('tecnologias', fn () => $this->tecnologias->map(fn ($t) => $t->tipo->value)->values()),
        ];
    }
}
