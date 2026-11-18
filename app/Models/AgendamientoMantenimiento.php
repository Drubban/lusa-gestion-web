<?php

namespace App\Models;

use App\Enums\EstadoAgendamiento;
use App\Enums\MotivoNoPresentado;
use App\Models\Concerns\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendamientoMantenimiento extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'agendamientos_mantenimiento';

    protected $fillable = [
        'unidad_id', 'fecha_agendada', 'estado', 'fecha_cumplimiento', 'observaciones',
        'motivo_no_presentado', 'fecha_reprogramada', 'reportado_por', 'created_by',
    ];

    protected $casts = [
        'fecha_agendada' => 'date',
        'fecha_cumplimiento' => 'date',
        'fecha_reprogramada' => 'date',
        'estado' => EstadoAgendamiento::class,
    ];

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function reportadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'reportado_por');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', EstadoAgendamiento::Pendiente->value);
    }

    /** Motivo como enum; registros historicos con texto libre devuelven null. */
    public function getMotivoAttribute(): ?MotivoNoPresentado
    {
        return MotivoNoPresentado::tryFrom((string) $this->motivo_no_presentado);
    }

    public function getMotivoEtiquetaAttribute(): ?string
    {
        return $this->motivo?->etiqueta() ?? $this->motivo_no_presentado;
    }

    public function getDiasRestantesAttribute(): ?int
    {
        return $this->fecha_agendada ? (int) now()->startOfDay()->diffInDays($this->fecha_agendada, false) : null;
    }

    public function scopeFiltrar($query, array $f)
    {
        return $query
            ->when($f['estado'] ?? null, fn ($q, $v) => $q->where('estado', $v))
            ->when($f['unidad_id'] ?? null, fn ($q, $v) => $q->where('unidad_id', $v))
            ->when($f['motivo'] ?? null, fn ($q, $v) => $q->where('motivo_no_presentado', $v))
            ->when(($f['desde'] ?? null) && ($f['hasta'] ?? null), fn ($q) => $q->whereBetween('fecha_agendada', [$f['desde'], $f['hasta']]))
            ->when($f['search'] ?? null, fn ($q, $t) => $q->whereHas('unidad', fn ($u) => $u->buscar($t)));
    }
}
