<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RevisionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hashid,
            'disp' => $this->disp?->value,
            'disp_etiqueta' => $this->disp?->etiqueta(),
            'fecha_reporte' => $this->fecha_reporte?->toDateString(),
            'hora_entrada' => $this->hora_entrada ? substr($this->hora_entrada, 0, 5) : null,
            'hora_salida' => $this->hora_salida ? substr($this->hora_salida, 0, 5) : null,
            'ruta' => $this->ruta,
            'problema' => $this->problema,
            'solucion' => $this->solucion,
            'status' => $this->status?->value,
            'status_etiqueta' => $this->status?->etiqueta(),
            'tiempo_estancia' => $this->tiempo_estancia,
            'responsable' => $this->responsable,
            'unidad' => $this->whenLoaded('unidad', fn () => new UnidadResource($this->unidad)),
        ];
    }
}
