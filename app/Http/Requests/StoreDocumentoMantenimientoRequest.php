<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoMantenimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambiar según tu lógica de autorización
    }

    public function rules(): array
    {
        return [
            'unidad_id' => 'required|exists:unidades,id',
            'operador_id' => 'required|exists:operadores,id',
            'fecha_mantenimiento' => 'required|date',
            'tipo_mantenimiento' => 'required|string|max:100',
            'descripcion' => 'required|string|max:500',
            'costo' => 'nullable|numeric|min:0',
            'kilometraje' => 'nullable|integer|min:0',
            'proveedor' => 'nullable|string|max:200',
            'observaciones' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'unidad_id.required' => 'La unidad es obligatoria',
            'unidad_id.exists' => 'La unidad seleccionada no existe',
            'operador_id.required' => 'El operador es obligatorio',
            'operador_id.exists' => 'El operador seleccionado no existe',
            'fecha_mantenimiento.required' => 'La fecha es obligatoria',
            'fecha_mantenimiento.date' => 'La fecha no es válida',
            'tipo_mantenimiento.required' => 'El tipo de mantenimiento es obligatorio',
            'descripcion.required' => 'La descripción es obligatoria',
            'costo.numeric' => 'El costo debe ser un número',
            'costo.min' => 'El costo no puede ser negativo',
            'kilometraje.integer' => 'El kilometraje debe ser un número entero',
        ];
    }
}