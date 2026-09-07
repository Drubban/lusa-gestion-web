<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendamientos_mantenimiento', function (Blueprint $table) {
            // Motivo de no presentación
            $table->string('motivo_no_presentado', 255)->nullable()->after('observaciones');
            // Fecha de reprogramación
            $table->date('fecha_reprogramada')->nullable()->after('motivo_no_presentado');
            // Usuario que reportó
            $table->foreignId('reportado_por')->nullable()->after('fecha_reprogramada')->constrained('usuarios_departamento')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('agendamientos_mantenimiento', function (Blueprint $table) {
            $table->dropForeign(['reportado_por']);
            $table->dropColumn(['motivo_no_presentado', 'fecha_reprogramada', 'reportado_por']);
        });
    }
};