<?php

namespace App\Models;

use App\Models\Concerns\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Operador extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'operadores';

    protected $fillable = ['clave_operador', 'nombre_completo', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionOperadorUnidad::class);
    }

    public function asignacionVigente(): HasOne
    {
        return $this->hasOne(AsignacionOperadorUnidad::class)->where('vigente', true)->latestOfMany('fecha_inicio');
    }

    /** La zona del operador es la de la unidad que tiene asignada. */
    public function getZonaNombreAttribute(): string
    {
        return $this->asignacionVigente?->unidad?->zona?->nombre ?? 'Sin zona';
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->orderBy('nombre_completo');
    }

    public function scopeBuscar($query, ?string $termino)
    {
        if (! $termino) {
            return $query;
        }

        return $query->where(fn ($q) => $q
            ->where('clave_operador', 'ILIKE', "%{$termino}%")
            ->orWhere('nombre_completo', 'ILIKE', "%{$termino}%"));
    }
}
