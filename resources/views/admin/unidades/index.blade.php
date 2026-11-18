@extends('layouts.app')
@section('title', 'Unidades')

@section('content')
<x-page-header title="Unidades" subtitle="Flota registrada y operador asignado">
    <x-slot:actions>
        <a href="{{ route('admin.qr.index') }}" class="btn btn-light"><i class="bi bi-qr-code me-1"></i>Codigos QR</a>
        <a href="{{ route('admin.unidades.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nueva unidad</a>
    </x-slot:actions>
</x-page-header>

<div class="card">
    <div class="card-body border-bottom">
        <form class="filters" method="GET">
            <div class="grow"><label class="form-label">Buscar</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Economico, nombre u operador"></div>
            <x-form.select name="zona" label="Zona" :options="$zonas" :selected="request('zona')" placeholder="Todas" wrapper="" />
            <x-form.select name="estado" label="Estado" :options="['activo' => 'Activas', 'inactivo' => 'Inactivas']" :selected="request('estado')" placeholder="Todas" wrapper="" />
            <div><button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr>
                <th><x-sort-link column="numero_economico" label="Economico" /></th>
                <th><x-sort-link column="nombre_unidad" label="Nombre" /></th>
                <th>Zona</th><th>Operador</th><th>Estado</th><th></th>
            </tr></thead>
            <tbody>
            @forelse ($unidades as $u)
                <tr>
                    <td class="fw-semibold"><a href="{{ route('admin.unidades.show', $u) }}">{{ $u->numero_economico }}</a></td>
                    <td>{{ $u->nombre_unidad ?: '-' }}</td>
                    <td>{{ ucfirst($u->zona?->nombre ?? '-') }}</td>
                    <td>@if($op = $u->asignacionVigente?->operador){{ $op->nombre_completo }}<span class="sub">{{ $op->clave_operador }}</span>@else<span class="text-muted">Sin asignar</span>@endif</td>
                    <td><x-badge :tone="$u->activo ? 'success' : 'muted'" dot>{{ $u->activo ? 'Activa' : 'Inactiva' }}</x-badge></td>
                    <td class="table-actions">
                        <x-icon-link :href="route('admin.unidades.edit', $u)" icon="bi-pencil" title="Editar" />
                        <x-delete-button :action="route('admin.unidades.destroy', $u)" confirm="Si la unidad tiene historial se desactivara en lugar de eliminarse. Continuar?" />
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><x-empty icon="bi-bus-front" text="No hay unidades con esos filtros." /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <x-pagination :paginator="$unidades" />
</div>
@endsection
