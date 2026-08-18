<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            if (!Schema::hasColumn('inventario', 'imagen')) {
                $table->string('imagen')->nullable()->after('descripcion');
            }
            if (!Schema::hasColumn('inventario', 'area')) {
                $table->string('area', 100)->nullable()->after('zona_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            if (Schema::hasColumn('inventario', 'imagen')) {
                $table->dropColumn('imagen');
            }
            if (Schema::hasColumn('inventario', 'area')) {
                $table->dropColumn('area');
            }
        });
    }
};