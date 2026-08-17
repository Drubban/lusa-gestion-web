<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoCapacitacion extends Model
{
    use HasFactory;

    // Especificar explícitamente el nombre de la tabla
    protected $table = 'documento_capacitacion';

    protected $fillable = [
        'asignacion_id',
        'fecha',
        'hora',
        'vigente',
        // Si agregaste estos campos en la migración
        'unidad_id',
        'operador_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'vigente' => 'boolean',
    ];

    // Relación con AsignacionOperadorUnidad
    public function asignacion()
    {
        return $this->belongsTo(AsignacionOperadorUnidad::class, 'asignacion_id');
    }

    // Relación con Unidad (si la agregaste)
    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    // Relación con Operador (si lo agregaste)
    public function operador()
    {
        return $this->belongsTo(Operador::class);
    }
}