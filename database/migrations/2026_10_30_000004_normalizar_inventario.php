<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * El formulario de inventario tenia dos juegos de campos para lo mismo
 * (nombre_equipo/marca vs nombre_producto/marca_producto, datos_extra vs descripcion).
 * Se fusionan en: nombre, marca, modelo, numero_serie, cantidad, descripcion.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('inventario', 'nombre_producto')) {
            DB::statement("UPDATE inventario SET nombre_equipo = COALESCE(NULLIF(nombre_equipo, ''), nombre_producto)");
            DB::statement("UPDATE inventario SET marca = COALESCE(NULLIF(marca, ''), marca_producto)");
        }
        if (Schema::hasColumn('inventario', 'datos_extra')) {
            DB::statement("UPDATE inventario SET descripcion = NULLIF(CONCAT_WS(E'\\n', NULLIF(descripcion, ''), NULLIF(datos_extra, '')), '')");
        }
        DB::statement('UPDATE inventario SET cantidad = 1 WHERE cantidad IS NULL');

        Schema::table('inventario', function (Blueprint $table) {
            foreach (['nombre_producto', 'marca_producto', 'datos_extra'] as $col) {
                if (Schema::hasColumn('inventario', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('inventario', function (Blueprint $table) {
            if (Schema::hasColumn('inventario', 'nombre_equipo')) {
                $table->renameColumn('nombre_equipo', 'nombre');
            }
        });

        Schema::table('inventario', function (Blueprint $table) {
            $table->integer('cantidad')->default(1)->nullable(false)->change();
            $table->index('numero_serie');
        });
    }

    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            $table->renameColumn('nombre', 'nombre_equipo');
            $table->string('nombre_producto', 100)->nullable();
            $table->string('marca_producto', 100)->nullable();
            $table->text('datos_extra')->nullable();
        });
    }
};
