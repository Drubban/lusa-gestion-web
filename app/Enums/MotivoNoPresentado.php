<?php

namespace App\Enums;

/**
 * Antes esta lista estaba copiada en 3 controladores y en el modelo.
 */
enum MotivoNoPresentado: string
{
    case Taller = 'taller';
    case Averia = 'averia';
    case FaltaOperador = 'falta_operador';
    case Documentacion = 'documentacion';
    case Cliente = 'cliente';
    case Otros = 'otros';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Taller => 'En taller',
            self::Averia => 'Averia mecanica',
            self::FaltaOperador => 'Falta de operador',
            self::Documentacion => 'Falta de documentacion',
            self::Cliente => 'Unidad en ruta con cliente',
            self::Otros => 'Otro motivo',
        };
    }

    public static function opciones(): array
    {
        $out = [];
        foreach (self::cases() as $c) {
            $out[$c->value] = $c->etiqueta();
        }

        return $out;
    }
}
