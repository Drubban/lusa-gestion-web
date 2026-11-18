@extends('layouts.app')
@section('title', 'Unidad ' . $unidad->numero_economico)

@section('content')
<x-page-header :title="'Unidad ' . $unidad->numero_economico" :subtitle="$unidad->nombre_unidad" :back="route('admin.unidades.index')" back-label="Unidades">
    <x-slot:actions>
        <a href="{{ route('admin.documentos-mantenimiento.create', ['unidad' => $unidad->hashid]) }}" class="btn btn-light"><i class="bi bi-file-earmark-plus me-1"></i>Mantenimiento</a>
        <a href="{{ route('admin.unidades.edit', $unidad) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
    </x-slot:actions>
</x-page-header>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3"><div class="card-body">
            <div class="card-title-sm">Datos generales</div>
            <x-dl :items="[
                'Zona' => ucfirst($unidad->zona?->nombre ?? ''),
                'Operador' => $unidad->operadorActual ? $unidad->operadorActual->nombre_completo . ' (' . $unidad->operadorActual->clave_operador . ')' : 'Sin asignar',
                'Proximo mantenimiento' => $unidad->agendamientoPendiente?->fecha_agendada?->format('d/m/Y'),
                'Registrada' => $unidad->created_at?->format('d/m/Y'),
            ]">
                <dt>Estado</dt><dd><x-badge :tone="$unidad->activo ? 'success' : 'muted'" dot>{{ $unidad->activo ? 'Activa' : 'Inactiva' }}</x-badge></dd>
                <dt>Tecnologias</dt><dd>@forelse($unidad->tecnologias as $t)<x-badge tone="primary" class="me-1">{{ $t->tipo->etiqueta() }}</x-badge>@empty - @endforelse</dd>
            </x-dl>
        </div></div>
        <div class="card qr-card"><div class="card-body">
            <div class="card-title-sm text-start">Codigo QR</div>
            <div class="qr-render mx-auto" data-qr="{{ $unidad->token_qr }}" role="img" aria-label="QR de la unidad {{ $unidad->numero_economico }}"></div>
            <form method="POST" action="{{ route('admin.unidades.regenerar-token', $unidad) }}" class="mt-3" data-confirm="El QR impreso actual dejara de funcionar. Continuar?">
                @csrf <button class="btn btn-light btn-sm"><i class="bi bi-arrow-repeat me-1"></i>Regenerar QR</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Ultimos movimientos</div>
            <div class="table-responsive"><table class="table">
                <thead><tr><th>Fecha</th><th>Tipo</th><th>Departamento</th><th>Estancia</th><th>Registro</th></tr></thead>
                <tbody>
                @forelse ($movimientos as $m)
                    <tr>
                        <td class="tabular">{{ $m->fecha_hora->format('d/m/Y H:i') }}</td>
                        <td><x-badge :tone="$m->tipo->value === 'entrada' ? 'primary' : 'accent'">{{ $m->tipo->etiqueta() }}</x-badge></td>
                        <td>{{ ucfirst($m->departamento?->nombre) }}</td>
                        <td>{{ $m->duracion_legible ?? '-' }}</td>
                        <td>{{ $m->usuario?->nombre_visible }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty text="Sin movimientos registrados." /></td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
        <div class="row g-3">
            <div class="col-md-6"><div class="card h-100">
                <div class="card-header">Mantenimientos</div>
                <ul class="list-group list-group-flush">
                    @forelse ($mantenimientos as $d)
                        <li class="list-group-item d-flex justify-content-between"><a href="{{ route('admin.documentos-mantenimiento.show', $d) }}">{{ $d->folio }}</a><span class="text-muted small">{{ $d->fecha?->format('d/m/Y') }}</span></li>
                    @empty
                        <li class="list-group-item text-muted small">Sin documentos.</li>
                    @endforelse
                </ul>
            </div></div>
            <div class="col-md-6"><div class="card h-100">
                <div class="card-header">Historial de operadores</div>
                <div class="card-body"><ul class="timeline">
                    @forelse ($unidad->asignaciones as $a)
                        <li><span class="fw-semibold">{{ $a->operador?->nombre_completo }}</span>
                            <span class="d-block small text-muted">{{ $a->fecha_inicio?->format('d/m/Y') }} - {{ $a->vigente ? 'actual' : $a->fecha_fin?->format('d/m/Y') }}</span></li>
                    @empty
                        <li class="text-muted small">Sin asignaciones.</li>
                    @endforelse
                </ul></div>
            </div></div>
        </div>
    </div>
</div>
@endsection
@push('scripts')<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.js"></script>@endpush
