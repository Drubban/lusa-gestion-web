<?php

namespace Database\Factories;

use App\Models\AsignacionOperadorUnidad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AsignacionOperadorUnidad>
 */
class AsignacionOperadorUnidadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'operador_id' => Operador::factory(),
            'unidad_id' => Unidad::factory(),
            'fecha_inicio' => $this->faker->date(),
            'fecha_fin' => null,
            'vigente' => true
        ];
    }
}
