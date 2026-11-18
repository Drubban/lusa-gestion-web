<?php

namespace App\Http\Requests;

use App\Enums\TipoTecnologia;
use App\Models\Unidad;
use Illuminate\Validation\Rule;

class TecnologiaRequest extends FormularioBase
{
    protected function hashids(): array
    {
        return ['unidad_id' => Unidad::class];
    }

    protected function booleanos(): array
    {
        return ['activo'];
    }

    public function rules(): array
    {
        $tecnologia = $this->route('tecnologia');
        $reglas = [
            'unidad_id' => ['required', 'integer', 'exists:unidades,id'],
            'tipo' => [
                'required', Rule::enum(TipoTecnologia::class),
                Rule::unique('tecnologias')->where('unidad_id', $this->input('unidad_id'))->ignore($tecnologia),
            ],
            'nombre' => ['nullable', 'string', 'max:100'],
            'activo' => ['boolean'],
            'detalle' => ['nullable', 'array'],
        ];

        $tipo = TipoTecnologia::tryFrom((string) $this->input('tipo'));
        foreach ($tipo?->campos() ?? [] as $campo => $etiqueta) {
            $reglas["detalle.{$campo}"] = $campo === 'costo_plan' ? ['nullable', 'numeric', 'min:0'] : ['nullable', 'string', 'max:100'];
        }

        return $reglas;
    }

    public function messages(): array
    {
        return ['tipo.unique' => 'La unidad ya tiene registrada esa tecnologia.'];
    }

    /** Solo los campos del subtipo elegido. */
    public function detalle(): array
    {
        $tipo = TipoTecnologia::from($this->validated('tipo'));

        return array_intersect_key($this->validated('detalle') ?? [], $tipo->campos());
    }
}
