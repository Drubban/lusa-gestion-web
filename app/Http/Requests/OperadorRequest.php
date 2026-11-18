<?php

namespace App\Http\Requests;

use App\Models\Unidad;
use Illuminate\Validation\Rule;

class OperadorRequest extends FormularioBase
{
    protected function hashids(): array
    {
        return ['unidad_id' => Unidad::class];
    }

    protected function booleanos(): array
    {
        return ['activo'];
    }

    public function rules(): array
    {
        return [
            'clave_operador' => ['required', 'string', 'max:20', Rule::unique('operadores')->ignore($this->route('operador'))],
            'nombre_completo' => ['required', 'string', 'max:255'],
            'unidad_id' => ['nullable', 'integer', 'exists:unidades,id'],
            'activo' => ['boolean'],
        ];
    }
}
