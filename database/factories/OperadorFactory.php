<?php

namespace Database\Factories;

use App\Models\Operador;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Operador>
 */
class OperadorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_completo' => $this->faker->name,
            'clave_operador' => $this->faker->unique()->numerify('OP###'),
            'zona_id' => Zona::factory(),
            'activo' => true
        ];
    }
}
