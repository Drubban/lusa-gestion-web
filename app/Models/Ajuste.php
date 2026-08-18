<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ajuste extends Model
{
    use HasFactory;

    protected $table = 'ajustes';

    protected $fillable = [
        'zona',
        'fecha',
        'hora',
        'monto_total',
        'folio',
        'operador_id',
        'clave_operador',
        'unidad_id',
        'firmado',
        'created_by',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'string',
        'monto_total' => 'decimal:2',
        'firmado' => 'boolean',
    ];

    // Relaciones
    public function operador(): BelongsTo
    {
        return $this->belongsTo(Operador::class);
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(UsuarioDepartamento::class, 'created_by');
    }

    // Scopes
    public function scopeBuscar($query, $termino)
    {
        if ($termino) {
            return $query->where(function ($q) use ($termino) {
                $q->where('folio', 'LIKE', "%{$termino}%")
                  ->orWhere('zona', 'LIKE', "%{$termino}%")
                  ->orWhere('clave_operador', 'LIKE', "%{$termino}%")
                  ->orWhereHas('operador', function ($q2) use ($termino) {
                      $q2->where('nombre_completo', 'LIKE', "%{$termino}%");
                  })
                  ->orWhereHas('unidad', function ($q2) use ($termino) {
                      $q2->where('numero_economico', 'LIKE', "%{$termino}%");
                  });
            });
        }
        return $query;
    }

    public function scopeEntreFechas($query, $inicio, $fin)
    {
        if ($inicio && $fin) {
            return $query->whereBetween('fecha', [$inicio, $fin]);
        }
        return $query;
    }

    public function scopeFirmados($query, $firmado)
    {
        if ($firmado !== null && $firmado !== '') {
            return $query->where('firmado', $firmado);
        }
        return $query;
    }
}