<?php

namespace Database\Factories;

use App\Models\Unidad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unidad>
 */
class UnidadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_economico' => $this->faker->unique()->numerify('ECO####'),
            'nombre_unidad' => $this->faker->word,
            'codigo_qr' => $this->faker->unique()->uuid,
            'activo' => true
        ];
    }
}
