<?php

namespace App\Models;

use Database\Factories\DocumentoCapacitacionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoCapacitacion extends Model
{
    /** @use HasFactory<DocumentoCapacitacionFactory> */
    use HasFactory;

    protected $table = 'documento_capacitacion';

    protected $fillable = [
        'asignacion_id',
        'operador_id',
        'unidad_id',
        // 'zona',
        'fecha',
        'hora',
        'vigente',
        'firma_operador',
        'firma_ing',
    ];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionOperadorUnidad::class);
    }
}
