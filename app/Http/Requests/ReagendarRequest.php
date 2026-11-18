<?php

namespace App\Http\Requests;

class ReagendarRequest extends FormularioBase
{
    public function rules(): array
    {
        return [
            'nueva_fecha' => ['required', 'date', 'after_or_equal:today'],
            'motivo' => ['nullable', 'string', 'max:500'],
        ];
    }
}
