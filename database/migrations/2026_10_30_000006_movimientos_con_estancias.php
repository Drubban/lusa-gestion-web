<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Convierte el registro de movimientos en un control de estancias:
 * cada SALIDA apunta a su ENTRADA (entrada_id) y guarda la duracion.
 * Se agrega uuid para que la sincronizacion offline de la app sea idempotente.
 * La columna "sincronizado" no tenia sentido del lado del servidor.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimiento_departamento', function (Blueprint $table) {
            if (! Schema::hasColumn('movimiento_departamento', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('movimiento_departamento', 'entrada_id')) {
                $table->foreignId('entrada_id')->nullable()->after('tipo')
                    ->constrained('movimiento_departamento')->nullOnDelete();
            }
            if (! Schema::hasColumn('movimiento_departamento', 'duracion_minutos')) {
                $table->unsignedInteger('duracion_minutos')->nullable()->after('entrada_id');
            }
            if (Schema::hasColumn('movimiento_departamento', 'sincronizado')) {
                $table->dropColumn('sincronizado');
            }
        });

        DB::statement('UPDATE movimiento_departamento SET tipo = LOWER(TRIM(tipo))');
        $this->emparejarHistorico();
    }

    /**
     * Empareja salidas historicas con la entrada abierta previa de la misma unidad y departamento.
     */
    private function emparejarHistorico(): void
    {
        $abiertas = [];
        DB::table('movimiento_departamento')
            ->orderBy('unidad_id')->orderBy('departamento_id')->orderBy('fecha_hora')->orderBy('id')
            ->select('id', 'unidad_id', 'departamento_id', 'tipo', 'fecha_hora')
            ->lazy(500)
            ->each(function ($mov) use (&$abiertas) {
                $clave = $mov->unidad_id . '-' . $mov->departamento_id;
                if ($mov->tipo === 'entrada') {
                    $abiertas[$clave] = $mov;
                    return;
                }
                if ($mov->tipo === 'salida' && isset($abiertas[$clave])) {
                    $entrada = $abiertas[$clave];
                    $minutos = (int) round((strtotime($mov->fecha_hora) - strtotime($entrada->fecha_hora)) / 60);
                    DB::table('movimiento_departamento')->where('id', $mov->id)->update([
                        'entrada_id' => $entrada->id,
                        'duracion_minutos' => max(0, $minutos),
                    ]);
                    unset($abiertas[$clave]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('movimiento_departamento', function (Blueprint $table) {
            $table->dropForeign(['entrada_id']);
            $table->dropColumn(['uuid', 'entrada_id', 'duracion_minutos']);
            $table->boolean('sincronizado')->default(false);
        });
    }
};
