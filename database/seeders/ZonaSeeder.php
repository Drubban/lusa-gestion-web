<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zonas = ['reyes', 'apaxco', 'citrus'];
        foreach ($zonas as $z) Zona::create(['nombre' => $z]);
    }
}
