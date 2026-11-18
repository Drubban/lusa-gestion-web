<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Error esperado de negocio (mensaje seguro para mostrar al usuario).
 * Se traduce a 422 en la API y a un mensaje flash en el panel.
 */
class ReglaNegocioException extends RuntimeException
{
    public function __construct(string $message, private readonly array $contexto = [])
    {
        parent::__construct($message);
    }

    public function contexto(): array
    {
        return $this->contexto;
    }
}
