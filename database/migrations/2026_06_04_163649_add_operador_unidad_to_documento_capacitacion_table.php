<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documento_capacitacion', function (Blueprint $table) {
            $table->foreignId('operador_id')->nullable()->after('asignacion_id')->constrained('operadores')->onDelete('set null');
            $table->foreignId('unidad_id')->nullable()->after('operador_id')->constrained('unidades')->onDelete('set null');
            // Opcional: quitar columnas antiguas si ya no las usas
            // $table->dropColumn(['nombre_operador', 'clave_operador']); // si existían
        });
    }

    public function down()
    {
        Schema::table('documento_capacitacion', function (Blueprint $table) {
            $table->dropForeign(['operador_id']);
            $table->dropForeign(['unidad_id']);
            $table->dropColumn(['operador_id', 'unidad_id']);
        });
    }
};