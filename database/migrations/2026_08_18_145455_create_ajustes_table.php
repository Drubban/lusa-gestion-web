<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ajustes', function (Blueprint $table) {
            $table->id();
            
            // Datos principales
            $table->string('zona', 100)->nullable();
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('monto_total', 12, 2);
            $table->string('folio', 50)->unique();
            
            // Datos del operador
            $table->foreignId('operador_id')->constrained('operadores')->onDelete('restrict');
            $table->string('clave_operador', 50);
            
            // Datos de la unidad
            $table->foreignId('unidad_id')->constrained('unidades')->onDelete('restrict');
            
            // Estado
            $table->boolean('firmado')->default(false);
            
            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('usuarios_departamento')->onDelete('restrict');
            $table->timestamps();
            
            // Índices
            $table->index('fecha');
            $table->index('folio');
            $table->index('operador_id');
            $table->index('unidad_id');
            $table->index('firmado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ajustes');
    }
};