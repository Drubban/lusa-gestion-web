<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoMantenimiento extends Model
{
    use HasFactory;

    // Especificar explícitamente el nombre de la tabla
    protected $table = 'documento_mantenimiento';

    protected $fillable = [
        'asignacion_id',
        'rol',
        'tecnologia_reportada',
        'prueba_barras',
        'comentarios',
        'fecha',
        'hora',
        'veces_adeudo',
        'observaciones_adeudo',
        'vigente',
        // Si agregaste estos campos en la migración
        'unidad_id',
        'operador_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'vigente' => 'boolean',
        'veces_adeudo' => 'integer',
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