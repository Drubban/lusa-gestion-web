@extends('layouts.app')
@section('title', 'Tecnologias')

@section('content')
<x-page-header title="Tecnologias" subtitle="Equipos instalados en las unidades">
    <x-slot:actions><a href="{{ route('admin.tecnologias.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Registrar tecnologia</a></x-slot:actions>
</x-page-header>
<div class="row g-3 mb-3">
    @foreach ($tipos as $valor => $texto)
        <div class="col-6 col-lg-3"><x-stat :label="$texto" :value="$conteo[$valor] ?? 0" :icon="match($valor) { 'barras' => 'bi-upc-scan', 'telpo' => 'bi-credit-card-2-front', 'gps' => 'bi-geo-alt', default => 'bi-camera-video' }" :tone="match($valor) { 'barras' => 'primary', 'telpo' => 'accent', 'gps' => 'success', default => 'purple' }" :href="route('admin.tecnologias.index', ['tipo' => $valor])" /></div>
    @endforeach
</div>
<div class="card">
    <div class="card-body border-bottom">
        <form class="filters" method="GET">
            <div class="grow"><label class="form-label">Buscar unidad</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Economico o nombre"></div>
            <x-form.select name="tipo" label="Tipo" :options="$tipos" :selected="request('tipo')" placeholder="Todos" wrapper="" />
            <div><button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>Unidad</th><th>Tipo</th><th>Identificador</th><th>Detalle</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        @forelse ($tecnologias as $t)
            @php $datos = array_filter($t->datos, fn ($v) => $v !== null && $v !== ''); @endphp
            <tr>
                <td class="fw-semibold">{{ $t->unidad?->numero_economico }}</td>
                <td><x-badge tone="primary">{{ $t->tipo->etiqueta() }}</x-badge></td>
                <td><a href="{{ route('admin.tecnologias.show', $t) }}">{{ $t->nombre ?: (reset($datos) ?: 'Ver') }}</a></td>
                <td class="small text-muted">{{ collect($datos)->take(3)->map(fn ($v, $k) => "$k: $v")->implode(' / ') }}</td>
                <td><x-badge :tone="$t->activo ? 'success' : 'muted'" dot>{{ $t->activo ? 'Activa' : 'Inactiva' }}</x-badge></td>
                <td class="table-actions">
                    <x-icon-link :href="route('admin.tecnologias.edit', $t)" icon="bi-pencil" title="Editar" />
                    <x-delete-button :action="route('admin.tecnologias.destroy', $t)" />
                </td>
            </tr>
        @empty
            <tr><td colspan="6"><x-empty icon="bi-cpu" text="Sin tecnologias registradas." /></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <x-pagination :paginator="$tecnologias" />
</div>
@endsection
