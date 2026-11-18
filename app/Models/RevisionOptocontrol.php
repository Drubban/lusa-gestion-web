<?php

namespace App\Models;

use App\Enums\DispositivoRevision;
use App\Enums\EstadoRevision;
use App\Models\Concerns\HasHashid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevisionOptocontrol extends Model
{
    use HasHashid;

    protected $table = 'revisiones_optocontrol';

    protected $fillable = [
        'disp', 'unidad_id', 'fecha_reporte', 'hora_entrada', 'hora_salida', 'ruta',
        'problema', 'solucion', 'status', 'tiempo_estancia', 'responsable', 'created_by',
    ];

    protected $casts = [
        'fecha_reporte' => 'date',
        'tiempo_estancia' => 'decimal:2',
        'disp' => DispositivoRevision::class,
        'status' => EstadoRevision::class,
    ];

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function scopeFiltrar($query, array $f)
    {
        return $query
            ->when($f['disp'] ?? null, fn ($q, $v) => $q->where('disp', $v))
            ->when($f['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($f['unidad_id'] ?? null, fn ($q, $v) => $q->where('unidad_id', $v))
            ->when($f['desde'] ?? null, fn ($q, $v) => $q->whereDate('fecha_reporte', '>=', $v))
            ->when($f['hasta'] ?? null, fn ($q, $v) => $q->whereDate('fecha_reporte', '<=', $v))
            ->when($f['search'] ?? null, fn ($q, $t) => $q->where(fn ($w) => $w
                ->where('problema', 'ILIKE', "%{$t}%")
                ->orWhere('solucion', 'ILIKE', "%{$t}%")
                ->orWhere('ruta', 'ILIKE', "%{$t}%")
                ->orWhere('responsable', 'ILIKE', "%{$t}%")
                ->orWhereHas('unidad', fn ($u) => $u->buscar($t))));
    }
}
