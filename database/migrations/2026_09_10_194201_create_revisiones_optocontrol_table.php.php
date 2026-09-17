<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisiones_optocontrol', function (Blueprint $table) {
            $table->id();
            $table->enum('disp', ['barras', 'telpo', 'gps', 'camaras', 'revision', 'telpo', 'taller']);
            $table->foreignId('unidad_id')->constrained('unidades')->onDelete('cascade');
            $table->string('nombre_unidad')->nullable();
            $table->date('fecha_reporte');
            $table->time('hora_entrada');
            $table->time('hora_salida')->nullable();
            $table->string('ruta')->nullable();
            $table->text('problema');
            $table->text('solucion')->nullable();
            $table->enum('status', ['pendiente', 'en_proceso', 'resuelto', 'cancelado'])->default('pendiente');
            $table->decimal('tiempo_estancia', 8, 2)->nullable();
            $table->string('responsable')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('usuarios_departamento')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisiones_optocontrol');
    }
};