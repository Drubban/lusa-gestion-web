<?php

namespace App\Enums;

enum TipoTecnologia: string
{
    case Barras = 'barras';
    case Telpo = 'telpo';
    case Gps = 'gps';
    case Mdvr = 'mdvr';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Barras => 'Barras',
            self::Telpo => 'Telpo',
            self::Gps => 'GPS',
            self::Mdvr => 'MDVR',
        };
    }

    /** Campos propios de cada subtipo: [campo_formulario => columna_tabla]. */
    public function campos(): array
    {
        return match ($this) {
            self::Barras => ['id_barra' => 'Id barra', 'barras' => 'Barras', 'telefono' => 'Telefono', 'plan' => 'Plan'],
            self::Telpo => ['telpo' => 'Telpo', 'imei_telpo' => 'IMEI Telpo', 'imei_antes' => 'IMEI anterior', 'v_apk' => 'Version APK', 'telefono' => 'Telefono', 'plan' => 'Plan', 'costo_plan' => 'Costo del plan'],
            self::Gps => ['imei_gps' => 'IMEI GPS', 'telefono' => 'Telefono', 'plan' => 'Plan'],
            self::Mdvr => ['dvr' => 'DVR', 'modelo' => 'Modelo', 'camaras' => 'Camaras', 'memoria' => 'Memoria'],
        };
    }

    public function relacion(): string
    {
        return $this->value;
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
