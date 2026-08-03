<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Operador extends Model
{
    use HasFactory;

    protected $table = 'operadores';

    protected $fillable = [
        'clave_operador',
        'nombre_completo',
        'activo',
    ];

    // Relación con asignaciones
    public function asignaciones()
    {
        return $this->hasMany(AsignacionOperadorUnidad::class);
    }

    // Asignación vigente
    public function asignacionVigente()
    {
        return $this->hasOne(AsignacionOperadorUnidad::class)
            ->where('vigente', true)
            ->whereNull('fecha_fin');
    }

    // Unidad actual
    public function unidadActual()
    {
        return $this->asignacionVigente()->with('unidad');
    }

    // Zona obtenida a través de la unidad actual
    public function getZonaAttribute()
    {
        $unidad = $this->asignacionVigente?->unidad;
        return $unidad?->zona; // Asumiendo que Unidad tiene zona_id y relación con Zona
    }

    // Helper para mostrar nombre de zona
    public function getZonaNombreAttribute()
    {
        return $this->zona?->nombre ?? 'Sin zona';
    }
    public function __construct(array $attributes = [])
{
    parent::__construct($attributes);
    Log::debug('Operador modelo instanciado, tabla: ' . $this->getTable());
}
}