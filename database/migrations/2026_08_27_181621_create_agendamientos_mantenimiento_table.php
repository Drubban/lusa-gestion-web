<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendamientos_mantenimiento', function (Blueprint $table) {
            $table->id();
            
            // Relación con la unidad
            $table->foreignId('unidad_id')->constrained('unidades')->onDelete('cascade');
            
            // Fecha agendada para el mantenimiento
            $table->date('fecha_agendada');
            
            // Estado del agendamiento
            $table->enum('estado', [
                'pendiente',      // Esperando la fecha
                'cumplido',       // Se realizó el mantenimiento
                'no_cumplido',    // No se presentó
                'reagendado'      // Se cambió la fecha
            ])->default('pendiente');
            
            // Fecha en que se cumplió (si aplica)
            $table->date('fecha_cumplimiento')->nullable();
            
            // Observaciones
            $table->text('observaciones')->nullable();
            
            // Usuario que agendó
            $table->foreignId('created_by')->nullable()->constrained('usuarios_departamento')->onDelete('restrict');
            
            $table->timestamps();
            
            // Índices
            $table->index('fecha_agendada');
            $table->index('estado');
            $table->index('unidad_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamientos_mantenimiento');
    }
};