<?php
// database/seeders/RolZonaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolZonaSeeder extends Seeder
{
    public function run()
    {
        $zonas = [
            ['nombre' => 'reyes', 'descripcion' => 'Ruta Reyes - Indios Verdes', 'activo' => true],
            ['nombre' => 'apaxco', 'descripcion' => 'Ruta Apaxco - Indios Verdes', 'activo' => true],
            ['nombre' => 'citrus', 'descripcion' => 'Ruta Citrus - Indios Verdes', 'activo' => true],
        ];
        
        foreach ($zonas as $zona) {
            DB::table('rol_zones')->insert($zona);
        }
    }
}