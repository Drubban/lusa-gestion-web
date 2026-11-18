@extends('layouts.app')
@section('title', 'Usuarios')

@section('content')
<x-page-header title="Usuarios" subtitle="Acceso al panel web y a la app movil">
    <x-slot:actions><a href="{{ route('admin.usuarios-app.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo usuario</a></x-slot:actions>
</x-page-header>
<div class="card">
    <div class="card-body border-bottom">
        <form class="filters" method="GET">
            <div class="grow"><label class="form-label">Buscar</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Usuario o nombre"></div>
            <x-form.select name="rol" label="Rol" :options="$roles" :selected="request('rol')" placeholder="Todos" wrapper="" />
            <div><button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>Usuario</th><th>Rol</th><th>Departamento</th><th>Documentos</th><th>Ultimo acceso</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        @foreach ($usuarios as $u)
            <tr>
                <td><span class="fw-semibold">{{ $u->nombre_usuario }}</span><span class="sub">{{ $u->nombre_completo }}</span></td>
                <td><x-badge :tone="match($u->rol->value) { 'admin' => 'purple', 'supervisor' => 'primary', default => 'muted' }">{{ $u->rol->etiqueta() }}</x-badge></td>
                <td>{{ ucfirst($u->departamento?->nombre ?? '-') }}</td>
                <td>@if($u->puede_generar_documentos)<i class="bi bi-check2 text-success"></i>@else<span class="text-muted">-</span>@endif</td>
                <td class="small text-muted">{{ $u->ultimo_acceso_at?->diffForHumans() ?? 'Nunca' }}</td>
                <td><x-badge :tone="$u->activo ? 'success' : 'muted'" dot>{{ $u->activo ? 'Activo' : 'Inactivo' }}</x-badge></td>
                <td class="table-actions">
                    <x-icon-link :href="route('admin.usuarios-app.edit', $u)" icon="bi-pencil" title="Editar" />
                    @unless($u->is(auth()->user()))
                        <x-delete-button :action="route('admin.usuarios-app.destroy', $u)" icon="bi-person-x" confirm="Se desactivara el usuario y se cerraran sus sesiones en la app. Continuar?" />
                    @endunless
                </td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    <x-pagination :paginator="$usuarios" />
</div>
@endsection
