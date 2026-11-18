<?php

namespace App\Enums;

enum RolUsuario: string
{
    case Admin = 'admin';
    case Supervisor = 'supervisor';
    case Departamento = 'departamento';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Supervisor => 'Supervisor',
            self::Departamento => 'Usuario de departamento',
        };
    }

    public function accedePanel(): bool
    {
        return $this !== self::Departamento;
    }

    public static function opciones(): array
    {
        return array_column(array_map(fn ($r) => ['v' => $r->value, 'e' => $r->etiqueta()], self::cases()), 'e', 'v');
    }
}
