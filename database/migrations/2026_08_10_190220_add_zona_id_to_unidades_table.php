<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidades', function (Blueprint $table) {
            if (!Schema::hasColumn('unidades', 'zona_id')) {
                $table->foreignId('zona_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('zonas')
                      ->onDelete('restrict');
            }
        });
    }

    public function down(): void
    {
        Schema::table('unidades', function (Blueprint $table) {
            if (Schema::hasColumn('unidades', 'zona_id')) {
                $table->dropForeign(['zona_id']);
                $table->dropColumn('zona_id');
            }
        });
    }
};