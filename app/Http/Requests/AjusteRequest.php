<?php

namespace App\Http\Requests;

use App\Models\Operador;
use App\Models\Unidad;
use App\Models\Zona;
use Illuminate\Validation\Rule;

class AjusteRequest extends FormularioBase
{
    protected function hashids(): array
    {
        return ['operador_id' => Operador::class, 'unidad_id' => Unidad::class, 'zona_id' => Zona::class];
    }

    protected function booleanos(): array
    {
        return ['firmado'];
    }

    public function rules(): array
    {
        return [
            'folio' => ['required', 'string', 'max:50', Rule::unique('ajustes')->ignore($this->route('ajuste'))],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'monto_total' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'operador_id' => ['required', 'integer', 'exists:operadores,id'],
            'unidad_id' => ['required', 'integer', 'exists:unidades,id'],
            'zona_id' => ['nullable', 'integer', 'exists:zonas,id'],
            'firmado' => ['boolean'],
        ];
    }
}
