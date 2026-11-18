<?php

namespace App\Models;

use App\Models\Concerns\HasHashid;
use App\Models\Concerns\TieneFirmas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoCapacitacion extends Model
{
    use HasFactory, HasHashid, TieneFirmas;

    public const FIRMAS = ['operador' => 'Firma del operador', 'instructor' => 'Firma del instructor / Ing. a cargo'];

    protected $table = 'documento_capacitacion';

    protected $fillable = [
        'uuid', 'unidad_id', 'operador_id', 'usuario_id',
        'tipo_capacitacion', 'fecha', 'hora', 'fecha_vencimiento',
        'instructor', 'duracion_horas', 'observaciones', 'vigente',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_vencimiento' => 'date',
        'duracion_horas' => 'decimal:2',
        'vigente' => 'boolean',
    ];

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(Operador::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function getFolioAttribute(): string
    {
        return 'CAP-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
