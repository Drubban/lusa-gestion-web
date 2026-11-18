@extends('layouts.app')
@section('title', 'Revision')

@section('content')
<x-page-header :title="$revision->disp?->etiqueta() . ' - ' . $revision->unidad?->numero_economico" :subtitle="$revision->fecha_reporte?->format('d/m/Y')" :back="route('admin.revisiones-optocontrol.index')" back-label="Revisiones">
    <x-slot:actions><a href="{{ route('admin.revisiones-optocontrol.edit', $revision) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a></x-slot:actions>
</x-page-header>
<div class="row g-3">
    <div class="col-lg-5"><div class="card"><div class="card-body">
        <x-dl :items="[
            'Unidad' => $revision->unidad?->etiqueta,
            'Ruta' => $revision->ruta,
            'Entrada' => substr((string) $revision->hora_entrada, 0, 5),
            'Salida' => substr((string) $revision->hora_salida, 0, 5),
            'Estancia' => $revision->tiempo_estancia ? $revision->tiempo_estancia . ' h' : null,
            'Responsable' => $revision->responsable,
            'Capturado por' => $revision->creador?->nombre_visible,
        ]">
            <dt>Estado</dt><dd><x-badge :tone="$revision->status->tono()" dot>{{ $revision->status->etiqueta() }}</x-badge></dd>
        </x-dl>
    </div></div></div>
    <div class="col-lg-7"><div class="card"><div class="card-body">
        <div class="card-title-sm">Problema</div><p style="white-space:pre-line">{{ $revision->problema }}</p>
        <div class="card-title-sm mt-4">Solucion</div><p class="mb-0" style="white-space:pre-line">{{ $revision->solucion ?: 'Sin registrar' }}</p>
    </div></div></div>
</div>
@endsection
