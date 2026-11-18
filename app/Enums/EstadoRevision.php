<?php

namespace App\Enums;

enum EstadoRevision: string
{
    case Pendiente = 'pendiente';
    case EnProceso = 'en_proceso';
    case Resuelto = 'resuelto';
    case Cancelado = 'cancelado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::EnProceso => 'En proceso',
            self::Resuelto => 'Resuelto',
            self::Cancelado => 'Cancelado',
        };
    }

    public function tono(): string
    {
        return match ($this) {
            self::Pendiente => 'warning',
            self::EnProceso => 'info',
            self::Resuelto => 'success',
            self::Cancelado => 'danger',
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
