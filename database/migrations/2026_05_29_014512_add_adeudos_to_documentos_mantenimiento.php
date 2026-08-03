<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documento_mantenimiento', function (Blueprint $table) {
            $table->integer('veces_adeudo')->default(0)->after('tecnologia_reportada');
            $table->text('observaciones_adeudo')->nullable()->after('veces_adeudo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos_mantenimiento', function (Blueprint $table) {
            //
        });
    }
};
