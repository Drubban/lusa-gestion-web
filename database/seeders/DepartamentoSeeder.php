<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        // Colores alineados a la paleta Lusa (azul, amarillo, verde, morado)
        $departamentos = [
            ['nombre' => 'taller', 'descripcion' => 'Taller mecanico', 'color' => '#D99A1E', 'icono' => 'build'],
            ['nombre' => 'lavado', 'descripcion' => 'Lavado y limpieza', 'color' => '#1D5FAD', 'icono' => 'local_car_wash'],
            ['nombre' => 'sistemas', 'descripcion' => 'Sistemas y tecnologia', 'color' => '#2E9E6A', 'icono' => 'memory'],
            ['nombre' => 'diesel', 'descripcion' => 'Carga de diesel', 'color' => '#6E4FB8', 'icono' => 'local_gas_station'],
        ];
        foreach ($departamentos as $d) {
            Departamento::updateOrCreate(['nombre' => $d['nombre']], $d + ['activo' => true]);
        }
    }
}
