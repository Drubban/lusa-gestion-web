@extends('layouts.app')
@section('title', 'Ajuste ' . $ajuste->folio)

@section('content')
<x-page-header :title="'Ajuste ' . $ajuste->folio" :back="route('admin.ajustes.index')" back-label="Ajustes">
    <x-slot:actions><a href="{{ route('admin.ajustes.edit', $ajuste) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a></x-slot:actions>
</x-page-header>
<div class="row"><div class="col-lg-7"><div class="card"><div class="card-body">
    <x-dl :items="[
        'Monto' => '$' . number_format($ajuste->monto_total, 2),
        'Fecha y hora' => $ajuste->fecha?->format('d/m/Y') . ' ' . substr((string) $ajuste->hora, 0, 5),
        'Operador' => $ajuste->operador ? $ajuste->operador->nombre_completo . ' (' . $ajuste->operador->clave_operador . ')' : null,
        'Unidad' => $ajuste->unidad?->etiqueta,
        'Zona' => ucfirst($ajuste->zona?->nombre ?? ''),
        'Registrado por' => $ajuste->creador?->nombre_visible,
    ]">
        <dt>Firma</dt><dd><x-badge :tone="$ajuste->firmado ? 'success' : 'warning'" dot>{{ $ajuste->firmado ? 'Firmado' : 'Pendiente' }}</x-badge></dd>
    </x-dl>
</div></div></div></div>
@endsection
