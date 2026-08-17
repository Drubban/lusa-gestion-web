<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'icono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación con UsuarioDepartamento
    public function usuarios()
    {
        return $this->hasMany(UsuarioDepartamento::class);
    }
}