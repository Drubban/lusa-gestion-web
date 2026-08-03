<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('unidades', function (Blueprint $table) {
            $table->foreignId('zona_id')->nullable()->constrained('zonas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('unidades', function (Blueprint $table) {
            $table->dropForeign(['zona_id']);
            $table->dropColumn('zona_id');
        });
    }
};