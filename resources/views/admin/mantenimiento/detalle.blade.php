@extends('layouts.app')
@section('title', 'Historial de mantenimiento')

@section('content')
<x-page-header :title="'Historial de ' . $unidad->numero_economico" :subtitle="$unidad->nombre_unidad" :back="route('admin.mantenimiento.index')" back-label="Estado de mantenimiento">
    <x-slot:actions>
        <a href="{{ route('admin.agendamientos.create', ['unidad' => $unidad->hashid]) }}" class="btn btn-light"><i class="bi bi-calendar-plus me-1"></i>Agendar</a>
        <a href="{{ route('admin.documentos-mantenimiento.create', ['unidad' => $unidad->hashid]) }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Registrar servicio</a>
    </x-slot:actions>
</x-page-header>
<div class="row g-3">
    <div class="col-lg-7"><div class="card">
        <div class="card-header">Servicios realizados <x-badge tone="primary">{{ $mantenimientos->count() }}</x-badge></div>
        <div class="table-responsive"><table class="table">
            <thead><tr><th>Folio</th><th>Fecha</th><th>Operador</th><th>Tecnologia</th></tr></thead>
            <tbody>
            @forelse ($mantenimientos as $d)
                <tr>
                    <td><a href="{{ route('admin.documentos-mantenimiento.show', $d) }}">{{ $d->folio }}</a></td>
                    <td class="tabular">{{ $d->fecha?->format('d/m/Y') }}</td>
                    <td>{{ $d->operador?->nombre_completo ?? '-' }}</td>
                    <td class="small">{{ implode(', ', $d->tecnologias) }}</td>
                </tr>
            @empty
                <tr><td colspan="4"><x-empty text="Sin servicios registrados." /></td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div></div>
    <div class="col-lg-5"><div class="card">
        <div class="card-header">Agendamientos</div>
        <ul class="list-group list-group-flush">
            @forelse ($unidad->agendamientos as $a)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="tabular">{{ $a->fecha_agendada?->format('d/m/Y') }}<span class="d-block small text-muted">{{ $a->motivo_etiqueta }}</span></span>
                    <x-badge :tone="$a->estado->tono()">{{ $a->estado->etiqueta() }}</x-badge>
                </li>
            @empty
                <li class="list-group-item text-muted small">Sin agendamientos.</li>
            @endforelse
        </ul>
    </div></div>
</div>
@endsection
