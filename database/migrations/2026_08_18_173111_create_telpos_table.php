<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telpos', function (Blueprint $table) {
            $table->id();
            
            // Relación con tecnología
            $table->foreignId('tecnologia_id')->constrained('tecnologias')->onDelete('cascade');
            
            // Campos específicos de TELPO
            $table->string('imei_antes', 100)->nullable();
            $table->string('v_apk', 50)->nullable();
            $table->string('telpo', 100)->nullable();
            $table->string('imei_telpo', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('plan', 50)->nullable();
            $table->decimal('costo_plan', 10, 2)->nullable();
            
            // Auditoría
            $table->timestamps();
            
            // Índices
            $table->index('tecnologia_id');
            $table->unique('imei_telpo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telpos');
    }
};