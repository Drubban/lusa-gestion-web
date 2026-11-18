<?php

namespace App\Http\Requests;

use App\Enums\DispositivoRevision;
use App\Enums\EstadoRevision;
use App\Models\Unidad;
use Illuminate\Validation\Rule;

class RevisionRequest extends FormularioBase
{
    protected function hashids(): array
    {
        return ['unidad_id' => Unidad::class];
    }

    public function rules(): array
    {
        return [
            'disp' => ['required', Rule::enum(DispositivoRevision::class)],
            'unidad_id' => ['required', 'integer', 'exists:unidades,id'],
            'fecha_reporte' => ['required', 'date', 'before_or_equal:today'],
            'hora_entrada' => ['required', 'date_format:H:i'],
            'hora_salida' => ['nullable', 'date_format:H:i'],
            'ruta' => ['nullable', 'string', 'max:255'],
            'problema' => ['required', 'string', 'max:3000'],
            'solucion' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', Rule::enum(EstadoRevision::class)],
            'responsable' => ['nullable', 'string', 'max:255'],
        ];
    }
}
