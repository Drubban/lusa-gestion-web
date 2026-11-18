@extends('layouts.app')
@section('title', $documento->folio)

@section('content')
<x-page-header :title="$documento->folio" subtitle="Documento de mantenimiento" :back="route('admin.documentos-mantenimiento.index')" back-label="Documentos">
    <x-slot:actions>
        <a href="{{ route('admin.documentos-mantenimiento.pdf', $documento) }}" class="btn btn-light"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        <a href="{{ route('admin.documentos-mantenimiento.word', $documento) }}" class="btn btn-light"><i class="bi bi-file-earmark-word me-1"></i>Word</a>
        <a href="{{ route('admin.documentos-mantenimiento.edit', $documento) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
    </x-slot:actions>
</x-page-header>
<div class="row g-3">
    <div class="col-lg-7"><div class="card"><div class="card-body">
        <x-dl :items="[
            'Unidad' => $documento->unidad?->etiqueta,
            'Zona' => ucfirst($documento->unidad?->zona?->nombre ?? ''),
            'Operador' => $documento->operador ? $documento->operador->nombre_completo . ' (' . $documento->operador->clave_operador . ')' : null,
            'Fecha y hora' => $documento->fecha?->format('d/m/Y') . ' ' . substr((string) $documento->hora, 0, 5),
            'Estado de camaras' => $documento->estado_camaras,
            'Prueba de barras' => $documento->prueba_barras,
            'Comentarios' => $documento->comentarios,
            'Adeudos' => $documento->veces_adeudo . ($documento->observaciones_adeudo ? ' - ' . $documento->observaciones_adeudo : ''),
            'Capturado por' => $documento->usuario?->nombre_visible,
        ]">
            <dt>Tecnologia</dt><dd>@foreach($documento->tecnologias as $t)<x-badge tone="primary" class="me-1">{{ \App\Models\DocumentoMantenimiento::TECNOLOGIAS[$t] ?? $t }}</x-badge>@endforeach</dd>
            <dt>Estado</dt><dd><x-badge :tone="$documento->vigente ? 'success' : 'muted'">{{ $documento->vigente ? 'Vigente' : 'No vigente' }}</x-badge></dd>
        </x-dl>
    </div></div></div>
    <div class="col-lg-5"><div class="card"><div class="card-body">
        <div class="card-title-sm">Firmas</div>
        @foreach (\App\Models\DocumentoMantenimiento::FIRMAS as $tipo => $etiqueta)
            @php $firma = $documento->firma($tipo); @endphp
            <div class="mb-3 pb-3 border-bottom">
                <div class="small text-muted mb-1">{{ $etiqueta }}</div>
                @if ($firma)
                    <img src="{{ $firma->data_uri }}" class="firma-img" alt="{{ $etiqueta }}">
                    @unless($firma->integridadValida())<div class="small text-danger"><i class="bi bi-shield-exclamation"></i> La firma no coincide con su huella original.</div>@endunless
                @else
                    <span class="text-muted small">Sin firma</span>
                @endif
            </div>
        @endforeach
    </div></div></div>
</div>
@endsection
