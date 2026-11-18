<?php

namespace App\Enums;

enum TipoMovimiento: string
{
    case Entrada = 'entrada';
    case Salida = 'salida';

    public function etiqueta(): string
    {
        return $this === self::Entrada ? 'Entrada' : 'Salida';
    }
}
