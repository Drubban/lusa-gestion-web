<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - codigo_qr (uuid) nunca se usaba: el QR impreso contiene token_qr.
 * - equipo_telpo / equipo_gps / equipo_barras duplicaban la tabla tecnologias.
 * - token_qr se amplia para tokens nuevos de 40 caracteres (los impresos siguen validos).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidades', function (Blueprint $table) {
            if (Schema::hasColumn('unidades', 'codigo_qr')) {
                $table->dropUnique(['codigo_qr']);
                $table->dropColumn('codigo_qr');
            }
            foreach (['equipo_telpo', 'equipo_gps', 'equipo_barras'] as $col) {
                if (Schema::hasColumn('unidades', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('unidades', function (Blueprint $table) {
            $table->string('token_qr', 64)->change();
        });
    }

    public function down(): void
    {
        Schema::table('unidades', function (Blueprint $table) {
            $table->string('codigo_qr')->nullable();
            $table->string('equipo_telpo', 100)->nullable();
            $table->string('equipo_gps', 100)->nullable();
            $table->string('equipo_barras', 100)->nullable();
        });
    }
};
