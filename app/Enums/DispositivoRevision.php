<?php

namespace App\Enums;

enum DispositivoRevision: string
{
    case Barras = 'barras';
    case Telpo = 'telpo';
    case Gps = 'gps';
    case Camaras = 'camaras';
    case Revision = 'revision';
    case Taller = 'taller';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Gps => 'GPS',
            self::Camaras => 'Camaras',
            self::Revision => 'Revision',
            default => ucfirst($this->value),
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
