<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios_departamento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_usuario', 50)->unique();
            $table->string('password');
            $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('restrict');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_departamento');
    }
};