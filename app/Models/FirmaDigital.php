<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Firma manuscrita (PNG en base64) ligada a cualquier documento.
 */
class FirmaDigital extends Model
{
    protected $table = 'firmas_digitales';

    protected $fillable = ['tipo_firma', 'firma_base64', 'hash_sha256'];

    protected $hidden = ['firma_base64'];

    public function modelo(): MorphTo
    {
        return $this->morphTo('modelo', 'modelo_type', 'modelo_id');
    }

    public function getDataUriAttribute(): string
    {
        $base64 = $this->firma_base64;

        return str_starts_with($base64, 'data:') ? $base64 : 'data:image/png;base64,' . $base64;
    }

    public function integridadValida(): bool
    {
        return $this->hash_sha256 === null || hash_equals($this->hash_sha256, hash('sha256', $this->firma_base64));
    }
}
