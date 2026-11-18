<?php

namespace App\Models;

use App\Models\Concerns\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ajuste extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'ajustes';

    protected $fillable = ['zona_id', 'fecha', 'hora', 'monto_total', 'folio', 'operador_id', 'unidad_id', 'firmado', 'created_by'];

    protected $casts = [
        'fecha' => 'date',
        'monto_total' => 'decimal:2',
        'firmado' => 'boolean',
    ];

    public function operador(): BelongsTo
    {
        return $this->belongsTo(Operador::class);
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function scopeFiltrar($query, array $f)
    {
        return $query
            ->when($f['search'] ?? null, fn ($q, $t) => $q->where(fn ($w) => $w
                ->where('folio', 'ILIKE', "%{$t}%")
                ->orWhereHas('operador', fn ($o) => $o->where('nombre_completo', 'ILIKE', "%{$t}%")->orWhere('clave_operador', 'ILIKE', "%{$t}%"))
                ->orWhereHas('unidad', fn ($u) => $u->where('numero_economico', 'ILIKE', "%{$t}%"))))
            ->when(($f['desde'] ?? null) && ($f['hasta'] ?? null), fn ($q) => $q->whereBetween('fecha', [$f['desde'], $f['hasta']]))
            ->when(isset($f['firmado']) && $f['firmado'] !== '', fn ($q) => $q->where('firmado', (bool) $f['firmado']));
    }
}
