<?php

namespace App\Http\Requests;

use App\Models\Unidad;

class AgendamientoRequest extends FormularioBase
{
    protected function hashids(): array
    {
        return ['unidad_id' => Unidad::class];
    }

    public function rules(): array
    {
        return [
            'unidad_id' => ['required', 'integer', 'exists:unidades,id'],
            'fecha_agendada' => ['required', 'date', 'after_or_equal:today'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
