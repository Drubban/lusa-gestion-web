@extends('layouts.app')
@section('title', 'Indicadores de revisiones')

@section('content')
<x-page-header title="Indicadores de revisiones" :back="route('admin.revisiones-optocontrol.index')" back-label="Revisiones">
    <x-slot:actions>
        <form class="d-flex gap-2" method="GET">
            <input type="date" class="form-control" name="desde" value="{{ request('desde') }}" aria-label="Desde">
            <input type="date" class="form-control" name="hasta" value="{{ request('hasta') }}" aria-label="Hasta">
            <button class="btn btn-light">Aplicar</button>
        </form>
    </x-slot:actions>
</x-page-header>
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><x-stat label="Total en el periodo" :value="$porEstado->sum()" icon="bi-clipboard-data" tone="primary" /></div>
    <div class="col-6 col-lg-3"><x-stat label="Pendientes" :value="$porEstado['pendiente'] ?? 0" icon="bi-hourglass-split" tone="warning" /></div>
    <div class="col-6 col-lg-3"><x-stat label="Resueltas" :value="$porEstado['resuelto'] ?? 0" icon="bi-check2-circle" tone="success" /></div>
    <div class="col-6 col-lg-3"><x-stat label="Estancia promedio" :value="number_format($promedioHoras, 1) . ' h'" icon="bi-stopwatch" tone="purple" /></div>
</div>
<div class="row g-3 mb-3">
    <div class="col-lg-8"><div class="card h-100"><div class="card-header">Reportes por dia (30 dias)</div><div class="card-body"><div class="chart-box">
        <canvas data-chart="{{ json_encode(['type' => 'bar', 'labels' => $tendencia->keys(), 'data' => $tendencia->values()]) }}"></canvas>
    </div></div></div></div>
    <div class="col-lg-4"><div class="card h-100"><div class="card-header">Por dispositivo</div><div class="card-body"><div class="chart-box">
        <canvas data-chart="{{ json_encode(['type' => 'doughnut', 'labels' => $porDispositivo->keys()->map(fn ($k) => $dispositivos[$k] ?? $k), 'data' => $porDispositivo->values()]) }}"></canvas>
    </div></div></div></div>
</div>
<div class="row g-3">
    <div class="col-lg-6"><div class="card h-100"><div class="card-header">Rutas con mas reportes</div><div class="card-body"><div class="chart-box">
        <canvas data-chart="{{ json_encode(['type' => 'bar', 'horizontal' => true, 'color' => 'gold', 'labels' => $porRuta->keys(), 'data' => $porRuta->values()]) }}"></canvas>
    </div></div></div></div>
    <div class="col-lg-6"><div class="card h-100">
        <div class="card-header">Unidades con mas reportes</div>
        <ul class="list-group list-group-flush">
            @forelse ($topUnidades as $eco => $total)
                <li class="list-group-item d-flex justify-content-between"><span class="fw-semibold">{{ $eco }}</span><x-badge tone="primary">{{ $total }}</x-badge></li>
            @empty
                <li class="list-group-item text-muted small">Sin datos.</li>
            @endforelse
        </ul>
        <div class="card-header border-top">Recientes</div>
        <ul class="list-group list-group-flush">
            @foreach ($recientes as $r)
                <li class="list-group-item d-flex justify-content-between"><a href="{{ route('admin.revisiones-optocontrol.show', $r) }}">{{ $r->unidad?->numero_economico }} - {{ $r->disp?->etiqueta() }}</a><x-badge :tone="$r->status->tono()">{{ $r->status->etiqueta() }}</x-badge></li>
            @endforeach
        </ul>
    </div></div>
</div>
@endsection
@push('scripts')<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>@endpush
