<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tecnologias', function (Blueprint $table) {
            $table->id();
            
            // Relación con unidad
            $table->foreignId('unidad_id')->constrained('unidades')->onDelete('cascade');
            
            // Tipo de tecnología
            $table->enum('tipo', ['barras', 'telpo', 'gps', 'mdvr']);
            
            // Campos comunes
            $table->string('nombre', 100)->nullable();
            $table->boolean('activo')->default(true);
            
            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('usuarios_departamento')->onDelete('restrict');
            $table->timestamps();
            
            // Índices
            $table->index('unidad_id');
            $table->index('tipo');
            $table->unique(['unidad_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tecnologias');
    }
};