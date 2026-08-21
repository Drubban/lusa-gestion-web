<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barras', function (Blueprint $table) {
            $table->id();
            
            // Relación con tecnología
            $table->foreignId('tecnologia_id')->constrained('tecnologias')->onDelete('cascade');
            
            // Campos específicos de BARRAS
            $table->string('id_barra', 100)->nullable();
            $table->string('barras', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('plan', 50)->nullable();
            
            // Auditoría
            $table->timestamps();
            
            // Índices
            $table->index('tecnologia_id');
            $table->unique('id_barra');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barras');
    }
};