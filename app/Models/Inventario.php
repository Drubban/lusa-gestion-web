<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventario';

    protected $fillable = [
        'fecha_entrega',
        'departamento_id',
        // 'zona_id',
        'area',
        'nombre_recibe',
        'clave_empleado',
        'categoria',
        'nombre_equipo',
        'marca',
        'modelo',
        'numero_serie',
        'datos_extra',
        'nombre_producto',
        'marca_producto',
        'cantidad',
        'descripcion',
        'imagen',
        'created_by',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'cantidad' => 'integer',
    ];

    // Constantes para categorías
    const CATEGORIAS = [
        'tarjetas' => 'Tarjetas',
        'equipos_computo' => 'Equipos de Cómputo',
        'telefonia' => 'Telefonía',
        'routers_switches' => 'Routers/Switches',
        'consumibles' => 'Consumibles',
        'perifericos' => 'Periféricos',
    ];

    // Categorías que requieren campos de equipo
    const CATEGORIAS_EQUIPO = [
        'equipos_computo',
        'telefonia',
        'routers_switches',
    ];

    // Categorías que requieren campos de producto
    const CATEGORIAS_PRODUCTO = [
        'tarjetas',
        'consumibles',
        'perifericos',
    ];

    // Relaciones
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(UsuarioDepartamento::class, 'created_by');
    }

    // Métodos de ayuda
    public function esCategoriaEquipo(): bool
    {
        return in_array($this->categoria, self::CATEGORIAS_EQUIPO);
    }

    public function esCategoriaProducto(): bool
    {
        return in_array($this->categoria, self::CATEGORIAS_PRODUCTO);
    }

    public function getNombreCategoriaAttribute(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? $this->categoria;
    }

    // Scopes
    public function scopePorCategoria($query, $categoria)
    {
        if ($categoria) {
            return $query->where('categoria', $categoria);
        }
        return $query;
    }

    public function scopeEntreFechas($query, $inicio, $fin)
    {
        if ($inicio && $fin) {
            return $query->whereBetween('fecha_entrega', [$inicio, $fin]);
        }
        return $query;
    }

    public function scopeBuscar($query, $termino)
    {
        if ($termino) {
            return $query->where(function ($q) use ($termino) {
                $q->where('nombre_recibe', 'LIKE', "%{$termino}%")
                  ->orWhere('clave_empleado', 'LIKE', "%{$termino}%")
                  ->orWhere('nombre_equipo', 'LIKE', "%{$termino}%")
                  ->orWhere('nombre_producto', 'LIKE', "%{$termino}%")
                  ->orWhere('numero_serie', 'LIKE', "%{$termino}%")
                  ->orWhere('area', 'LIKE', "%{$termino}%");
            });
        }
        return $query;
    }

    // Accessor para obtener la URL de la imagen
    public function getImagenUrlAttribute(): ?string
    {
        if ($this->imagen) {
            return asset('storage/' . $this->imagen);
        }
        return null;
    }
}