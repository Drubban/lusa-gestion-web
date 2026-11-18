<?php

namespace App\Enums;

/**
 * Semaforo de mantenimiento segun dias desde el ultimo servicio.
 * Antes la misma logica estaba copiada (y duplicada dentro de un try/catch) en dos dashboards.
 */
enum SemaforoMantenimiento: string
{
    case Reciente = 'reciente';
    case AtencionMedia = 'atencion_media';
    case RequiereAtencion = 'requiere_atencion';
    case Urgente = 'urgente';
    case SinMantenimiento = 'sin_mantenimiento';

    public static function desdeDias(?int $dias): self
    {
        return match (true) {
            $dias === null => self::SinMantenimiento,
            $dias <= 7 => self::Reciente,
            $dias <= 14 => self::AtencionMedia,
            $dias <= 21 => self::RequiereAtencion,
            default => self::Urgente,
        };
    }

    public function etiqueta(): string
    {
        return match ($this) {
            self::Reciente => 'Reciente (hasta 7 dias)',
            self::AtencionMedia => 'Atencion media (8-14)',
            self::RequiereAtencion => 'Requiere atencion (15-21)',
            self::Urgente => 'Urgente (mas de 21)',
            self::SinMantenimiento => 'Sin mantenimiento',
        };
    }

    public function tono(): string
    {
        return match ($this) {
            self::Reciente => 'success',
            self::AtencionMedia => 'warning',
            self::RequiereAtencion => 'accent',
            self::Urgente => 'danger',
            self::SinMantenimiento => 'muted',
        };
    }
}
