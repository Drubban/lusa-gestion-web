<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoCapacitacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'operador_id'         => 'required|exists:operadores,id',
            'unidad_id'           => 'nullable|exists:unidades,id',
            'tipo_capacitacion'   => 'required|string|max:200',
            'fecha_capacitacion'  => 'required|date',
            'fecha_vencimiento'   => 'required|date|after:fecha_capacitacion',
            'instructor'          => 'nullable|string|max:200',
            'duracion_horas'      => 'nullable|numeric|min:0',
            'observaciones'       => 'nullable|string|max:1000',
            'firma_operador'      => 'nullable|string',
            'firma_instructor'    => 'nullable|string',
        ];
    }
}