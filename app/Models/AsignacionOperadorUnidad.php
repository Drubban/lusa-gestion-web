<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Historial de que operador maneja que unidad. Toda alta/baja pasa por AsignacionService.
 */
class AsignacionOperadorUnidad extends Model
{
    use HasFactory;

    protected $table = 'asignacion_operador_unidad';

    protected $fillable = ['operador_id', 'unidad_id', 'fecha_inicio', 'fecha_fin', 'vigente'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'vigente' => 'boolean',
    ];

    public function operador(): BelongsTo
    {
        return $this->belongsTo(Operador::class);
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }
}
