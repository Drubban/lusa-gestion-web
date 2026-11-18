<?php

namespace App\Support;

use Generator;
use RuntimeException;

/**
 * Lee CSV con cabeceras normalizadas (minusculas, sin BOM) y detecta el separador.
 * Reemplaza tres implementaciones distintas que habia en ImportacionController.
 */
class LectorCsv
{
    private array $cabeceras = [];

    public function __construct(private readonly string $ruta) {}

    public function cabeceras(): array
    {
        return $this->cabeceras;
    }

    /**
     * @return Generator<int, array<string, string>> numero de linea => fila asociativa
     */
    public function filas(array $requeridas = []): Generator
    {
        $handle = fopen($this->ruta, 'r');
        if ($handle === false) {
            throw new RuntimeException('No se pudo abrir el archivo.');
        }

        try {
            $primera = fgets($handle);
            if ($primera === false) {
                throw new RuntimeException('El archivo esta vacio.');
            }
            $separador = substr_count($primera, ';') > substr_count($primera, ',') ? ';' : ',';
            $this->cabeceras = array_map(
                fn ($c) => strtolower(trim($c, " \t\n\r\0\x0B\xEF\xBB\xBF\"")),
                str_getcsv($primera, $separador),
            );

            $faltan = array_diff($requeridas, $this->cabeceras);
            if ($faltan) {
                throw new RuntimeException('Faltan columnas: ' . implode(', ', $faltan) . '. Detectadas: ' . implode(', ', $this->cabeceras));
            }

            $linea = 1;
            while (($datos = fgetcsv($handle, 0, $separador)) !== false) {
                $linea++;
                if (! array_filter($datos, fn ($v) => trim((string) $v) !== '')) {
                    continue;
                }
                $fila = [];
                foreach ($this->cabeceras as $i => $c) {
                    $fila[$c] = self::limpiar($datos[$i] ?? '');
                }
                yield $linea => $fila;
            }
        } finally {
            fclose($handle);
        }
    }

    public static function limpiar(?string $texto): string
    {
        $texto = trim((string) $texto);
        if ($texto !== '' && ! mb_check_encoding($texto, 'UTF-8')) {
            $texto = mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');
        }

        return $texto;
    }

    public static function booleano(?string $valor, bool $porDefecto = true): bool
    {
        if ($valor === null || $valor === '') {
            return $porDefecto;
        }

        return in_array(mb_strtolower($valor), ['1', 'true', 'si', 'sí', 'activo', 'yes'], true);
    }
}
