<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mdvrs', function (Blueprint $table) {
            // Eliminar la restricción única de la columna dvr
            $table->dropUnique(['dvr']);
        });
    }

    public function down(): void
    {
        Schema::table('mdvrs', function (Blueprint $table) {
            // Revertir: agregar la restricción única nuevamente
            $table->unique('dvr');
        });
    }
};