<?php

namespace App\Support;

use App\Models\Departamento;
use App\Models\Operador;
use App\Models\Unidad;
use App\Models\Zona;

/**
 * Listas para <select> con el id ofuscado como valor. Centraliza consultas que
 * antes se repetian en cada create/edit de cada controlador.
 */
class Catalogos
{
    public static function unidades(bool $soloActivas = true): array
    {
        return Unidad::query()
            ->when($soloActivas, fn ($q) => $q->where('activo', true))
            ->orderBy('numero_economico')
            ->get(['id', 'numero_economico', 'nombre_unidad'])
            ->mapWithKeys(fn ($u) => [$u->hashid => $u->etiqueta])
            ->all();
    }

    public static function operadores(bool $soloActivos = true): array
    {
        return Operador::query()
            ->when($soloActivos, fn ($q) => $q->where('activo', true))
            ->orderBy('nombre_completo')
            ->get(['id', 'clave_operador', 'nombre_completo'])
            ->mapWithKeys(fn ($o) => [$o->hashid => "{$o->clave_operador} - {$o->nombre_completo}"])
            ->all();
    }

    public static function zonas(): array
    {
        return Zona::where('activo', true)->orderBy('nombre')->get(['id', 'nombre'])
            ->mapWithKeys(fn ($z) => [$z->hashid => ucfirst($z->nombre)])->all();
    }

    public static function departamentos(): array
    {
        return Departamento::activos()->get(['id', 'nombre'])
            ->mapWithKeys(fn ($d) => [$d->hashid => ucfirst($d->nombre)])->all();
    }
}
