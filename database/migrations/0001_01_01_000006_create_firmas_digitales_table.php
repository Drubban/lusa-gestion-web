<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('firmas_digitales', function (Blueprint $table) {
            $table->id();
            $table->string('modelo_type');
            $table->unsignedBigInteger('modelo_id');
            $table->string('tipo_firma');
            $table->text('firma_base64');
            $table->timestamps();
            
            $table->index(['modelo_type', 'modelo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firmas_digitales');
    }
};