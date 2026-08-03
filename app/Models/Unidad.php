<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidades';  // ← Agrega esta línea

    protected $fillable = ['numero_economico', 'nombre_unidad', 'codigo_qr', 'token_qr', 'activo','zona_id' ];

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionOperadorUnidad::class);
    }

    public function asignacionVigente()
    {
        return $this->hasOne(AsignacionOperadorUnidad::class)
            ->where('vigente', true)
            ->whereNull('fecha_fin');
    }

    public function operadorActual()
    {
        return $this->asignacionVigente()->with('operador');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoDepartamento::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($unidad) {
            if (empty($unidad->codigo_qr)) {
                $unidad->codigo_qr = Str::uuid();
            }
            if (empty($unidad->token_qr)) {
                $unidad->token_qr = Str::random(20);
            }
        });
    }
}