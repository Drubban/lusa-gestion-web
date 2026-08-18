<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            if (Schema::hasColumn('inventario', 'zona_id')) {
                $table->dropForeign(['zona_id']);
                $table->dropColumn('zona_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            $table->foreignId('zona_id')->nullable()->constrained('zonas')->onDelete('restrict');
        });
    }
};