<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuarioDepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Departamento::all() as $depto) {
            UsuarioDepartamento::create([
                'nombre_usuario' => $depto->nombre . '_user',
                'password' => bcrypt('12345678'),
                'departamento_id' => $depto->id
            ]);
        }
    }
}
