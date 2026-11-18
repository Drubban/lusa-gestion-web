@extends('layouts.app')
@section('title', $documento->folio)

@section('content')
<x-page-header :title="$documento->folio" subtitle="Conformidad de capacitacion" :back="route('admin.documentos-capacitacion.index')" back-label="Capacitacion">
    <x-slot:actions>
        <a href="{{ route('admin.documentos-capacitacion.pdf', $documento) }}" class="btn btn-light"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        <a href="{{ route('admin.documentos-capacitacion.edit', $documento) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
    </x-slot:actions>
</x-page-header>
<div class="row g-3">
    <div class="col-lg-7"><div class="card"><div class="card-body">
        <x-dl :items="[
            'Operador' => $documento->operador ? $documento->operador->nombre_completo . ' (' . $documento->operador->clave_operador . ')' : null,
            'Unidad' => $documento->unidad?->etiqueta,
            'Zona' => ucfirst($documento->unidad?->zona?->nombre ?? ''),
            'Tipo' => $documento->tipo_capacitacion,
            'Fecha y hora' => $documento->fecha?->format('d/m/Y') . ' ' . substr((string) $documento->hora, 0, 5),
            'Vence' => $documento->fecha_vencimiento?->format('d/m/Y'),
            'Instructor' => $documento->instructor,
            'Duracion' => $documento->duracion_horas ? $documento->duracion_horas . ' h' : null,
            'Observaciones' => $documento->observaciones,
        ]" />
    </div></div></div>
    <div class="col-lg-5"><div class="card"><div class="card-body">
        <div class="card-title-sm">Firmas</div>
        @foreach (\App\Models\DocumentoCapacitacion::FIRMAS as $tipo => $etiqueta)
            <div class="mb-3 pb-3 border-bottom">
                <div class="small text-muted mb-1">{{ $etiqueta }}</div>
                @if ($f = $documento->firma($tipo))<img src="{{ $f->data_uri }}" class="firma-img" alt="{{ $etiqueta }}">@else<span class="small text-muted">Sin firma</span>@endif
            </div>
        @endforeach
    </div></div></div>
</div>
@endsection
