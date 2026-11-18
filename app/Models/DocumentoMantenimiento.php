<?php

namespace App\Models;

use App\Models\Concerns\HasHashid;
use App\Models\Concerns\TieneFirmas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoMantenimiento extends Model
{
    use HasFactory, HasHashid, TieneFirmas;

    public const TECNOLOGIAS = ['barras' => 'Barras', 'gps' => 'GPS', 'varilla' => 'Varilla', 'telpo' => 'Telpo', 'mdvr' => 'MDVR', 'camaras' => 'Camaras'];

    public const FIRMAS = ['operador' => 'Firma del operador', 'ingeniero' => 'Firma del ingeniero a cargo', 'tabulacion' => 'Firma de tabulacion'];

    protected $table = 'documento_mantenimiento';

    protected $fillable = [
        'uuid', 'unidad_id', 'operador_id', 'usuario_id',
        'tecnologia_reportada', 'estado_camaras', 'prueba_barras', 'comentarios',
        'fecha', 'hora', 'veces_adeudo', 'observaciones_adeudo', 'vigente',
    ];

    protected $casts = [
        'fecha' => 'date',
        'vigente' => 'boolean',
        'veces_adeudo' => 'integer',
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

    public function getTecnologiasAttribute(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->tecnologia_reportada))));
    }

    public function getFolioAttribute(): string
    {
        return 'MTO-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
