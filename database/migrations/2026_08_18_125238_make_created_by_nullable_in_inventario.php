<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            if (Schema::hasColumn('inventario', 'created_by')) {
                // Eliminar la restricción de clave foránea primero
                $table->dropForeign(['created_by']);
                // Hacer la columna nullable
                $table->unsignedBigInteger('created_by')->nullable()->change();
                // Volver a agregar la clave foránea
                $table->foreign('created_by')->references('id')->on('usuarios_departamento')->onDelete('restrict');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            if (Schema::hasColumn('inventario', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->unsignedBigInteger('created_by')->nullable(false)->change();
                $table->foreign('created_by')->references('id')->on('usuarios_departamento')->onDelete('restrict');
            }
        });
    }
};