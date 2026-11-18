@extends('layouts.app')
@section('title', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')

@section('content')
<x-page-header :title="$usuario->exists ? 'Editar ' . $usuario->nombre_usuario : 'Nuevo usuario'" :back="route('admin.usuarios-app.index')" back-label="Usuarios" />
<div class="row"><div class="col-lg-8 col-xl-6">
<form method="POST" action="{{ $usuario->exists ? route('admin.usuarios-app.update', $usuario) : route('admin.usuarios-app.store') }}" class="card" autocomplete="off">
    @csrf @if($usuario->exists) @method('PUT') @endif
    <div class="card-body">
        <div class="form-section">
            <div class="form-section-title">Identidad</div>
            <div class="row">
                <div class="col-sm-6"><x-form.input name="nombre_usuario" label="Usuario" :value="$usuario->nombre_usuario" required maxlength="50" hint="Letras, numeros, guion y guion bajo." /></div>
                <div class="col-sm-6"><x-form.input name="nombre_completo" label="Nombre completo" :value="$usuario->nombre_completo" maxlength="150" /></div>
            </div>
            <x-form.input name="email" type="email" label="Correo (opcional)" :value="$usuario->email" maxlength="150" />
        </div>
        <div class="form-section">
            <div class="form-section-title">Contrasena</div>
            <div class="row">
                <div class="col-sm-6"><x-form.input name="password" type="password" label="Contrasena" :required="! $usuario->exists" autocomplete="new-password" hint="Minimo 8 caracteres con letras y numeros." /></div>
                <div class="col-sm-6"><x-form.input name="password_confirmation" type="password" label="Confirmar" :required="! $usuario->exists" autocomplete="new-password" /></div>
            </div>
            @if($usuario->exists)<p class="form-text mb-0">Deja vacio para conservar la actual. Al cambiarla se cierran sus sesiones en la app.</p>@endif
        </div>
        <div class="form-section">
            <div class="form-section-title">Permisos</div>
            <div class="row">
                <div class="col-sm-6"><x-form.select name="rol" label="Rol" :options="$roles" :selected="$usuario->rol" :placeholder="false" required /></div>
                <div class="col-sm-6"><x-form.select name="departamento_id" label="Departamento" :options="$departamentos" :selected="$usuario->departamento?->hashid" placeholder="Ninguno" hint="Obligatorio para usuarios de departamento." /></div>
            </div>
            <x-form.switch name="puede_generar_documentos" label="Puede generar documentos desde la app" :checked="$usuario->puede_generar_documentos" />
            <x-form.switch name="activo" label="Usuario activo" :checked="$usuario->activo" wrapper="mb-0" />
        </div>
    </div>
    <div class="card-body border-top"><x-form.actions :cancel="route('admin.usuarios-app.index')" /></div>
</form>
</div></div>
@endsection
