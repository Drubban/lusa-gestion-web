<?php

namespace Database\Factories;

use App\Models\UsuarioDepartamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UsuarioDepartamento>
 */
class UsuarioDepartamentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_usuario' => $this->faker->unique()->userName,
            'password' => bcrypt('password123'),
            'departamento_id' => Departamento::factory(),
            'activo' => true
        ];
    }
}
