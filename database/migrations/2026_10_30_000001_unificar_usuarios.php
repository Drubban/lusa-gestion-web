<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Unifica la autenticacion: antes existian dos modelos (User sin tabla y
 * UsuarioDepartamento). Ahora existe una sola tabla "usuarios" con rol,
 * usada tanto por el panel web como por la app movil.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('usuarios_departamento') && ! Schema::hasTable('usuarios')) {
            Schema::rename('usuarios_departamento', 'usuarios');
        }

        Schema::table('usuarios', function (Blueprint $table) {
            if (! Schema::hasColumn('usuarios', 'nombre_completo')) {
                $table->string('nombre_completo', 150)->nullable()->after('nombre_usuario');
            }
            if (! Schema::hasColumn('usuarios', 'email')) {
                $table->string('email', 150)->nullable()->unique()->after('nombre_completo');
            }
            if (! Schema::hasColumn('usuarios', 'rol')) {
                // admin: todo | supervisor: panel sin gestion de usuarios | departamento: solo app
                $table->string('rol', 20)->default('departamento')->after('departamento_id');
                $table->index('rol');
            }
            if (! Schema::hasColumn('usuarios', 'remember_token')) {
                $table->rememberToken();
            }
            if (! Schema::hasColumn('usuarios', 'ultimo_acceso_at')) {
                $table->timestamp('ultimo_acceso_at')->nullable();
            }
            if (! Schema::hasColumn('usuarios', 'intentos_fallidos')) {
                $table->unsignedSmallInteger('intentos_fallidos')->default(0);
                $table->timestamp('bloqueado_hasta')->nullable();
            }
        });

        // Un administrador puede no pertenecer a un departamento operativo
        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedBigInteger('departamento_id')->nullable()->change();
        });

        // Los tokens anteriores apuntaban a la clase vieja; se invalidan para forzar nuevo login
        if (Schema::hasTable('personal_access_tokens')) {
            DB::table('personal_access_tokens')->delete();
        }
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['nombre_completo', 'email', 'rol', 'remember_token', 'ultimo_acceso_at', 'intentos_fallidos', 'bloqueado_hasta']);
        });
        Schema::rename('usuarios', 'usuarios_departamento');
    }
};
