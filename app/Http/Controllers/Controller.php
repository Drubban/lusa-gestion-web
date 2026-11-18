<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /** Columnas permitidas para ordenar; evita inyectar columnas arbitrarias en ORDER BY. */
    protected function orden(?string $columna, ?string $direccion, array $permitidas, string $porDefecto): array
    {
        $columna = in_array($columna, $permitidas, true) ? $columna : $porDefecto;
        $direccion = $direccion === 'desc' ? 'desc' : 'asc';

        return [$columna, $direccion];
    }
}
