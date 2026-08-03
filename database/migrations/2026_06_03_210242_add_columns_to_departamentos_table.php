<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->string('descripcion')->nullable()->after('nombre');
            $table->string('color')->nullable()->after('descripcion');
            $table->string('icono')->nullable()->after('color');
            $table->boolean('activo')->default(true)->after('icono');
        });
    }

    public function down()
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'color', 'icono', 'activo']);
        });
    }
};