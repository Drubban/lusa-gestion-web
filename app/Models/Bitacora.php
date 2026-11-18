<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bitacora extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'bitacora';

    protected $fillable = ['usuario_id', 'accion', 'entidad', 'entidad_id', 'canal', 'ip', 'detalle'];

    protected $casts = ['detalle' => 'array'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
