<?php

namespace Database\Seeders;

use App\Enums\RolUsuario;
use App\Models\Departamento;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Crea el administrador inicial y un usuario por departamento.
 * Las contrasenas se generan al azar y se muestran una sola vez en consola
 * (antes todas eran "12345678").
 */
class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $credenciales = [];

        $passAdmin = env('ADMIN_PASSWORD') ?: Str::password(16, symbols: false);
        Usuario::firstOrCreate(
            ['nombre_usuario' => env('ADMIN_USUARIO', 'admin')],
            ['nombre_completo' => 'Administrador de sistemas', 'password' => $passAdmin, 'rol' => RolUsuario::Admin, 'activo' => true, 'puede_generar_documentos' => true],
        );
        $credenciales[] = [env('ADMIN_USUARIO', 'admin'), $passAdmin, 'admin'];

        // Solo departamentos operativos de entradas/salidas (la tabla puede tener mas areas y duplicados por mayusculas)
        $operativos = Departamento::whereRaw('LOWER(nombre) IN (?, ?, ?, ?)', ['taller', 'lavado', 'sistemas', 'diesel'])
            ->orderBy('id')->get()->unique(fn ($d) => mb_strtolower($d->nombre));

        foreach ($operativos as $depto) {
            $usuario = \Illuminate\Support\Str::slug($depto->nombre, '_') . '_app';
            $pass = Str::password(12, symbols: false);
            $creado = Usuario::firstOrCreate(
                ['nombre_usuario' => $usuario],
                ['password' => $pass, 'departamento_id' => $depto->id, 'rol' => RolUsuario::Departamento, 'activo' => true],
            );
            if ($creado->wasRecentlyCreated) {
                $credenciales[] = [$usuario, $pass, 'departamento'];
            }
        }

        $this->command?->warn('Guarda estas credenciales, no se volveran a mostrar:');
        $this->command?->table(['Usuario', 'Contrasena', 'Rol'], $credenciales);
    }
}
