@extends('layouts.app')
@section('title', 'Inicio')

@section('content')
<x-page-header title="Hola, {{ auth()->user()->nombre_visible }}" subtitle="Resumen operativo de hoy">
    <x-slot:actions>
        <a href="{{ route('admin.movimientos.create') }}" class="btn btn-light"><i class="bi bi-arrow-left-right me-1"></i>Registrar movimiento</a>
        <a href="{{ route('admin.documentos-mantenimiento.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo mantenimiento</a>
    </x-slot:actions>
</x-page-header>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><x-stat label="Unidades dentro de departamentos" :value="$kpis['dentro']" icon="bi-box-arrow-in-right" tone="primary" :href="route('admin.movimientos.index')" /></div>
    <div class="col-6 col-lg-3"><x-stat label="Mantenimientos agendados hoy" :value="$kpis['agendados_hoy']" icon="bi-calendar-event" tone="accent" :href="route('admin.mantenimiento.index')" /></div>
    <div class="col-6 col-lg-3"><x-stat label="Agendamientos vencidos" :value="$kpis['vencidos']" icon="bi-exclamation-triangle" :tone="$kpis['vencidos'] ? 'danger' : 'success'" :href="route('admin.agendamientos.index', ['estado' => 'pendiente'])" /></div>
    <div class="col-6 col-lg-3"><x-stat label="Revisiones abiertas" :value="$kpis['revisiones_abiertas']" icon="bi-tools" tone="purple" :href="route('admin.revisiones-optocontrol.index')" /></div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">Movimientos de los ultimos 7 dias</div>
            <div class="card-body"><div class="chart-box">
                <canvas data-chart="{{ json_encode(['type' => 'line', 'labels' => $serieMovimientos->keys(), 'data' => $serieMovimientos->values()]) }}"></canvas>
            </div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">Semaforo de mantenimiento</div>
            <div class="card-body">
                <div class="chart-box" style="height:200px">
                    <canvas data-chart="{{ json_encode(['type' => 'doughnut', 'labels' => collect(\App\Enums\SemaforoMantenimiento::cases())->map->etiqueta(), 'data' => array_values($resumenMto['por_semaforo']), 'colors' => ['#2e9e6a', '#e8a814', '#d9822b', '#c94a4a', '#c3cad6']]) }}"></canvas>
                </div>
                <div class="small text-muted text-center mt-2">{{ $resumenMto['total'] }} unidades activas</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><x-stat label="Unidades activas" :value="$kpis['unidades']" icon="bi-bus-front" tone="muted" /></div>
    <div class="col-6 col-lg-3"><x-stat label="Operadores activos" :value="$kpis['operadores']" icon="bi-person-badge" tone="muted" /></div>
    <div class="col-6 col-lg-3"><x-stat label="Mantenimientos del mes" :value="$kpis['mantenimientos_mes']" icon="bi-file-earmark-check" tone="success" /></div>
    <div class="col-6 col-lg-3"><x-stat label="Entregas de inventario del mes" :value="$kpis['inventario_mes']" icon="bi-box-seam" tone="info" /></div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">Actividad reciente <a href="{{ route('admin.movimientos.index') }}" class="small fw-normal">Ver todo</a></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <tbody>
                    @forelse ($ultimosMovimientos as $m)
                        <tr>
                            <td style="width:40px"><x-badge :tone="$m->tipo->value === 'entrada' ? 'primary' : 'accent'"><i class="bi {{ $m->tipo->value === 'entrada' ? 'bi-box-arrow-in-right' : 'bi-box-arrow-right' }}"></i></x-badge></td>
                            <td><span class="fw-semibold">{{ $m->unidad?->numero_economico }}</span><span class="sub">{{ $m->tipo->etiqueta() }} en {{ ucfirst($m->departamento?->nombre) }}</span></td>
                            <td class="text-end"><span class="tabular">{{ $m->fecha_hora->format('d/m H:i') }}</span><span class="sub">{{ $m->usuario?->nombre_visible }}</span></td>
                        </tr>
                    @empty
                        <tr><td><x-empty text="Aun no hay movimientos." /></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">Proximos mantenimientos <a href="{{ route('admin.agendamientos.index') }}" class="small fw-normal">Agenda</a></div>
            <ul class="list-group list-group-flush">
                @forelse ($proximos as $a)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><span class="fw-semibold">{{ $a->unidad?->numero_economico }}</span> <span class="small text-muted">{{ $a->unidad?->nombre_unidad }}</span></span>
                        <x-badge :tone="$a->fecha_agendada->isPast() && ! $a->fecha_agendada->isToday() ? 'danger' : ($a->fecha_agendada->isToday() ? 'accent' : 'muted')">{{ $a->fecha_agendada->format('d/m/Y') }}</x-badge>
                    </li>
                @empty
                    <li class="list-group-item"><x-empty text="Sin agendamientos pendientes." /></li>
                @endforelse
            </ul>
        </div>
        <div class="card">
            <div class="card-header">Estancias por departamento <span class="small fw-normal text-muted">30 dias</span></div>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Departamento</th><th class="text-end">Movimientos</th><th class="text-end">Estancia prom.</th></tr></thead>
                    <tbody>
                    @forelse ($porDepartamento as $d)
                        <tr><td>{{ ucfirst($d->nombre) }}</td><td class="text-end tabular">{{ $d->total }}</td><td class="text-end tabular">{{ $d->promedio ? $d->promedio . ' min' : '-' }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-muted small">Sin datos.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>@endpush
