<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('operadores', function (Blueprint $table) {
            // Primero eliminar la clave foránea si existe
            $table->dropForeign(['zona_id']);
            $table->dropColumn('zona_id');
        });
    }

    public function down()
    {
        Schema::table('operadores', function (Blueprint $table) {
            $table->foreignId('zona_id')->nullable()->constrained('zonas')->onDelete('set null');
        });
    }
};