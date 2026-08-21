<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mdvrs', function (Blueprint $table) {
            $table->id();
            
            // Relación con tecnología
            $table->foreignId('tecnologia_id')->constrained('tecnologias')->onDelete('cascade');
            
            // Campos específicos de MDVR
            $table->string('dvr', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('camaras', 50)->nullable();
            $table->string('memoria', 50)->nullable();
            
            // Auditoría
            $table->timestamps();
            
            // Índices
            $table->index('tecnologia_id');
            $table->unique('dvr');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mdvrs');
    }
};