<?php

namespace App\Models;

use App\Models\Concerns\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    use HasFactory, HasHashid;

    protected $fillable = ['nombre', 'descripcion', 'color', 'icono', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoDepartamento::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->orderBy('nombre');
    }
}
