<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida que la firma sea una imagen PNG en base64 (puro o data URI) de tamano razonable.
 */
class FirmaPng implements ValidationRule
{
    public function __construct(private readonly int $maxBytes = 512 * 1024) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('La firma no tiene un formato valido.');

            return;
        }
        $limpio = preg_replace('#^data:image/png;base64,#', '', $value);
        $binario = base64_decode($limpio, true);
        if ($binario === false || ! str_starts_with($binario, "\x89PNG\r\n\x1a\n")) {
            $fail('La firma debe ser una imagen PNG valida.');

            return;
        }
        if (strlen($binario) > $this->maxBytes) {
            $fail('La imagen de la firma es demasiado grande.');
        }
    }
}
