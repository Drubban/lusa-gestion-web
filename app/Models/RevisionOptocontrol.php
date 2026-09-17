<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevisionOptocontrol extends Model
{
    protected $table = 'revisiones_optocontrol';

    protected $fillable = [
        'disp',
        'unidad_id',
        'nombre_unidad',
        'fecha_reporte',
        'hora_entrada',
        'hora_salida',
        'ruta',
        'problema',
        'solucion',
        'status',
        'tiempo_estancia',
        'responsable',
        'created_by',
    ];

    protected $casts = [
        'fecha_reporte'   => 'date',
        'tiempo_estancia' => 'decimal:2',
    ];

    const DISP_OPCIONES = ['barras', 'telpo', 'gps', 'camaras', 'revision', 'taller'];

    const STATUS_OPCIONES = ['pendiente', 'en_proceso', 'resuelto', 'cancelado'];

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(UsuarioDepartamento::class, 'created_by');
    }

    public function getDispLabelAttribute(): string
    {
        $labels = [
            'barras'   => 'Barras',
            'telpo'    => 'Telpo',
            'gps'      => 'GPS',
            'camaras'  => 'Camaras',
            'revision' => 'Revision',
            'taller'   => 'Taller',
        ];
        return $labels[$this->disp] ?? ucfirst($this->disp);
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pendiente'  => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'resuelto'   => 'Resuelto',
            'cancelado'  => 'Cancelado',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'pendiente'  => 'warning',
            'en_proceso' => 'info',
            'resuelto'   => 'success',
            'cancelado'  => 'danger',
        ];
        return $colors[$this->status] ?? 'secondary';
    }
}