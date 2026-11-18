<?php

namespace App\Models;

use App\Enums\TipoMovimiento;
use App\Models\Concerns\HasHashid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Evento de entrada o salida de una unidad a un departamento.
 * Una salida apunta a su entrada (entrada_id) y guarda la duracion de la estancia.
 */
class MovimientoDepartamento extends Model
{
    use HasHashid;

    protected $table = 'movimiento_departamento';

    protected $fillable = [
        'uuid', 'unidad_id', 'departamento_id', 'usuario_departamento_id',
        'tipo', 'entrada_id', 'duracion_minutos', 'fecha_hora', 'observaciones',
    ];

    protected $casts = [
        'tipo' => TipoMovimiento::class,
        'fecha_hora' => 'datetime',
        'duracion_minutos' => 'integer',
    ];

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_departamento_id');
    }

    public function entrada(): BelongsTo
    {
        return $this->belongsTo(self::class, 'entrada_id');
    }

    public function salida(): HasOne
    {
        return $this->hasOne(self::class, 'entrada_id');
    }

    /** Entradas que aun no tienen salida registrada. */
    public function scopeEstanciasAbiertas($query)
    {
        return $query->where('tipo', TipoMovimiento::Entrada->value)
            ->whereNotExists(fn ($q) => $q->selectRaw('1')
                ->from('movimiento_departamento as s')
                ->whereColumn('s.entrada_id', 'movimiento_departamento.id'));
    }

    public function getDuracionLegibleAttribute(): ?string
    {
        $min = $this->duracion_minutos;
        if ($min === null) {
            return null;
        }

        return $min >= 60 ? intdiv($min, 60) . ' h ' . ($min % 60) . ' min' : $min . ' min';
    }
}
