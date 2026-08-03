<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimiento_departamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_id')->constrained('unidades')->onDelete('restrict');
            $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('restrict');
            $table->foreignId('usuario_departamento_id')->constrained('usuarios_departamento')->onDelete('restrict');
            $table->string('tipo'); // entrada o salida
            $table->dateTime('fecha_hora');
            $table->text('observaciones')->nullable();
            $table->boolean('sincronizado')->default(false);
            $table->timestamps();
            
            $table->index(['unidad_id', 'fecha_hora']);
            $table->index(['departamento_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_departamento');
    }
};