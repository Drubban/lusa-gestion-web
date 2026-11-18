<?php

namespace App\Http\Requests;

use App\Models\Unidad;

class AgendarMasivoRequest extends FormularioBase
{
    protected function hashids(): array
    {
        return ['unidades' => Unidad::class];
    }

    public function rules(): array
    {
        return [
            'unidades' => ['required', 'array', 'min:1', 'max:500'],
            'unidades.*' => ['integer', 'exists:unidades,id'],
            'fecha_agendada' => ['required', 'date', 'after_or_equal:today'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
