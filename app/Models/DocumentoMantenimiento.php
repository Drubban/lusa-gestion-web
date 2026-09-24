<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoMantenimiento extends Model
{
    use HasFactory;

    protected $table = 'documento_mantenimiento';

    protected $fillable = [
        'unidad_id',
        'operador_id',
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
        'firma_operador',
        'firma_ing',
        'firma_tabulacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'vigente' => 'boolean',
        'veces_adeudo' => 'integer',
    ];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionOperadorUnidad::class, 'asignacion_id');
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function operador()
    {
        return $this->belongsTo(Operador::class);
    }
}