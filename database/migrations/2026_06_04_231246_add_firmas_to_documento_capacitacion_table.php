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
        Schema::table('documento_capacitacion', function (Blueprint $table) {
            $table->text('firma_operador')->nullable()->after('vigente');
            $table->text('firma_ing')->nullable()->after('firma_operador');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_capacitacion', function (Blueprint $table) {
            //
        });
    }
};
