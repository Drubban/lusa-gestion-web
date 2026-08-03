<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignacion_operador_unidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operador_id')->constrained('operadores')->onDelete('restrict');
            $table->foreignId('unidad_id')->constrained('unidades')->onDelete('restrict');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('vigente')->default(true);
            $table->timestamps();
            
            $table->index(['operador_id', 'vigente']);
            $table->index(['unidad_id', 'vigente']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacion_operador_unidad');
    }
};