@extends('layouts.app')
@section('title', 'Agendamientos')

@section('content')
<x-page-header title="Agendamientos de mantenimiento" subtitle="Una unidad solo puede tener un agendamiento pendiente">
    <x-slot:actions>
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-download me-1"></i>Exportar</button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('admin.exportar.agendamientos', ['alcance' => 'pendientes']) }}">Pendientes (CSV)</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.exportar.agendamientos', ['alcance' => 'todos']) }}">Todos (CSV)</a></li>
            </ul>
        </div>
        <a href="{{ route('admin.agendamientos.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Agendar</a>
    </x-slot:actions>
</x-page-header>
<div class="card">
    <div class="card-body border-bottom">
        <form class="filters" method="GET">
            <div class="grow"><label class="form-label">Unidad</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Economico o nombre"></div>
            <x-form.select name="estado" label="Estado" :options="$estados" :selected="request('estado')" placeholder="Todos" wrapper="" />
            <x-form.select name="motivo" label="Motivo" :options="$motivos" :selected="request('motivo')" placeholder="Todos" wrapper="" />
            <div><label class="form-label">Desde</label><input type="date" class="form-control" name="desde" value="{{ request('desde') }}"></div>
            <div><label class="form-label">Hasta</label><input type="date" class="form-control" name="hasta" value="{{ request('hasta') }}"></div>
            <div><button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>Unidad</th><th>Fecha</th><th>Estado</th><th>Motivo</th><th>Observaciones</th><th></th></tr></thead>
        <tbody>
        @forelse ($agendamientos as $a)
            <tr>
                <td class="fw-semibold">{{ $a->unidad?->numero_economico }}<span class="sub">{{ ucfirst($a->unidad?->zona?->nombre ?? '') }}</span></td>
                <td class="tabular">{{ $a->fecha_agendada?->format('d/m/Y') }}
                    @if($a->estado === \App\Enums\EstadoAgendamiento::Pendiente)
                        <span class="sub">{{ $a->dias_restantes === 0 ? 'Hoy' : ($a->dias_restantes < 0 ? 'Vencido hace ' . abs($a->dias_restantes) . ' d' : 'En ' . $a->dias_restantes . ' d') }}</span>
                    @endif
                </td>
                <td><x-badge :tone="$a->estado->tono()" dot>{{ $a->estado->etiqueta() }}</x-badge></td>
                <td>{{ $a->motivo_etiqueta ?? '-' }}</td>
                <td class="small text-muted" style="max-width:280px">{{ \Illuminate\Support\Str::limit($a->observaciones, 90) }}</td>
                <td class="table-actions">
                    @include('admin.agendamientos._acciones', ['a' => $a])
                    @if($a->estado === \App\Enums\EstadoAgendamiento::Pendiente)<x-icon-link :href="route('admin.agendamientos.edit', $a)" icon="bi-pencil" title="Editar" />@endif
                    <x-delete-button :action="route('admin.agendamientos.destroy', $a)" />
                </td>
            </tr>
        @empty
            <tr><td colspan="6"><x-empty icon="bi-calendar2" text="Sin agendamientos." /></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <x-pagination :paginator="$agendamientos" />
</div>
@include('admin.agendamientos._modales')
@endsection
