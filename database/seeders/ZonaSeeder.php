<?php

namespace Database\Seeders;

use App\Models\Zona;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zonas = ['reyes', 'apaxco', 'citrus', 'tranzumpango', 'corredor bc', 'odz'];

        foreach ($zonas as $z) {
            // Busca por nombre, si no existe lo crea de forma segura
            Zona::firstOrCreate(['nombre' => $z]);
        }
    }
}
