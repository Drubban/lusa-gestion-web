<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoMantenimiento extends Model
{
    use HasFactory;

    protected $table = 'documento_mantenimiento';

    // app/Models/DocumentoMantenimiento.php
    protected $fillable = [
        'asignacion_id',
        // 'operador_id',
        // 'unidad_id',
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
    ];

    // 👇 Relación con la asignación (operador + unidad)
    public function asignacion()
    {
        return $this->belongsTo(AsignacionOperadorUnidad::class, 'asignacion_id');
    }

    // Relaciones con firmas (si las usas)
    public function firmaOperador()
    {
        return $this->belongsTo(FirmaDigital::class, 'firma_operador_id');
    }

    public function firmaIng()
    {
        return $this->belongsTo(FirmaDigital::class, 'firma_ing_id');
    }

    public function firmaTabulacion()
    {
        return $this->belongsTo(FirmaDigital::class, 'firma_tabulacion_id');
    }
}
