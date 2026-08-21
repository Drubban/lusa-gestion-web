<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gps', function (Blueprint $table) {
            $table->id();
            
            // Relación con tecnología
            $table->foreignId('tecnologia_id')->constrained('tecnologias')->onDelete('cascade');
            
            // Campos específicos de GPS
            $table->string('imei_gps', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('plan', 50)->nullable();
            
            // Auditoría
            $table->timestamps();
            
            // Índices
            $table->index('tecnologia_id');
            $table->unique('imei_gps');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gps');
    }
};