<?php
// app/Models/UsuarioDepartamento.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class UsuarioDepartamento extends Authenticatable
{
    use HasFactory;
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios_departamento';

    protected $fillable = [
        'nombre_usuario',
        'password',
        'departamento_id',
        'puede_generar_documentos',
        'activo',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'puede_generar_documentos' => 'boolean',
    ];

    // Relación con departamento
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    // Relación con movimientos
    public function movimientos()
    {
        return $this->hasMany(MovimientoDepartamento::class, 'usuario_departamento_id');
    }
}