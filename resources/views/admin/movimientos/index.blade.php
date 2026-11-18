@extends('layouts.app')
@section('title', 'Entradas y salidas')

@section('content')
<x-page-header title="Entradas y salidas" subtitle="Estancias de unidades por departamento">
    <x-slot:actions><a href="{{ route('admin.movimientos.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Registrar movimiento</a></x-slot:actions>
</x-page-header>

<div class="card mb-3">
    <div class="card-header">Dentro ahora <x-badge tone="primary">{{ $dentro->count() }}</x-badge></div>
    @if ($dentro->isEmpty())
        <x-empty icon="bi-check2-circle" text="No hay unidades dentro de ningun departamento." />
    @else
        <div class="table-responsive"><table class="table">
            <thead><tr><th>Unidad</th><th>Departamento</th><th>Entro</th><th>Tiempo dentro</th></tr></thead>
            <tbody>
            @foreach ($dentro as $d)
                @php $min = (int) $d->fecha_hora->diffInMinutes(now()); @endphp
                <tr>
                    <td class="fw-semibold">{{ $d->unidad?->numero_economico }}</td>
                    <td>{{ ucfirst($d->departamento?->nombre) }}</td>
                    <td class="tabular">{{ $d->fecha_hora->format('d/m H:i') }}</td>
                    <td><x-badge :tone="$min > 480 ? 'danger' : ($min > 120 ? 'accent' : 'success')">{{ $min >= 60 ? intdiv($min, 60) . ' h ' . ($min % 60) . ' min' : $min . ' min' }}</x-badge></td>
                </tr>
            @endforeach
            </tbody>
        </table></div>
    @endif
</div>

<div class="card">
    <div class="card-body border-bottom">
        <form class="filters" method="GET">
            <div class="grow"><label class="form-label">Unidad</label><input class="form-control" name="unidad" value="{{ request('unidad') }}" placeholder="Numero economico"></div>
            <x-form.select name="departamento" label="Departamento" :options="$departamentos" :selected="request('departamento')" placeholder="Todos" wrapper="" />
            <x-form.select name="tipo" label="Tipo" :options="['entrada' => 'Entrada', 'salida' => 'Salida']" :selected="request('tipo')" placeholder="Ambos" wrapper="" />
            <div><label class="form-label">Fecha</label><input type="date" class="form-control" name="fecha" value="{{ request('fecha') }}"></div>
            <div><button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>Fecha y hora</th><th>Unidad</th><th>Tipo</th><th>Departamento</th><th>Estancia</th><th>Registro</th><th></th></tr></thead>
        <tbody>
        @forelse ($movimientos as $m)
            <tr>
                <td class="tabular">{{ $m->fecha_hora->format('d/m/Y H:i') }}</td>
                <td class="fw-semibold">{{ $m->unidad?->numero_economico }}</td>
                <td><x-badge :tone="$m->tipo->value === 'entrada' ? 'primary' : 'accent'"><i class="bi {{ $m->tipo->value === 'entrada' ? 'bi-box-arrow-in-right' : 'bi-box-arrow-right' }}"></i>{{ $m->tipo->etiqueta() }}</x-badge></td>
                <td>{{ ucfirst($m->departamento?->nombre) }}</td>
                <td>{{ $m->duracion_legible ?? '-' }}</td>
                <td>{{ $m->usuario?->nombre_visible }}</td>
                <td class="table-actions">
                    <x-icon-link :href="route('admin.movimientos.show', $m)" icon="bi-eye" title="Ver" />
                    <x-icon-link :href="route('admin.movimientos.edit', $m)" icon="bi-pencil" title="Corregir" />
                </td>
            </tr>
        @empty
            <tr><td colspan="7"><x-empty icon="bi-arrow-left-right" text="Sin movimientos con esos filtros." /></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <x-pagination :paginator="$movimientos" />
</div>
@endsection
