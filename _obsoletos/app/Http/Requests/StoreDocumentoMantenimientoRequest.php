<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoMantenimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unidad_id'             => 'required|exists:unidades,id',
            'operador_id'           => 'required|exists:operadores,id',
            'fecha_mantenimiento'   => 'required|date',
            'hora_mantenimiento'    => 'nullable|string|max:8',
            'tipo_mantenimiento'    => 'required|string|max:100',
            'descripcion'           => 'required|string|max:2000',

            // Campos opcionales
            'asignacion_id'         => 'nullable|exists:asignacion_operador_unidad,id',
            'rol'                   => 'nullable|string|max:100',
            'tecnologia_reportada'  => 'nullable|string|max:500',
            'prueba_barras'         => 'nullable|string|max:10',
            'estado_camaras'        => 'nullable|string|max:255',
            'veces_adeudo'          => 'nullable|integer|min:0',
            'observaciones_adeudo'  => 'nullable|string|max:500',
            'observaciones'         => 'nullable|string|max:500',

            // Firmas en base64
            'firma_operador'        => 'nullable|string',
            'firma_ing'             => 'nullable|string',
            'firma_tabulacion'      => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'unidad_id.required'            => 'La unidad es obligatoria',
            'unidad_id.exists'              => 'La unidad seleccionada no existe',
            'operador_id.required'          => 'El operador es obligatorio',
            'operador_id.exists'            => 'El operador seleccionado no existe',
            'fecha_mantenimiento.required'  => 'La fecha es obligatoria',
            'fecha_mantenimiento.date'      => 'La fecha no es valida',
            'tipo_mantenimiento.required'   => 'El tipo de mantenimiento es obligatorio',
            'descripcion.required'          => 'La descripcion es obligatoria',
        ];
    }
}