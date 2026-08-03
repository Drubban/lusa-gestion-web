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

    // Relación con Unidad (esta es la que faltaba)
    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
}