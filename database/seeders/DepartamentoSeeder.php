<?php
// database/seeders/DepartamentoSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    public function run()
    {
        $departamentos = [
            ['nombre' => 'taller', 'descripcion' => 'Departamento de taller mecánico', 'color' => '#FF5722', 'icono' => 'build', 'activo' => true],
            ['nombre' => 'lavado', 'descripcion' => 'Departamento de lavado y limpieza', 'color' => '#2196F3', 'icono' => 'cleaning_services', 'activo' => true],
            ['nombre' => 'sistemas', 'descripcion' => 'Departamento de sistemas y tecnología', 'color' => '#4CAF50', 'icono' => 'computer', 'activo' => true],
            ['nombre' => 'diesel', 'descripcion' => 'Departamento de combustible diesel', 'color' => '#9C27B0', 'icono' => 'local_gas_station', 'activo' => true],
        ];
        
        foreach ($departamentos as $depto) {
            DB::table('departamentos')->insert($depto);
        }
    }
}