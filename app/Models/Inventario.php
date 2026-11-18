<?php

namespace App\Models;

use App\Enums\CategoriaInventario;
use App\Models\Concerns\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Inventario extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'inventario';

    protected $fillable = [
        'fecha_entrega', 'departamento_id', 'area', 'nombre_recibe', 'clave_empleado',
        'categoria', 'nombre', 'marca', 'modelo', 'numero_serie', 'cantidad',
        'descripcion', 'imagen', 'created_by',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'cantidad' => 'integer',
        'categoria' => CategoriaInventario::class,
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen ? Storage::disk('public')->url($this->imagen) : null;
    }

    public function scopeFiltrar($query, array $filtros)
    {
        return $query
            ->when($filtros['categoria'] ?? null, fn ($q, $v) => $q->where('categoria', $v))
            ->when(($filtros['desde'] ?? null) && ($filtros['hasta'] ?? null), fn ($q) => $q->whereBetween('fecha_entrega', [$filtros['desde'], $filtros['hasta']]))
            ->when($filtros['search'] ?? null, fn ($q, $t) => $q->where(fn ($w) => $w
                ->where('nombre_recibe', 'ILIKE', "%{$t}%")
                ->orWhere('clave_empleado', 'ILIKE', "%{$t}%")
                ->orWhere('nombre', 'ILIKE', "%{$t}%")
                ->orWhere('numero_serie', 'ILIKE', "%{$t}%")
                ->orWhere('area', 'ILIKE', "%{$t}%")));
    }
}
