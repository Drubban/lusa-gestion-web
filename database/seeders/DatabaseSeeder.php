<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ZonaSeeder::class,
            DepartamentoSeeder::class,
            OperadorSeeder::class,
            UnidadSeeder::class,
            AsignacionSeeder::class,
            UsuarioDepartamentoSeeder::class,
        ]);
    }
}