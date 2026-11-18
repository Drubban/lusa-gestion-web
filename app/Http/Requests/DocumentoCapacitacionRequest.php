<?php

namespace App\Http\Requests;

use App\Models\Operador;
use App\Models\Unidad;
use App\Rules\FirmaPng;

class DocumentoCapacitacionRequest extends FormularioBase
{
    public function authorize(): bool
    {
        $usuario = $this->user();

        return $usuario !== null && ($usuario->accedePanel() || $usuario->tokenCan('documentos:escribir'));
    }

    protected function hashids(): array
    {
        return ['unidad_id' => Unidad::class, 'operador_id' => Operador::class];
    }

    protected function booleanos(): array
    {
        return ['vigente'];
    }

    public function rules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid'],
            'unidad_id' => ['required', 'integer', 'exists:unidades,id'],
            'operador_id' => ['nullable', 'integer', 'exists:operadores,id'],
            'tipo_capacitacion' => ['nullable', 'string', 'max:200'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
            'fecha_vencimiento' => ['nullable', 'date', 'after:fecha'],
            'instructor' => ['nullable', 'string', 'max:200'],
            'duracion_horas' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'vigente' => ['boolean'],
            'firmas' => ['nullable', 'array'],
            'firmas.operador' => ['nullable', new FirmaPng],
            'firmas.instructor' => ['nullable', new FirmaPng],
        ];
    }
}
