<?php

namespace Database\Factories;

use App\Enums\RolUsuario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'nombre_usuario' => $this->faker->unique()->userName(),
            'nombre_completo' => $this->faker->name(),
            'password' => Str::password(16),
            'rol' => RolUsuario::Departamento,
            'activo' => true,
            'puede_generar_documentos' => false,
        ];
    }

    public function admin(): static
    {
        return $this->state(['rol' => RolUsuario::Admin, 'puede_generar_documentos' => true]);
    }
}
