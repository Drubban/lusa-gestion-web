<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bitacora de auditoria: quien hizo que, desde que IP y en que canal (web/app).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('accion', 60);
            $table->string('entidad', 60)->nullable();
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->string('canal', 10)->default('web');
            $table->string('ip', 45)->nullable();
            $table->json('detalle')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['entidad', 'entidad_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};
