<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Problemas que corrige:
 * - Las firmas estaban guardadas 3 veces de formas distintas: columnas *_id hacia
 *   firmas_digitales, columnas text (firma_operador, firma_ing...) y longText
 *   agregadas despues (firma_instructor). Ahora solo existe firmas_digitales (polimorfica).
 * - Los documentos guardaban asignacion_id + unidad_id + operador_id (dato derivable
 *   y ademas el admin creaba asignaciones falsas con operador nulo). Ahora el documento
 *   guarda directamente unidad_id (obligatorio) y operador_id (opcional).
 * - La columna "rol" contenia la zona escrita a mano; la zona se obtiene de la unidad.
 * - documento_capacitacion no tenia columnas que el modelo usaba (tipo_capacitacion, fecha_vencimiento).
 */
return new class extends Migration
{
    private const MAPA_FIRMAS = [
        'documento_mantenimiento' => [
            'firma_operador' => 'operador',
            'firma_ing' => 'ingeniero',
            'firma_tabulacion' => 'tabulacion',
        ],
        'documento_capacitacion' => [
            'firma_operador' => 'operador',
            'firma_ing' => 'instructor',
            'firma_instructor' => 'instructor',
        ],
    ];

    public function up(): void
    {
        // 1) firmas_digitales queda como unica fuente de firmas
        Schema::table('firmas_digitales', function (Blueprint $table) {
            if (! Schema::hasColumn('firmas_digitales', 'hash_sha256')) {
                $table->string('hash_sha256', 64)->nullable()->after('firma_base64');
            }
        });
        DB::statement('ALTER TABLE firmas_digitales ALTER COLUMN firma_base64 TYPE TEXT');

        foreach (self::MAPA_FIRMAS as $tabla => $columnas) {
            $this->migrarFirmas($tabla, $columnas);
        }

        // 2) Completar unidad_id / operador_id desde la asignacion antes de eliminarla
        foreach (['documento_mantenimiento', 'documento_capacitacion'] as $tabla) {
            if (Schema::hasColumn($tabla, 'asignacion_id')) {
                DB::statement("
                    UPDATE {$tabla} d
                    SET unidad_id = COALESCE(d.unidad_id, a.unidad_id),
                        operador_id = COALESCE(d.operador_id, a.operador_id)
                    FROM asignacion_operador_unidad a
                    WHERE a.id = d.asignacion_id
                ");
            }

            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                foreach (['firma_operador_id', 'firma_ing_id', 'firma_tabulacion_id', 'asignacion_id'] as $fk) {
                    if (Schema::hasColumn($tabla, $fk)) {
                        $table->dropForeign([$fk]);
                        $table->dropColumn($fk);
                    }
                }
                foreach (['firma_operador', 'firma_ing', 'firma_tabulacion', 'firma_instructor', 'rol'] as $col) {
                    if (Schema::hasColumn($tabla, $col)) {
                        $table->dropColumn($col);
                    }
                }
                if (! Schema::hasColumn($tabla, 'uuid')) {
                    // Permite que la app reintente envios sin duplicar documentos
                    $table->uuid('uuid')->nullable()->unique();
                }
                if (! Schema::hasColumn($tabla, 'usuario_id')) {
                    $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
                }
                $table->index(['unidad_id', 'fecha']);
            });

            $this->hacerObligatorioSiNoHayNulos($tabla, 'unidad_id');
        }

        // 3) Columnas faltantes en capacitacion
        Schema::table('documento_capacitacion', function (Blueprint $table) {
            if (! Schema::hasColumn('documento_capacitacion', 'tipo_capacitacion')) {
                $table->string('tipo_capacitacion', 200)->nullable()->after('operador_id');
            }
            if (! Schema::hasColumn('documento_capacitacion', 'fecha_vencimiento')) {
                $table->date('fecha_vencimiento')->nullable()->after('hora');
            }
        });

        // 4) Mantenimiento: observaciones_adeudo y comentarios quedan, se agrega usuario
        Schema::table('documento_mantenimiento', function (Blueprint $table) {
            if (Schema::hasColumn('documento_mantenimiento', 'prueba_barras')) {
                DB::statement('ALTER TABLE documento_mantenimiento ALTER COLUMN prueba_barras TYPE VARCHAR(2)');
            }
        });
    }

    private function migrarFirmas(string $tabla, array $columnas): void
    {
        $existentes = array_filter(array_keys($columnas), fn ($c) => Schema::hasColumn($tabla, $c));
        if (empty($existentes)) {
            return;
        }

        DB::table($tabla)->select(array_merge(['id'], $existentes))->orderBy('id')
            ->chunkById(200, function ($filas) use ($tabla, $columnas, $existentes) {
                $insertar = [];
                foreach ($filas as $fila) {
                    $tiposInsertados = [];
                    foreach ($existentes as $columna) {
                        $valor = $fila->{$columna};
                        $tipo = $columnas[$columna];
                        if (empty($valor) || in_array($tipo, $tiposInsertados, true)) {
                            continue;
                        }
                        $tiposInsertados[] = $tipo;
                        $insertar[] = [
                            'modelo_type' => $tabla,
                            'modelo_id' => $fila->id,
                            'tipo_firma' => $tipo,
                            'firma_base64' => $valor,
                            'hash_sha256' => hash('sha256', $valor),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if ($insertar) {
                    DB::table('firmas_digitales')->insert($insertar);
                }
            });
    }

    private function hacerObligatorioSiNoHayNulos(string $tabla, string $columna): void
    {
        $nulos = DB::table($tabla)->whereNull($columna)->count();
        if ($nulos === 0) {
            DB::statement("ALTER TABLE {$tabla} ALTER COLUMN {$columna} SET NOT NULL");
        }
        // Si quedan nulos se deja nullable para no perder datos historicos;
        // la validacion de la aplicacion ya exige el valor en registros nuevos.
    }

    public function down(): void
    {
        // Migracion de normalizacion sin reversa automatica: restaurar desde respaldo.
    }
};
