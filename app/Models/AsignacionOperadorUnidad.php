<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionOperadorUnidad extends Model
{
    use HasFactory;

    protected $table = 'asignacion_operador_unidad';

    protected $fillable = [
        'operador_id',
        'unidad_id',
        'fecha_inicio',
        'fecha_fin',
        'vigente',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'vigente' => 'boolean',
    ];

    // Relación con Operador
    public function operador()
    {
        return $this->belongsTo(Operador::class);
    }

    // Relación con Unidad
    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    // 🔥 RELACIÓN CON DOCUMENTOS DE MANTENIMIENTO - AGREGAR ESTA
    public function documentosMantenimiento()
    {
        return $this->hasMany(DocumentoMantenimiento::class, 'asignacion_id');
    }

    // 🔥 RELACIÓN CON DOCUMENTOS DE CAPACITACIÓN - AGREGAR ESTA (opcional)
    public function documentosCapacitacion()
    {
        return $this->hasMany(DocumentoCapacitacion::class, 'asignacion_id');
    }

    // 🔥 RELACIÓN CON MOVIMIENTOS - AGREGAR ESTA (opcional)
    public function movimientos()
    {
        return $this->hasMany(MovimientoDepartamento::class, 'asignacion_id');
    }
}