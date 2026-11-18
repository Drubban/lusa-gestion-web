<?php

namespace App\Http\Requests\Api;

use App\Enums\TipoMovimiento;
use App\Http\Requests\FormularioBase;
use App\Models\Departamento;
use App\Models\Unidad;
use Illuminate\Validation\Rule;

/**
 * Movimiento enviado por la app. La unidad se identifica por el token del QR
 * o por su id ofuscado. Si no se envia tipo, el servidor lo deduce.
 */
class MovimientoApiRequest extends FormularioBase
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->tokenCan('movimientos:escribir');
    }

    protected function hashids(): array
    {
        return ['unidad_id' => Unidad::class, 'departamento_id' => Departamento::class];
    }

    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid'],
            'token_qr' => ['required_without:unidad_id', 'nullable', 'string', 'max:64'],
            'unidad_id' => ['required_without:token_qr', 'nullable', 'integer', 'exists:unidades,id'],
            'departamento_id' => ['nullable', 'integer', 'exists:departamentos,id'],
            'tipo' => ['nullable', Rule::enum(TipoMovimiento::class)],
            'fecha_hora' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
