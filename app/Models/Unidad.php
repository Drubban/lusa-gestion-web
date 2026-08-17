<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidades';

    protected $fillable = [
        'numero_economico',
        'nombre_unidad',
        'zona_id',
        'codigo_qr',
        'token_qr',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Mutador para activo
    public function setActivoAttribute($value)
    {
        $this->attributes['activo'] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    // 🔥 RELACIÓN CON MOVIMIENTOS - AGREGAR ESTO
    public function movimientos()
    {
        return $this->hasMany(MovimientoDepartamento::class, 'unidad_id');
    }

    // 🔥 RELACIÓN CON MOVIMIENTOS A TRAVÉS DE ASIGNACIONES (alternativa)
    public function movimientosHistoricos()
    {
        return $this->hasManyThrough(
            MovimientoDepartamento::class,
            AsignacionOperadorUnidad::class,
            'unidad_id',      // Foreign key en asignacion_operador_unidad
            'asignacion_id',  // Foreign key en movimiento_departamento
            'id',             // Local key en unidades
            'id'              // Local key en asignacion_operador_unidad
        );
    }

    // Relación con Zona
    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    // Relación con AsignacionOperadorUnidad
    public function asignaciones()
    {
        return $this->hasMany(AsignacionOperadorUnidad::class);
    }

    public function asignacionVigente()
    {
        return $this->hasOne(AsignacionOperadorUnidad::class)
            ->where('vigente', true)
            ->with('operador');
    }

    // Relación con Documentos de Mantenimiento (a través de asignaciones)
    public function documentosMantenimiento()
    {
        return $this->hasManyThrough(
            DocumentoMantenimiento::class,
            AsignacionOperadorUnidad::class,
            'unidad_id',
            'asignacion_id',
            'id',
            'id'
        );
    }

    // Relación con Documentos de Capacitación (a través de asignaciones)
    public function documentosCapacitacion()
    {
        return $this->hasManyThrough(
            DocumentoCapacitacion::class,
            AsignacionOperadorUnidad::class,
            'unidad_id',
            'asignacion_id',
            'id',
            'id'
        );
    }

    // Método para obtener el operador actual
    public function getOperadorActualAttribute()
    {
        return $this->asignacionVigente?->operador;
    }

    // Método para obtener el nombre de la zona
    public function getNombreZonaAttribute()
    {
        return $this->zona?->nombre;
    }
}
