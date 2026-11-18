@extends('layouts.app')
@section('title', $tecnologia->tipo->etiqueta())

@section('content')
<x-page-header :title="$tecnologia->tipo->etiqueta() . ' - ' . $tecnologia->unidad?->numero_economico" :subtitle="$tecnologia->nombre" :back="route('admin.tecnologias.index')" back-label="Tecnologias">
    <x-slot:actions><a href="{{ route('admin.tecnologias.edit', $tecnologia) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a></x-slot:actions>
</x-page-header>
<div class="row"><div class="col-lg-7"><div class="card"><div class="card-body">
    <x-dl :items="array_merge(['Unidad' => $tecnologia->unidad?->etiqueta, 'Zona' => ucfirst($tecnologia->unidad?->zona?->nombre ?? '')], $tecnologia->datos, ['Registrada por' => $tecnologia->creador?->nombre_visible, 'Alta' => $tecnologia->created_at?->format('d/m/Y')])">
        <dt>Estado</dt><dd><x-badge :tone="$tecnologia->activo ? 'success' : 'muted'" dot>{{ $tecnologia->activo ? 'Activa' : 'Inactiva' }}</x-badge></dd>
    </x-dl>
</div></div></div></div>
@endsection
