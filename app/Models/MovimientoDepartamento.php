<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoDepartamento extends Model
{
    protected $table = 'movimiento_departamento';

    protected $fillable = [
        'unidad_id', 'departamento_id', 'usuario_departamento_id',
        'tipo', 'fecha_hora', 'observaciones', 'sincronizado'
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function usuario()
    {
        return $this->belongsTo(UsuarioDepartamento::class);
    }
}