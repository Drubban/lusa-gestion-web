@extends('layouts.app')
@section('title', 'Estado de mantenimiento')

@section('content')
<x-page-header title="Estado de mantenimiento" subtitle="Dias desde el ultimo servicio de cada unidad activa" />

<div class="row g-3 mb-3">
    @foreach ($semaforos as $s)
        <div class="col-6 col-md-4 col-xl"><x-stat :label="$s->etiqueta()" :value="$resumen['por_semaforo'][$s->value]" icon="bi-circle-fill" :tone="$s->tono()" :href="request()->fullUrlWithQuery(['semaforo' => $s->value])" /></div>
    @endforeach
</div>

@if ($porAtender->isNotEmpty())
<div class="card mb-3">
    <div class="card-header">Agendados para hoy o vencidos <x-badge tone="accent">{{ $porAtender->count() }}</x-badge></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Unidad</th><th>Zona</th><th>Fecha</th><th></th></tr></thead>
        <tbody>
        @foreach ($porAtender as $a)
            <tr>
                <td class="fw-semibold">{{ $a->unidad?->numero_economico }}</td>
                <td>{{ ucfirst($a->unidad?->zona?->nombre ?? '-') }}</td>
                <td><x-badge :tone="$a->fecha_agendada->isToday() ? 'accent' : 'danger'">{{ $a->fecha_agendada->isToday() ? 'Hoy' : $a->fecha_agendada->format('d/m/Y') }}</x-badge></td>
                <td class="table-actions">@include('admin.agendamientos._acciones', ['a' => $a])</td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endif

<form method="POST" action="{{ route('admin.mantenimiento.agendar-masivo') }}" class="card">
    @csrf
    <div class="card-body border-bottom">
        <div class="filters">
            <div class="grow"><label class="form-label">Buscar</label><input class="form-control" name="search" form="filtros" value="{{ request('search') }}" placeholder="Unidad u operador"></div>
            <x-form.select name="zona" label="Zona" :options="$zonas" :selected="request('zona')" placeholder="Todas" wrapper="" form="filtros" />
            <x-form.select name="semaforo" label="Estado" :options="collect($semaforos)->mapWithKeys(fn ($s) => [$s->value => $s->etiqueta()])->all()" :selected="request('semaforo')" placeholder="Todos" wrapper="" form="filtros" />
            <x-form.select name="agendamiento" label="Agendamiento" :options="['con' => 'Con agendamiento', 'sin' => 'Sin agendamiento']" :selected="request('agendamiento')" placeholder="Todos" wrapper="" form="filtros" />
            <div><button class="btn btn-light" form="filtros"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </div>
    </div>
    <div class="card-body border-bottom d-flex flex-wrap gap-2 align-items-end" style="background:#fafbfd">
        <div class="me-auto small text-muted pb-2"><span data-check-count>0</span> unidades seleccionadas</div>
        <div><label class="form-label">Agendar para</label><input type="date" class="form-control" name="fecha_agendada" min="{{ today()->format('Y-m-d') }}" value="{{ today()->addDay()->format('Y-m-d') }}" required></div>
        <div style="min-width:240px"><label class="form-label">Observaciones</label><input class="form-control" name="observaciones" maxlength="1000"></div>
        <div><button class="btn btn-accent" data-requiere-seleccion disabled><i class="bi bi-calendar-plus me-1"></i>Agendar seleccionadas</button></div>
    </div>
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr>
            <th style="width:36px"><input type="checkbox" class="form-check-input" data-check-all="input[name='unidades[]']" aria-label="Seleccionar todas"></th>
            <th>Unidad</th><th>Operador</th><th>Ultimo servicio</th><th>Estado</th><th>Proximo sugerido</th><th>Agendado</th><th>Tecnologias</th><th></th>
        </tr></thead>
        <tbody>
        @forelse ($tablero as $f)
            <tr>
                <td><input type="checkbox" class="form-check-input" name="unidades[]" value="{{ $f['unidad']->hashid }}" aria-label="Seleccionar {{ $f['unidad']->numero_economico }}"></td>
                <td class="fw-semibold">{{ $f['unidad']->numero_economico }}<span class="sub">{{ ucfirst($f['unidad']->zona?->nombre ?? '') }}</span></td>
                <td>{{ $f['unidad']->asignacionVigente?->operador?->nombre_completo ?? '-' }}</td>
                <td class="tabular">{{ $f['ultimo'] ? \Illuminate\Support\Carbon::parse($f['ultimo'])->format('d/m/Y') : '-' }}@if($f['dias'] !== null)<span class="sub">hace {{ $f['dias'] }} dias</span>@endif</td>
                <td><x-badge :tone="$f['semaforo']->tono()" dot>{{ $f['semaforo']->etiqueta() }}</x-badge></td>
                <td class="tabular">{{ $f['proximo'] ?? '-' }}</td>
                <td>@if($f['agendamiento'])<x-badge :tone="$f['agendamiento']->fecha_agendada->isBefore(today()) ? 'danger' : 'primary'">{{ $f['agendamiento']->fecha_agendada->format('d/m/Y') }}</x-badge>@else<span class="text-muted">-</span>@endif</td>
                <td>@foreach($f['unidad']->tecnologias as $t)<x-badge tone="muted" class="me-1">{{ $t->tipo->etiqueta() }}</x-badge>@endforeach</td>
                <td class="table-actions"><x-icon-link :href="route('admin.mantenimiento.detalle', $f['unidad'])" icon="bi-clock-history" title="Historial" /></td>
            </tr>
        @empty
            <tr><td colspan="9"><x-empty text="No hay unidades con esos filtros." /></td></tr>
        @endforelse
        </tbody>
    </table></div>
</form>
<form id="filtros" method="GET"></form>
@include('admin.agendamientos._modales')
@endsection
