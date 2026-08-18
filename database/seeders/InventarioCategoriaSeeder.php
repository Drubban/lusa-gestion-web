<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarioCategoriaSeeder extends Seeder
{
    public function run(): void
    {
        // Esto es solo para referencia, las categorías están en el modelo
        // No se guardan en BD, son constantes
        $categorias = [
            'tarjetas' => 'Tarjetas',
            'equipos_computo' => 'Equipos de Cómputo',
            'telefonia' => 'Telefonía',
            'routers_switches' => 'Routers/Switches',
            'consumibles' => 'Consumibles',
            'perifericos' => 'Periféricos',
        ];

        // Si quieres guardarlas en una tabla de catálogos, puedes crear
        // una migración para catalogo_inventario y guardarlas aquí
    }
}