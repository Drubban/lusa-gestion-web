<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hashid,
            'fecha_entrega' => $this->fecha_entrega?->toDateString(),
            'categoria' => $this->categoria?->value,
            'categoria_etiqueta' => $this->categoria?->etiqueta(),
            'nombre' => $this->nombre,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'numero_serie' => $this->numero_serie,
            'cantidad' => $this->cantidad,
            'nombre_recibe' => $this->nombre_recibe,
            'clave_empleado' => $this->clave_empleado,
            'area' => $this->area,
            'departamento' => $this->whenLoaded('departamento', fn () => $this->departamento?->nombre),
        ];
    }
}
