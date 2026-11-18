@extends('layouts.app')
@section('title', 'Inventario de sistemas')

@section('content')
<x-page-header title="Inventario de sistemas" subtitle="Entregas de equipo y consumibles por departamento">
    <x-slot:actions><a href="{{ route('admin.inventario.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Registrar entrega</a></x-slot:actions>
</x-page-header>
<div class="card">
    <div class="card-body border-bottom">
        <form class="filters" method="GET">
            <div class="grow"><label class="form-label">Buscar</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Articulo, serie, persona o clave"></div>
            <x-form.select name="categoria" label="Categoria" :options="$categorias" :selected="request('categoria')" placeholder="Todas" wrapper="" />
            <div><label class="form-label">Desde</label><input type="date" class="form-control" name="desde" value="{{ request('desde') }}"></div>
            <div><label class="form-label">Hasta</label><input type="date" class="form-control" name="hasta" value="{{ request('hasta') }}"></div>
            <div><button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>Fecha</th><th>Articulo</th><th>Categoria</th><th>Cant.</th><th>Recibe</th><th>Departamento</th><th></th></tr></thead>
        <tbody>
        @forelse ($inventarios as $i)
            <tr>
                <td class="tabular">{{ $i->fecha_entrega?->format('d/m/Y') }}</td>
                <td><a href="{{ route('admin.inventario.show', $i) }}" class="fw-semibold">{{ $i->nombre }}</a><span class="sub">{{ trim($i->marca . ' ' . $i->modelo) }} @if($i->numero_serie) - S/N {{ $i->numero_serie }} @endif</span></td>
                <td><x-badge :tone="$i->categoria?->esEquipo() ? 'primary' : 'purple'">{{ $i->categoria?->etiqueta() }}</x-badge></td>
                <td class="tabular">{{ $i->cantidad }}</td>
                <td>{{ $i->nombre_recibe }}<span class="sub">{{ $i->clave_empleado }}</span></td>
                <td>{{ ucfirst($i->departamento?->nombre ?? '-') }}<span class="sub">{{ $i->area }}</span></td>
                <td class="table-actions">
                    <x-icon-link :href="route('admin.inventario.edit', $i)" icon="bi-pencil" title="Editar" />
                    <x-delete-button :action="route('admin.inventario.destroy', $i)" />
                </td>
            </tr>
        @empty
            <tr><td colspan="7"><x-empty icon="bi-box-seam" text="Sin registros de inventario." /></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <x-pagination :paginator="$inventarios" />
</div>
@endsection
