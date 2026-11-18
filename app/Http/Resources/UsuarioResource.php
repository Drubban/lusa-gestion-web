<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hashid,
            'nombre_usuario' => $this->nombre_usuario,
            'nombre' => $this->nombre_visible,
            'rol' => $this->rol?->value,
            'departamento' => $this->departamento ? new DepartamentoResource($this->departamento) : null,
            'puede_generar_documentos' => (bool) $this->puede_generar_documentos,
            'habilidades' => $this->habilidadesApp(),
        ];
    }
}
