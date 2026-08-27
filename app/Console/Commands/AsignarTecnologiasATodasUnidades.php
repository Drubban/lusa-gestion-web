<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Unidad;
use App\Models\Tecnologia;
use App\Models\Barra;
use App\Models\Telpo;
use App\Models\Gps;
use App\Models\Mdvr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AsignarTecnologiasATodasUnidades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unidades:asignar-tecnologias
                            {--tipo= : Asignar solo un tipo especifico (barras, telpo, gps, mdvr)}
                            {--unidad= : Asignar a una unidad especifica por ID}
                            {--force : Forzar la asignacion aunque ya existan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna todas las tecnologias (barras, telpo, gps, mdvr) a todas las unidades activas';

    public function handle()
    {
        $this->info('=== ASIGNACION MASIVA DE TECNOLOGIAS ===');

        // Obtener parametros
        $tipoEspecifico = $this->option('tipo');
        $unidadEspecifica = $this->option('unidad');
        $force = $this->option('force');

        // Construir la consulta de unidades
        $query = Unidad::where('activo', true);

        if ($unidadEspecifica) {
            $query->where('id', $unidadEspecifica);
            $this->info("📌 Asignando a unidad especifica ID: {$unidadEspecifica}");
        } else {
            $this->info("📌 Asignando a todas las unidades activas");
        }

        $unidades = $query->get();

        if ($unidades->isEmpty()) {
            $this->error('❌ No se encontraron unidades activas.');
            return 1;
        }

        $this->info("📊 Unidades encontradas: " . $unidades->count());

        // Tipos de tecnologia
        $tipos = ['barras', 'telpo', 'gps', 'mdvr'];

        if ($tipoEspecifico) {
            if (!in_array($tipoEspecifico, $tipos)) {
                $this->error("❌ Tipo '{$tipoEspecifico}' no valido. Debe ser: " . implode(', ', $tipos));
                return 1;
            }
            $tipos = [$tipoEspecifico];
            $this->info("📌 Asignando solo tipo: {$tipoEspecifico}");
        }

        $asignados = 0;
        $omitidos = 0;
        $errores = [];

        DB::beginTransaction();

        try {
            foreach ($unidades as $unidad) {
                $this->info("--- Procesando unidad: {$unidad->numero_economico} (ID: {$unidad->id}) ---");

                foreach ($tipos as $tipo) {
                    // Verificar si ya existe
                    $existe = Tecnologia::where('unidad_id', $unidad->id)
                        ->where('tipo', $tipo)
                        ->exists();

                    if ($existe && !$force) {
                        $this->warn("   ⏩ Tecnologia '{$tipo}' ya existe en unidad {$unidad->numero_economico}. Omitiendo.");
                        $omitidos++;
                        continue;
                    }

                    // Crear la tecnologia
                    $tecnologia = Tecnologia::create([
                        'unidad_id' => $unidad->id,
                        'tipo' => $tipo,
                        'nombre' => "{$tipo} - {$unidad->numero_economico}",
                        'activo' => true,
                        'created_by' => 1, // Usuario Drubban
                    ]);

                    // Crear datos especificos segun tipo (con valores por defecto)
                    $this->crearDatosEspecificos($tecnologia, $tipo);

                    $asignados++;
                    $this->info("   ✅ Tecnologia '{$tipo}' asignada a unidad {$unidad->numero_economico}");
                }
            }

            DB::commit();

            $this->newLine();
            $this->info('=== RESUMEN ===');
            $this->info("✅ Tecnologias asignadas: {$asignados}");
            $this->info("⏩ Tecnologias omitidas: {$omitidos}");
            $this->info("📊 Unidades procesadas: " . $unidades->count());

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            Log::error('Error en comando AsignarTecnologiasATodasUnidades: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Crear datos especificos segun el tipo (con valores por defecto)
     */
    private function crearDatosEspecificos($tecnologia, $tipo)
    {
        switch ($tipo) {
            case 'barras':
                Barra::create([
                    'tecnologia_id' => $tecnologia->id,
                    'id_barra' => "BAR-{$tecnologia->unidad_id}",
                    'barras' => 'OptoControl',
                    'telefono' => '0000000000',
                    'plan' => 'Plan Estandar',
                ]);
                break;

            case 'telpo':
                Telpo::create([
                    'tecnologia_id' => $tecnologia->id,
                    'imei_antes' => null,
                    'v_apk' => '4.9.2',
                    'telpo' => 'T20',
                    'imei_telpo' => "TELPO-{$tecnologia->unidad_id}",
                    'telefono' => '0000000000',
                    'plan' => 'Plan Estandar',
                    'costo_plan' => 749.00,
                ]);
                break;

            case 'gps':
                Gps::create([
                    'tecnologia_id' => $tecnologia->id,
                    'imei_gps' => "GPS-{$tecnologia->unidad_id}",
                    'telefono' => '0000000000',
                    'plan' => 'Plan Estandar',
                ]);
                break;

            case 'mdvr':
                Mdvr::create([
                    'tecnologia_id' => $tecnologia->id,
                    'dvr' => "DVR-{$tecnologia->unidad_id}",
                    'modelo' => 'Modelo Estandar',
                    'camaras' => '4',
                    'memoria' => '256GB',
                ]);
                break;
        }
    }
}