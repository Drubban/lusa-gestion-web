<?php

namespace App\Http\Requests;

use App\Models\DocumentoMantenimiento;
use App\Models\Operador;
use App\Models\Unidad;
use App\Rules\FirmaPng;
use Illuminate\Validation\Rule;

/** Mismo formulario para panel y app (antes cada uno validaba campos distintos). */
class DocumentoMantenimientoRequest extends FormularioBase
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
            'tecnologias' => ['required', 'array', 'min:1'],
            'tecnologias.*' => [Rule::in(array_keys(DocumentoMantenimiento::TECNOLOGIAS))],
            'camaras' => ['nullable', 'array'],
            'camaras.*' => ['integer', 'between:1,4'],
            'prueba_barras' => ['nullable', 'in:SI,NO'],
            'comentarios' => ['nullable', 'string', 'max:2000'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
            'veces_adeudo' => ['nullable', 'integer', 'min:0', 'max:99'],
            'observaciones_adeudo' => ['nullable', 'string', 'max:500'],
            'vigente' => ['boolean'],
            'firmas' => ['nullable', 'array'],
            'firmas.operador' => ['nullable', new FirmaPng],
            'firmas.ingeniero' => ['nullable', new FirmaPng],
            'firmas.tabulacion' => ['nullable', new FirmaPng],
        ];
    }
}
