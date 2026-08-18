<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            
            // Datos principales
            $table->date('fecha_entrega');
            $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('restrict');
            $table->string('area', 100)->nullable();
            
            $table->string('nombre_recibe', 100);
            $table->string('clave_empleado', 50);
            
            // Categoría
            $table->enum('categoria', [
                'tarjetas',
                'equipos_computo',
                'telefonia',
                'routers_switches',
                'consumibles',
                'perifericos'
            ]);
            
            // Campos para categorías con equipo
            $table->string('nombre_equipo', 100)->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('numero_serie', 100)->nullable();
            $table->text('datos_extra')->nullable();
            
            // Campos para categorías consumibles y tarjetas
            $table->string('nombre_producto', 100)->nullable();
            $table->string('marca_producto', 100)->nullable();
            $table->integer('cantidad')->nullable();
            $table->text('descripcion')->nullable();
            
            // Imagen
            $table->string('imagen')->nullable();
            
            // ✅ Auditoría - HACER NULLABLE
            $table->foreignId('created_by')->nullable()->constrained('usuarios_departamento')->onDelete('restrict');
            $table->timestamps();
            
            // Índices
            $table->index('fecha_entrega');
            $table->index('categoria');
            $table->index('clave_empleado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};