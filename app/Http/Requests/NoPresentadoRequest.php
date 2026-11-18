<?php

namespace App\Http\Requests;

use App\Enums\MotivoNoPresentado;
use Illuminate\Validation\Rule;

class NoPresentadoRequest extends FormularioBase
{
    public function rules(): array
    {
        return [
            'motivo' => ['required', Rule::enum(MotivoNoPresentado::class)],
            'fecha_reprogramada' => ['nullable', 'date', 'after_or_equal:today'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
