<?php

namespace Database\Factories;

use App\Models\Zona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Zona>
 */
class ZonaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // database/factories/ZonaFactory.php
        return ['nombre' => $this->faker->randomElement(['reyes', 'apaxco', 'citrus']), 'activo' => true];
    }
}
