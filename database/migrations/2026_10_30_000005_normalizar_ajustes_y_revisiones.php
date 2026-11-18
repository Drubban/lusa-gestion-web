<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * - ajustes.clave_operador duplicaba operadores.clave_operador.
 * - ajustes.zona (texto libre) pasa a ser llave foranea zona_id.
 * - revisiones_optocontrol.nombre_unidad duplicaba unidades.nombre_unidad.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ajustes', function (Blueprint $table) {
            if (! Schema::hasColumn('ajustes', 'zona_id')) {
                $table->foreignId('zona_id')->nullable()->after('id')->constrained('zonas')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('ajustes', 'zona')) {
            DB::statement('
                UPDATE ajustes a SET zona_id = z.id
                FROM zonas z
                WHERE LOWER(TRIM(a.zona)) = LOWER(z.nombre) AND a.zona_id IS NULL
            ');
            // Si no coincide por nombre, se toma la zona actual de la unidad
            DB::statement('
                UPDATE ajustes a SET zona_id = u.zona_id
                FROM unidades u
                WHERE u.id = a.unidad_id AND a.zona_id IS NULL
            ');
        }

        Schema::table('ajustes', function (Blueprint $table) {
            foreach (['zona', 'clave_operador'] as $col) {
                if (Schema::hasColumn('ajustes', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('revisiones_optocontrol', function (Blueprint $table) {
            if (Schema::hasColumn('revisiones_optocontrol', 'nombre_unidad')) {
                $table->dropColumn('nombre_unidad');
            }
            $table->index(['unidad_id', 'fecha_reporte']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('ajustes', function (Blueprint $table) {
            $table->string('zona', 100)->nullable();
            $table->string('clave_operador', 50)->nullable();
        });
        Schema::table('revisiones_optocontrol', function (Blueprint $table) {
            $table->string('nombre_unidad')->nullable();
        });
    }
};
