<?php

namespace App\Models;

use App\Enums\TipoTecnologia;
use App\Models\Concerns\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Tecnologia instalada en una unidad. Los datos propios de cada tipo viven en
 * su tabla (barras, telpos, gps, mdvrs) con relacion 1 a 1.
 */
class Tecnologia extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'tecnologias';

    protected $fillable = ['unidad_id', 'tipo', 'nombre', 'activo', 'created_by'];

    protected $casts = [
        'activo' => 'boolean',
        'tipo' => TipoTecnologia::class,
    ];

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function barras(): HasOne
    {
        return $this->hasOne(Barra::class);
    }

    public function telpo(): HasOne
    {
        return $this->hasOne(Telpo::class);
    }

    public function gps(): HasOne
    {
        return $this->hasOne(Gps::class);
    }

    public function mdvr(): HasOne
    {
        return $this->hasOne(Mdvr::class);
    }

    /** Registro del subtipo correspondiente (Barra, Telpo, Gps o Mdvr). */
    public function detalle(): ?Model
    {
        return $this->tipo ? $this->{$this->tipo->relacion()} : null;
    }

    public function getDatosAttribute(): array
    {
        if (! $this->tipo) {
            return [];
        }
        $detalle = $this->detalle();
        $out = [];
        foreach ($this->tipo->campos() as $campo => $etiqueta) {
            $out[$etiqueta] = $detalle?->{$campo};
        }

        return $out;
    }

    public function scopeFiltrar($query, array $f)
    {
        return $query
            ->when($f['tipo'] ?? null, fn ($q, $v) => $q->where('tipo', $v))
            ->when($f['unidad_id'] ?? null, fn ($q, $v) => $q->where('unidad_id', $v))
            ->when($f['search'] ?? null, fn ($q, $t) => $q->whereHas('unidad', fn ($u) => $u->buscar($t)));
    }
}
