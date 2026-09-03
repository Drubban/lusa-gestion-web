<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendamientoMantenimiento extends Model
{
    use HasFactory;

    protected $table = 'agendamientos_mantenimiento';

    protected $fillable = [
        'unidad_id',
        'fecha_agendada',
        'estado',
        'fecha_cumplimiento',
        'observaciones',
        'created_by',
    ];

    protected $casts = [
        'fecha_agendada' => 'date',
        'fecha_cumplimiento' => 'date',
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

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCumplidos($query)
    {
        return $query->where('estado', 'cumplido');
    }

    public function scopeNoCumplidos($query)
    {
        return $query->where('estado', 'no_cumplido');
    }

    public function scopeReagendados($query)
    {
        return $query->where('estado', 'reagendado');
    }

    // Métodos de ayuda
    public function getEstadoBadgeAttribute(): string
    {
        return match ($this->estado) {
            'pendiente' => '<span class="badge bg-warning text-dark">Pendiente</span>',
            'cumplido' => '<span class="badge bg-success">Cumplido</span>',
            'no_cumplido' => '<span class="badge bg-danger">No cumplido</span>',
            'reagendado' => '<span class="badge bg-info">Reagendado</span>',
            default => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }

    public function getDiasRestantesAttribute(): ?int
    {
        if (!$this->fecha_agendada) {
            return null;
        }
        return now()->diffInDays($this->fecha_agendada, false);
    }
}