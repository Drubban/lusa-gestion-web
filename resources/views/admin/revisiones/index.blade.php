@extends('layouts.app')
@section('title', 'Revisiones optocontrol')

@section('content')
<x-page-header title="Revisiones optocontrol" subtitle="Reportes de falla y atencion de dispositivos">
    <x-slot:actions>
        <a href="{{ route('admin.revisiones-optocontrol.dashboard') }}" class="btn btn-light"><i class="bi bi-bar-chart me-1"></i>Indicadores</a>
        <a href="{{ route('admin.revisiones-optocontrol.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nueva revision</a>
    </x-slot:actions>
</x-page-header>
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><x-stat label="Pendientes" :value="$stats->pendientes" icon="bi-hourglass-split" tone="warning" :href="route('admin.revisiones-optocontrol.index', ['status' => 'pendiente'])" /></div>
    <div class="col-6 col-lg-3"><x-stat label="En proceso" :value="$stats->en_proceso" icon="bi-gear" tone="info" :href="route('admin.revisiones-optocontrol.index', ['status' => 'en_proceso'])" /></div>
    <div class="col-6 col-lg-3"><x-stat label="Resueltas" :value="$stats->resueltos" icon="bi-check2-circle" tone="success" /></div>
    <div class="col-6 col-lg-3"><x-stat label="Ultimos 30 dias" :value="$stats->ultimos30" icon="bi-calendar3" tone="purple" /></div>
</div>
<div class="card">
    <div class="card-body border-bottom">
        <form class="filters" method="GET">
            <div class="grow"><label class="form-label">Buscar</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Problema, solucion, ruta, responsable"></div>
            <x-form.select name="disp" label="Dispositivo" :options="$dispositivos" :selected="request('disp')" placeholder="Todos" wrapper="" />
            <x-form.select name="status" label="Estado" :options="$estados" :selected="request('status')" placeholder="Todos" wrapper="" />
            <x-form.select name="unidad" label="Unidad" :options="$unidades" :selected="request('unidad')" placeholder="Todas" wrapper="" />
            <div><label class="form-label">Desde</label><input type="date" class="form-control" name="desde" value="{{ request('desde') }}"></div>
            <div><label class="form-label">Hasta</label><input type="date" class="form-control" name="hasta" value="{{ request('hasta') }}"></div>
            <div><button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>Fecha</th><th>Unidad</th><th>Dispositivo</th><th>Problema</th><th>Estancia</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        @forelse ($revisiones as $r)
            <tr>
                <td class="tabular">{{ $r->fecha_reporte?->format('d/m/Y') }}<span class="sub">{{ substr((string) $r->hora_entrada, 0, 5) }}</span></td>
                <td class="fw-semibold">{{ $r->unidad?->numero_economico }}<span class="sub">{{ $r->ruta }}</span></td>
                <td><x-badge tone="primary">{{ $r->disp?->etiqueta() }}</x-badge></td>
                <td style="max-width:320px"><a href="{{ route('admin.revisiones-optocontrol.show', $r) }}">{{ \Illuminate\Support\Str::limit($r->problema, 80) }}</a></td>
                <td class="tabular">{{ $r->tiempo_estancia ? $r->tiempo_estancia . ' h' : '-' }}</td>
                <td><x-badge :tone="$r->status->tono()" dot>{{ $r->status->etiqueta() }}</x-badge></td>
                <td class="table-actions">
                    <x-icon-link :href="route('admin.revisiones-optocontrol.edit', $r)" icon="bi-pencil" title="Editar" />
                    <x-delete-button :action="route('admin.revisiones-optocontrol.destroy', $r)" />
                </td>
            </tr>
        @empty
            <tr><td colspan="7"><x-empty icon="bi-tools" text="Sin revisiones." /></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <x-pagination :paginator="$revisiones" />
</div>
@endsection
