<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documento_mantenimiento', function (Blueprint $table) {
            $table->string('rol')->nullable()->after('asignacion_id');
            $table->string('tecnologia_reportada')->nullable()->change(); // ya existe, pero la dejamos
            $table->enum('prueba_barras', ['SI', 'NO'])->nullable()->after('tecnologia_reportada');
            $table->text('comentarios')->nullable()->after('prueba_barras');
            // ya tiene veces_adeudo y observaciones_adeudo, los mantenemos
        });
    }

    public function down()
    {
        Schema::table('documento_mantenimiento', function (Blueprint $table) {
            $table->dropColumn(['rol', 'prueba_barras', 'comentarios']);
        });
    }
};