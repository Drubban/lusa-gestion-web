@extends('layouts.app')
@section('title', 'Operadores')

@section('content')
<x-page-header title="Operadores" subtitle="Personal de conduccion y su unidad actual">
    <x-slot:actions><a href="{{ route('admin.operadores.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo operador</a></x-slot:actions>
</x-page-header>

<div class="card">
    <div class="card-body border-bottom">
        <form class="filters" method="GET">
            <div class="grow"><label class="form-label">Buscar</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Clave o nombre"></div>
            <x-form.select name="zona" label="Zona" :options="$zonas" :selected="request('zona')" placeholder="Todas" wrapper="" />
            <x-form.select name="estado" label="Estado" :options="['activo' => 'Activos', 'inactivo' => 'Inactivos']" :selected="request('estado')" placeholder="Todos" wrapper="" />
            <div><button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th><x-sort-link column="clave_operador" label="Clave" /></th><th><x-sort-link column="nombre_completo" label="Nombre" /></th><th>Unidad</th><th>Zona</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse ($operadores as $o)
                @php $unidad = $o->asignacionVigente?->unidad; @endphp
                <tr>
                    <td class="fw-semibold"><a href="{{ route('admin.operadores.show', $o) }}">{{ $o->clave_operador }}</a></td>
                    <td>{{ $o->nombre_completo }}</td>
                    <td>@if($unidad)<a href="{{ route('admin.unidades.show', $unidad) }}">{{ $unidad->numero_economico }}</a>@else<span class="text-muted">Sin unidad</span>@endif</td>
                    <td>{{ ucfirst($unidad?->zona?->nombre ?? '-') }}</td>
                    <td><x-badge :tone="$o->activo ? 'success' : 'muted'" dot>{{ $o->activo ? 'Activo' : 'Inactivo' }}</x-badge></td>
                    <td class="table-actions">
                        <x-icon-link :href="route('admin.operadores.edit', $o)" icon="bi-pencil" title="Editar" />
                        <x-delete-button :action="route('admin.operadores.destroy', $o)" confirm="Si el operador tiene historial se desactivara. Continuar?" />
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><x-empty icon="bi-person-badge" text="No hay operadores con esos filtros." /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <x-pagination :paginator="$operadores" />
</div>
@endsection
