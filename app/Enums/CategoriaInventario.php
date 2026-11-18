<?php

namespace App\Enums;

enum CategoriaInventario: string
{
    case Tarjetas = 'tarjetas';
    case EquiposComputo = 'equipos_computo';
    case Telefonia = 'telefonia';
    case RoutersSwitches = 'routers_switches';
    case Consumibles = 'consumibles';
    case Perifericos = 'perifericos';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Tarjetas => 'Tarjetas',
            self::EquiposComputo => 'Equipos de computo',
            self::Telefonia => 'Telefonia',
            self::RoutersSwitches => 'Routers / Switches',
            self::Consumibles => 'Consumibles',
            self::Perifericos => 'Perifericos',
        };
    }

    /** Las categorias de equipo requieren modelo y numero de serie unico. */
    public function esEquipo(): bool
    {
        return in_array($this, [self::EquiposComputo, self::Telefonia, self::RoutersSwitches], true);
    }

    public static function opciones(): array
    {
        $out = [];
        foreach (self::cases() as $c) {
            $out[$c->value] = $c->etiqueta();
        }

        return $out;
    }

    public static function valoresEquipo(): array
    {
        return array_values(array_map(fn ($c) => $c->value, array_filter(self::cases(), fn ($c) => $c->esEquipo())));
    }
}
