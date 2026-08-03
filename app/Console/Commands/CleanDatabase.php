<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanDatabase extends Command
{
    protected $signature = 'db:clean';
    protected $description = 'Limpia todas las tablas de la base de datos';

    public function handle()
    {
        $this->info('Limpiando base de datos...');
        
        // Deshabilitar restricciones de clave foránea
        DB::statement('SET session_replication_role = replica;');
        
        // Obtener todas las tablas
        $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        
        foreach ($tables as $table) {
            $tableName = $table->tablename;
            $this->info("Eliminando tabla: {$tableName}");
            Schema::dropIfExists($tableName);
        }
        
        // Habilitar restricciones nuevamente
        DB::statement('SET session_replication_role = origin;');
        
        // Limpiar migraciones
        DB::table('migrations')->truncate();
        
        $this->info('Base de datos limpiada correctamente.');
    }
}