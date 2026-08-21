<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tecnologia extends Model
{
    use HasFactory;

    protected $table = 'tecnologias';

    protected $fillable = [
        'unidad_id',
        'tipo',
        'nombre',
        'activo',
        'created_by',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Constantes para los tipos
    const TIPOS = [
        'barras' => 'Barras',
        'telpo' => 'Telpo',
        'gps' => 'GPS',
        'mdvr' => 'MDVR',
    ];

    // Relaciones
    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(UsuarioDepartamento::class, 'created_by');
    }

    // Relaciones polimórficas con cada tecnología
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

    // Métodos de ayuda
    public function getTipoNombreAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function getDatosAttribute(): ?array
    {
        switch ($this->tipo) {
            case 'barras':
                return $this->barras?->toArray();
            case 'telpo':
                return $this->telpo?->toArray();
            case 'gps':
                return $this->gps?->toArray();
            case 'mdvr':
                return $this->mdvr?->toArray();
            default:
                return null;
        }
    }

    // Scopes
    public function scopePorTipo($query, $tipo)
    {
        if ($tipo) {
            return $query->where('tipo', $tipo);
        }
        return $query;
    }

    public function scopePorUnidad($query, $unidadId)
    {
        if ($unidadId) {
            return $query->where('unidad_id', $unidadId);
        }
        return $query;
    }
}