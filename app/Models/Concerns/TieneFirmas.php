<?php

namespace App\Models\Concerns;

use App\Models\FirmaDigital;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait TieneFirmas
{
    public function firmas(): MorphMany
    {
        return $this->morphMany(FirmaDigital::class, 'modelo', 'modelo_type', 'modelo_id');
    }

    public function firma(string $tipo): ?FirmaDigital
    {
        return $this->firmas->firstWhere('tipo_firma', $tipo);
    }

    /**
     * Guarda o reemplaza una firma. Acepta base64 puro o data URI.
     */
    public function guardarFirma(string $tipo, ?string $base64): void
    {
        if (! $base64) {
            return;
        }
        $limpio = preg_replace('#^data:image/\w+;base64,#', '', $base64);
        $this->firmas()->updateOrCreate(
            ['tipo_firma' => $tipo],
            ['firma_base64' => $limpio, 'hash_sha256' => hash('sha256', $limpio)],
        );
    }
}
