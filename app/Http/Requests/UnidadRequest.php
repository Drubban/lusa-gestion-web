<?php

namespace App\Http\Requests;

use App\Models\Operador;
use App\Models\Zona;
use Illuminate\Validation\Rule;

class UnidadRequest extends FormularioBase
{
    protected function hashids(): array
    {
        return ['zona_id' => Zona::class, 'operador_id' => Operador::class];
    }

    protected function booleanos(): array
    {
        return ['activo'];
    }

    public function rules(): array
    {
        return [
            'numero_economico' => ['required', 'string', 'max:20', Rule::unique('unidades')->ignore($this->route('unidad'))],
            'nombre_unidad' => ['nullable', 'string', 'max:255'],
            'zona_id' => ['required', 'integer', 'exists:zonas,id'],
            'operador_id' => ['nullable', 'integer', 'exists:operadores,id'],
            'activo' => ['boolean'],
        ];
    }
}
