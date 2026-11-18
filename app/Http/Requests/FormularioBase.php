<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base de formularios: decodifica ids ofuscados (hashids) y normaliza checkboxes
 * antes de validar, para que las reglas trabajen con ids reales.
 */
abstract class FormularioBase extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** Campo => clase de modelo cuyo hashid se debe decodificar. */
    protected function hashids(): array
    {
        return [];
    }

    /** Campos checkbox que deben convertirse a boolean. */
    protected function booleanos(): array
    {
        return [];
    }

    protected function prepareForValidation(): void
    {
        $datos = [];
        foreach ($this->hashids() as $campo => $modelo) {
            $valor = $this->input($campo);
            if ($valor === null || $valor === '') {
                $datos[$campo] = null;
                continue;
            }
            // Un hash invalido se convierte en 0 para que la regla "exists" falle de forma controlada
            $datos[$campo] = is_array($valor)
                ? array_map(fn ($h) => $modelo::decodeHashid((string) $h) ?? 0, $valor)
                : ($modelo::decodeHashid((string) $valor) ?? 0);
        }
        foreach ($this->booleanos() as $campo) {
            $datos[$campo] = $this->boolean($campo);
        }
        $this->merge($datos);
    }

    public function attributes(): array
    {
        return [
            'unidad_id' => 'unidad',
            'operador_id' => 'operador',
            'zona_id' => 'zona',
            'departamento_id' => 'departamento',
        ];
    }
}
