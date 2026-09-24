<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoCapacitacion extends Model
{
    use HasFactory;

    protected $table = 'documento_capacitacion';

    protected $fillable = [
        'operador_id',
        'unidad_id',
        'asignacion_id',
        'tipo_capacitacion',
        'fecha_capacitacion',
        'fecha_vencimiento',
        'instructor',
        'duracion_horas',
        'observaciones',
        'firma_operador',
        'firma_instructor',
    ];

    protected $casts = [
        'fecha_capacitacion' => 'date',
        'fecha_vencimiento' => 'date',
        'duracion_horas' => 'decimal:2',
    ];

    public function operador()
    {
        return $this->belongsTo(Operador::class);
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function asignacion()
    {
        return $this->belongsTo(AsignacionOperadorUnidad::class, 'asignacion_id');
    }
}