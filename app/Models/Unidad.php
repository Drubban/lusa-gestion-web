<?php

namespace App\Models;

use App\Models\Concerns\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Unidad extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'unidades';

    protected $fillable = ['numero_economico', 'nombre_unidad', 'zona_id', 'token_qr', 'activo'];

    protected $hidden = ['token_qr'];

    protected $casts = ['activo' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(function (Unidad $unidad) {
            $unidad->token_qr ??= self::nuevoTokenQr();
        });
    }

    public static function nuevoTokenQr(): string
    {
        return Str::random(40);
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionOperadorUnidad::class);
    }

    public function asignacionVigente(): HasOne
    {
        return $this->hasOne(AsignacionOperadorUnidad::class)->where('vigente', true)->latestOfMany('fecha_inicio');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoDepartamento::class);
    }

    public function documentosMantenimiento(): HasMany
    {
        return $this->hasMany(DocumentoMantenimiento::class);
    }

    public function ultimoMantenimiento(): HasOne
    {
        return $this->hasOne(DocumentoMantenimiento::class)->latestOfMany('fecha');
    }

    public function documentosCapacitacion(): HasMany
    {
        return $this->hasMany(DocumentoCapacitacion::class);
    }

    public function tecnologias(): HasMany
    {
        return $this->hasMany(Tecnologia::class);
    }

    public function agendamientos(): HasMany
    {
        return $this->hasMany(AgendamientoMantenimiento::class);
    }

    public function agendamientoPendiente(): HasOne
    {
        return $this->hasOne(AgendamientoMantenimiento::class)->where('estado', 'pendiente')->oldestOfMany('fecha_agendada');
    }

    public function revisiones(): HasMany
    {
        return $this->hasMany(RevisionOptocontrol::class);
    }

    public function getOperadorActualAttribute(): ?Operador
    {
        return $this->asignacionVigente?->operador;
    }

    public function getEtiquetaAttribute(): string
    {
        return trim($this->numero_economico . ($this->nombre_unidad ? ' - ' . $this->nombre_unidad : ''));
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true)->orderBy('numero_economico');
    }

    public function scopeBuscar($query, ?string $termino)
    {
        if (! $termino) {
            return $query;
        }

        return $query->where(fn ($q) => $q
            ->where('numero_economico', 'ILIKE', "%{$termino}%")
            ->orWhere('nombre_unidad', 'ILIKE', "%{$termino}%")
            ->orWhereHas('asignacionVigente.operador', fn ($o) => $o->where('nombre_completo', 'ILIKE', "%{$termino}%")));
    }
}
