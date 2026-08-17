<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar asignacion_id a documento_mantenimiento
        Schema::table('documento_mantenimiento', function (Blueprint $table) {
            // Verificar si la columna no existe antes de agregarla
            if (!Schema::hasColumn('documento_mantenimiento', 'asignacion_id')) {
                $table->foreignId('asignacion_id')
                      ->nullable()
                      ->after('operador_id') // Colocar después de operador_id
                      ->constrained('asignacion_operador_unidad')
                      ->nullOnDelete();
            }
        });

        // 2. Agregar asignacion_id a documento_capacitacion
        Schema::table('documento_capacitacion', function (Blueprint $table) {
            if (!Schema::hasColumn('documento_capacitacion', 'asignacion_id')) {
                $table->foreignId('asignacion_id')
                      ->nullable()
                      ->after('operador_id')
                      ->constrained('asignacion_operador_unidad')
                      ->nullOnDelete();
            }
        });

        // 3. Agregar unidad_id a documento_capacitacion si no existe
        Schema::table('documento_capacitacion', function (Blueprint $table) {
            if (!Schema::hasColumn('documento_capacitacion', 'unidad_id')) {
                $table->foreignId('unidad_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('unidades')
                      ->nullOnDelete();
            }
        });

        // 4. Verificar que documento_mantenimiento tenga unidad_id y operador_id
        // (por si acaso, aunque probablemente ya existen)
        Schema::table('documento_mantenimiento', function (Blueprint $table) {
            if (!Schema::hasColumn('documento_mantenimiento', 'unidad_id')) {
                $table->foreignId('unidad_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('unidades')
                      ->nullOnDelete();
            }
            if (!Schema::hasColumn('documento_mantenimiento', 'operador_id')) {
                $table->foreignId('operador_id')
                      ->nullable()
                      ->after('unidad_id')
                      ->constrained('operadores')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // Revertir cambios de forma segura
        Schema::table('documento_mantenimiento', function (Blueprint $table) {
            if (Schema::hasColumn('documento_mantenimiento', 'asignacion_id')) {
                $table->dropForeign(['asignacion_id']);
                $table->dropColumn('asignacion_id');
            }
        });

        Schema::table('documento_capacitacion', function (Blueprint $table) {
            if (Schema::hasColumn('documento_capacitacion', 'asignacion_id')) {
                $table->dropForeign(['asignacion_id']);
                $table->dropColumn('asignacion_id');
            }
            if (Schema::hasColumn('documento_capacitacion', 'unidad_id')) {
                $table->dropForeign(['unidad_id']);
                $table->dropColumn('unidad_id');
            }
        });
    }
};