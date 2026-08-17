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
            'operador_id' => 'required|exists:operadores,id',
            'tipo_capacitacion' => 'required|string|max:100',
            'fecha_capacitacion' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_capacitacion',
            'instructor' => 'nullable|string|max:200',
            'duracion_horas' => 'nullable|integer|min:1',
            'observaciones' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'operador_id.required' => 'El operador es obligatorio',
            'operador_id.exists' => 'El operador seleccionado no existe',
            'tipo_capacitacion.required' => 'El tipo de capacitación es obligatorio',
            'fecha_capacitacion.required' => 'La fecha es obligatoria',
            'fecha_capacitacion.date' => 'La fecha no es válida',
            'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria',
            'fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a la fecha de capacitación',
        ];
    }
}