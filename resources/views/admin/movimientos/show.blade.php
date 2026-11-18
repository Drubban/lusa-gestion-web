@extends('layouts.app')
@section('title', 'Movimiento')

@section('content')
<x-page-header :title="$movimiento->tipo->etiqueta() . ' de ' . $movimiento->unidad?->numero_economico" :subtitle="$movimiento->fecha_hora->format('d/m/Y H:i')" :back="route('admin.movimientos.index')" back-label="Entradas y salidas">
    <x-slot:actions>
        <a href="{{ route('admin.movimientos.edit', $movimiento) }}" class="btn btn-light"><i class="bi bi-pencil me-1"></i>Corregir</a>
        <x-delete-button :action="route('admin.movimientos.destroy', $movimiento)" label="Eliminar" />
    </x-slot:actions>
</x-page-header>
<div class="row g-3">
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <x-dl :items="[
            'Unidad' => $movimiento->unidad?->etiqueta,
            'Departamento' => ucfirst($movimiento->departamento?->nombre),
            'Operador en ese momento' => $operador ? $operador->nombre_completo . ' (' . $operador->clave_operador . ')' : null,
            'Registrado por' => $movimiento->usuario?->nombre_visible,
            'Observaciones' => $movimiento->observaciones,
        ]" />
    </div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <div class="card-title-sm">Estancia</div>
        <ul class="timeline">
            @php $entrada = $movimiento->tipo->value === 'entrada' ? $movimiento : $movimiento->entrada; $salida = $movimiento->tipo->value === 'salida' ? $movimiento : $movimiento->salida; @endphp
            <li><span class="fw-semibold">Entrada</span><span class="d-block small text-muted">{{ $entrada?->fecha_hora?->format('d/m/Y H:i') ?? 'Sin registro' }}</span></li>
            <li class="salida"><span class="fw-semibold">Salida</span><span class="d-block small text-muted">{{ $salida?->fecha_hora?->format('d/m/Y H:i') ?? 'Sigue dentro' }}</span></li>
        </ul>
        @if ($salida?->duracion_legible)<p class="mb-0">Duracion: <strong>{{ $salida->duracion_legible }}</strong></p>@endif
    </div></div></div>
</div>
@endsection
