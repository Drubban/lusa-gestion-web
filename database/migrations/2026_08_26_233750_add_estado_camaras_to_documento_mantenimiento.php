<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documento_mantenimiento', function (Blueprint $table) {
            if (!Schema::hasColumn('documento_mantenimiento', 'estado_camaras')) {
                $table->string('estado_camaras', 255)->nullable()->after('tecnologia_reportada');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documento_mantenimiento', function (Blueprint $table) {
            if (Schema::hasColumn('documento_mantenimiento', 'estado_camaras')) {
                $table->dropColumn('estado_camaras');
            }
        });
    }
};