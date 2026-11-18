<?php

namespace App\Models;

use App\Enums\RolUsuario;
use App\Models\Concerns\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Usuario unico del sistema (panel web y app movil).
 */
class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, HasHashid, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre_usuario',
        'nombre_completo',
        'email',
        'password',
        'departamento_id',
        'rol',
        'puede_generar_documentos',
        'activo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
            'puede_generar_documentos' => 'boolean',
            'rol' => RolUsuario::class,
            'ultimo_acceso_at' => 'datetime',
            'bloqueado_hasta' => 'datetime',
        ];
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoDepartamento::class, 'usuario_departamento_id');
    }

    public function esAdmin(): bool
    {
        return $this->rol === RolUsuario::Admin;
    }

    public function accedePanel(): bool
    {
        return $this->activo && $this->rol?->accedePanel();
    }

    public function estaBloqueado(): bool
    {
        return $this->bloqueado_hasta !== null && $this->bloqueado_hasta->isFuture();
    }

    public function getNombreVisibleAttribute(): string
    {
        return $this->nombre_completo ?: $this->nombre_usuario;
    }

    /**
     * Permisos (abilities) que se asignan al token de la app segun el perfil.
     */
    public function habilidadesApp(): array
    {
        $habilidades = ['catalogos:leer', 'movimientos:escribir', 'agendamientos:leer', 'revisiones:escribir', 'inventario:leer'];
        if ($this->puede_generar_documentos || $this->rol !== RolUsuario::Departamento) {
            $habilidades[] = 'documentos:escribir';
        }
        if ($this->rol !== RolUsuario::Departamento) {
            $habilidades[] = 'agendamientos:escribir';
            $habilidades[] = 'resumen:leer';
        }

        return $habilidades;
    }
}
