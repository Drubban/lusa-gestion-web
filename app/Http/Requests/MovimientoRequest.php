<?php

namespace App\Http\Requests;

use App\Enums\TipoMovimiento;
use App\Models\Departamento;
use App\Models\Unidad;
use Illuminate\Validation\Rule;

/** Correccion manual de un movimiento desde el panel. */
class MovimientoRequest extends FormularioBase
{
    protected function hashids(): array
    {
        return ['unidad_id' => Unidad::class, 'departamento_id' => Departamento::class];
    }

    public function rules(): array
    {
        return [
            'unidad_id' => ['required', 'integer', 'exists:unidades,id'],
            'departamento_id' => ['required', 'integer', 'exists:departamentos,id'],
            'tipo' => ['required', Rule::enum(TipoMovimiento::class)],
            'fecha_hora' => ['required', 'date', 'before_or_equal:now'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
