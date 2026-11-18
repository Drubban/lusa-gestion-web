<?php

namespace App\Http\Requests;

use App\Enums\CategoriaInventario;
use App\Models\Departamento;
use Illuminate\Validation\Rule;

/**
 * Formulario unico de inventario. Las categorias de equipo exigen modelo y
 * numero de serie unico; las de consumo exigen cantidad.
 */
class InventarioRequest extends FormularioBase
{
    protected function hashids(): array
    {
        return ['departamento_id' => Departamento::class];
    }

    public function rules(): array
    {
        $esEquipo = in_array($this->input('categoria'), CategoriaInventario::valoresEquipo(), true);

        return [
            'fecha_entrega' => ['required', 'date', 'before_or_equal:today'],
            'departamento_id' => ['required', 'integer', 'exists:departamentos,id'],
            'area' => ['nullable', 'string', 'max:100'],
            'nombre_recibe' => ['required', 'string', 'max:100'],
            'clave_empleado' => ['required', 'string', 'max:50'],
            'categoria' => ['required', Rule::enum(CategoriaInventario::class)],
            'nombre' => ['required', 'string', 'max:100'],
            'marca' => ['nullable', 'string', 'max:100'],
            'modelo' => [Rule::requiredIf($esEquipo), 'nullable', 'string', 'max:100'],
            'numero_serie' => [Rule::requiredIf($esEquipo), 'nullable', 'string', 'max:100', Rule::unique('inventario')->ignore($this->route('inventario'))],
            'cantidad' => ['required', 'integer', 'min:1', 'max:100000'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'imagen' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ];
    }
}
