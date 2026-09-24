<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documento_mantenimiento', function (Blueprint $table) {
            // Firmas en base64 (grandes)
            if (!Schema::hasColumn('documento_mantenimiento', 'firma_operador')) {
                $table->longText('firma_operador')->nullable();
            }
            if (!Schema::hasColumn('documento_mantenimiento', 'firma_ing')) {
                $table->longText('firma_ing')->nullable();
            }
            if (!Schema::hasColumn('documento_mantenimiento', 'firma_tabulacion')) {
                $table->longText('firma_tabulacion')->nullable();
            }

            // Campos del formato
            if (!Schema::hasColumn('documento_mantenimiento', 'rol')) {
                $table->string('rol', 100)->nullable();
            }
            if (!Schema::hasColumn('documento_mantenimiento', 'tecnologia_reportada')) {
                $table->text('tecnologia_reportada')->nullable();
            }
            if (!Schema::hasColumn('documento_mantenimiento', 'prueba_barras')) {
                $table->string('prueba_barras', 10)->nullable();
            }
            if (!Schema::hasColumn('documento_mantenimiento', 'veces_adeudo')) {
                $table->integer('veces_adeudo')->default(0);
            }
            if (!Schema::hasColumn('documento_mantenimiento', 'observaciones_adeudo')) {
                $table->text('observaciones_adeudo')->nullable();
            }
            if (!Schema::hasColumn('documento_mantenimiento', 'vigente')) {
                $table->boolean('vigente')->default(true);
            }
            if (!Schema::hasColumn('documento_mantenimiento', 'hora')) {
                $table->string('hora', 8)->nullable();
            }
            if (!Schema::hasColumn('documento_mantenimiento', 'operador_id')) {
                $table->unsignedBigInteger('operador_id')->nullable();
            }
            if (!Schema::hasColumn('documento_mantenimiento', 'unidad_id')) {
                $table->unsignedBigInteger('unidad_id')->nullable();
            }
        });

        // Lo mismo para capacitacion
        Schema::table('documento_capacitacion', function (Blueprint $table) {
            if (!Schema::hasColumn('documento_capacitacion', 'firma_operador')) {
                $table->longText('firma_operador')->nullable();
            }
            if (!Schema::hasColumn('documento_capacitacion', 'firma_instructor')) {
                $table->longText('firma_instructor')->nullable();
            }
            if (!Schema::hasColumn('documento_capacitacion', 'instructor')) {
                $table->string('instructor', 200)->nullable();
            }
            if (!Schema::hasColumn('documento_capacitacion', 'duracion_horas')) {
                $table->decimal('duracion_horas', 6, 2)->nullable();
            }
            if (!Schema::hasColumn('documento_capacitacion', 'observaciones')) {
                $table->text('observaciones')->nullable();
            }
            if (!Schema::hasColumn('documento_capacitacion', 'asignacion_id')) {
                $table->unsignedBigInteger('asignacion_id')->nullable();
            }
            if (!Schema::hasColumn('documento_capacitacion', 'unidad_id')) {
                $table->unsignedBigInteger('unidad_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('documento_mantenimiento', function (Blueprint $table) {
            $table->dropColumn([
                'firma_operador',
                'firma_ing',
                'firma_tabulacion',
                'rol',
                'tecnologia_reportada',
                'prueba_barras',
                'veces_adeudo',
                'observaciones_adeudo',
                'vigente',
                'hora',
            ]);
        });

        Schema::table('documento_capacitacion', function (Blueprint $table) {
            $table->dropColumn([
                'firma_operador',
                'firma_instructor',
                'instructor',
                'duracion_horas',
                'observaciones',
                'asignacion_id',
                'unidad_id',
            ]);
        });
    }
};