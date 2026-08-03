<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AsignacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asignar operadores a unidades (vigentes)
        $unidades = Unidad::all();
        foreach ($unidades as $unidad) {
            $operador = Operador::inRandomOrder()->first();
            AsignacionOperadorUnidad::create([
                'operador_id' => $operador->id,
                'unidad_id' => $unidad->id,
                'fecha_inicio' => now(),
                'vigente' => true
            ]);
        }
    }
}
