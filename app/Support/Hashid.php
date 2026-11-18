<?php

namespace App\Support;

/**
 * Ofusca ids numericos para URLs y API sin dependencias externas
 * (antes requeria el paquete hashids/hashids y la extension bcmath).
 *
 * Funcionamiento:
 * - El id (entero de 64 bits) se divide en dos mitades de 32 bits.
 * - Se aplica una red Feistel de 4 rondas cuya funcion es HMAC-SHA256 con una llave
 *   derivada de la sal + el contexto (tabla). Es una permutacion: reversible y sin colisiones.
 * - Cada mitad se escribe en base N con el alfabeto configurado (6 caracteres por mitad).
 * - Al decodificar se exige que los 16 bits altos sean cero: un texto inventado casi
 *   nunca produce un id valido.
 */
class Hashid
{
    private const RONDAS = 4;
    private const DIGITOS_POR_MITAD = 6;
    private const MASCARA_32 = 0xFFFFFFFF;

    /** @var array<string, string> llaves por contexto */
    private static array $llaves = [];

    public static function encode(int $id, string $contexto): string
    {
        if ($id <= 0 || $id >= (1 << 48)) {
            throw new \InvalidArgumentException('Id fuera de rango para ofuscar.');
        }
        $llave = self::llave($contexto);
        $izq = ($id >> 32) & self::MASCARA_32;
        $der = $id & self::MASCARA_32;

        for ($r = 0; $r < self::RONDAS; $r++) {
            [$izq, $der] = [$der, $izq ^ self::ronda($der, $r, $llave)];
        }

        return self::aBase($izq) . self::aBase($der);
    }

    public static function decode(?string $hash, string $contexto): ?int
    {
        $longitud = self::DIGITOS_POR_MITAD * 2;
        if ($hash === null || strlen($hash) !== $longitud) {
            return null;
        }
        $izq = self::deBase(substr($hash, 0, self::DIGITOS_POR_MITAD));
        $der = self::deBase(substr($hash, self::DIGITOS_POR_MITAD));
        if ($izq === null || $der === null) {
            return null;
        }

        $llave = self::llave($contexto);
        for ($r = self::RONDAS - 1; $r >= 0; $r--) {
            [$izq, $der] = [$der ^ self::ronda($izq, $r, $llave), $izq];
        }

        // Los 16 bits altos deben ser cero (ids validos menores a 2^48)
        if (($izq >> 16) !== 0) {
            return null;
        }
        $id = ($izq << 32) | $der;

        return $id > 0 ? $id : null;
    }

    private static function ronda(int $mitad, int $ronda, string $llave): int
    {
        $mac = hash_hmac('sha256', pack('N', $mitad) . chr($ronda), $llave, true);

        return unpack('N', substr($mac, 0, 4))[1];
    }

    private static function llave(string $contexto): string
    {
        return self::$llaves[$contexto] ??= hash_hmac('sha256', $contexto, (string) config('hashids.salt'), true);
    }

    private static function alfabeto(): string
    {
        return (string) config('hashids.alphabet', 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789');
    }

    private static function aBase(int $valor): string
    {
        $alfabeto = self::alfabeto();
        $base = strlen($alfabeto);
        $salida = '';
        for ($i = 0; $i < self::DIGITOS_POR_MITAD; $i++) {
            $salida = $alfabeto[$valor % $base] . $salida;
            $valor = intdiv($valor, $base);
        }

        return $salida;
    }

    private static function deBase(string $texto): ?int
    {
        $alfabeto = self::alfabeto();
        $base = strlen($alfabeto);
        $valor = 0;
        for ($i = 0, $n = strlen($texto); $i < $n; $i++) {
            $pos = strpos($alfabeto, $texto[$i]);
            if ($pos === false) {
                return null;
            }
            $valor = $valor * $base + $pos;
        }

        return $valor <= self::MASCARA_32 ? $valor : null;
    }
}
