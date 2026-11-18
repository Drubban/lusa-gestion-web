<?php

namespace App\Enums;

enum EstadoAgendamiento: string
{
    case Pendiente = 'pendiente';
    case Cumplido = 'cumplido';
    case NoCumplido = 'no_cumplido';
    case Reagendado = 'reagendado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Cumplido => 'Cumplido',
            self::NoCumplido => 'No cumplido',
            self::Reagendado => 'Reagendado',
        };
    }

    public function tono(): string
    {
        return match ($this) {
            self::Pendiente => 'warning',
            self::Cumplido => 'success',
            self::NoCumplido => 'danger',
            self::Reagendado => 'info',
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
