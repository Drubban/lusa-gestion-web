<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_capacitacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_id')->constrained('asignacion_operador_unidad')->onDelete('restrict');
            $table->date('fecha');
            $table->time('hora');
            $table->foreignId('firma_operador_id')->nullable()->constrained('firmas_digitales')->onDelete('set null');
            $table->foreignId('firma_ing_id')->nullable()->constrained('firmas_digitales')->onDelete('set null');
            $table->boolean('vigente')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_capacitacion');
    }
};