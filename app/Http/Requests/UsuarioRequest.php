<?php

namespace App\Http\Requests;

use App\Enums\RolUsuario;
use App\Models\Departamento;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioRequest extends FormularioBase
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->esAdmin();
    }

    protected function hashids(): array
    {
        return ['departamento_id' => Departamento::class];
    }

    protected function booleanos(): array
    {
        return ['activo', 'puede_generar_documentos'];
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');

        return [
            'nombre_usuario' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('usuarios')->ignore($usuario)],
            'nombre_completo' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('usuarios')->ignore($usuario)],
            'password' => [$usuario ? 'nullable' : 'required', 'confirmed', Password::min(8)->letters()->numbers()],
            'rol' => ['required', Rule::enum(RolUsuario::class)],
            'departamento_id' => [Rule::requiredIf($this->input('rol') === RolUsuario::Departamento->value), 'nullable', 'integer', 'exists:departamentos,id'],
            'puede_generar_documentos' => ['boolean'],
            'activo' => ['boolean'],
        ];
    }
}
