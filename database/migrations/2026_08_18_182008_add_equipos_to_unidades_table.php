<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidades', function (Blueprint $table) {
            // E.T - Equipo Telpo
            $table->string('equipo_telpo', 100)->nullable()->after('nombre_unidad');
            // E.G - Equipo GPS  
            $table->string('equipo_gps', 100)->nullable()->after('equipo_telpo');
            // E.B - Equipo Barras
            $table->string('equipo_barras', 100)->nullable()->after('equipo_gps');
        });
    }

    public function down(): void
    {
        Schema::table('unidades', function (Blueprint $table) {
            $table->dropColumn(['equipo_telpo', 'equipo_gps', 'equipo_barras']);
        });
    }
};