<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartamentoLusaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = [
            // Taquillas
            ['nombre' => 'Taquilla Zona cero'],
            ['nombre' => 'Taquilla IV'],
            ['nombre' => 'Taquilla Zapata'],
            
            // Administrativos
            ['nombre' => 'RH'],
            ['nombre' => 'Logistica'],
            ['nombre' => 'Contabilidad'],
            ['nombre' => 'Reclutamiento'],
            ['nombre' => 'Auditoria'],
            
            // Talleres y mantenimiento
            ['nombre' => 'Diesel'],
            ['nombre' => 'Taller'],
            ['nombre' => 'Taller Tepsa'],
            ['nombre' => 'Taller Mangas'],
            ['nombre' => 'Mantenimiento'],
            ['nombre' => 'Servicios'],
            
            // Operativos
            ['nombre' => 'Progreso'],
            ['nombre' => 'Gas'],
            ['nombre' => 'Lavado'],
            
            // Tabulaciones
            ['nombre' => 'Tabulacion Zapata'],
            ['nombre' => 'Tabulacion Zona Cero'],
            ['nombre' => 'Tabulacion IV'],

            ['nombre' => 'AIFA']
        ];

        // Ordenar alfabéticamente para mejor visualización
        sort($departamentos);

        $insertados = 0;
        $omitidos = 0;

        foreach ($departamentos as $departamento) {
            // Verificar si ya existe para evitar duplicados
            $existe = DB::table('departamentos')
                ->where('nombre', $departamento['nombre'])
                ->exists();

            if (!$existe) {
                DB::table('departamentos')->insert($departamento);
                $insertados++;
                Log::info("Departamento creado: {$departamento['nombre']}");
            } else {
                $omitidos++;
                Log::info("Departamento ya existe: {$departamento['nombre']} - omitido");
            }
        }

        $this->command->info("✅ Seeder ejecutado correctamente:");
        $this->command->info("   - $insertados departamentos insertados");
        $this->command->info("   - $omitidos departamentos ya existentes (omitidos)");
        $this->command->info("   - Total: " . count($departamentos) . " departamentos procesados");
    }
}