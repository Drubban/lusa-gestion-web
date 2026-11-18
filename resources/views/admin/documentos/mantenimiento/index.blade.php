@extends('layouts.app')
@section('title', 'Documentos de mantenimiento')

@section('content')
<x-page-header title="Documentos de mantenimiento" subtitle="Formato de servicio y analisis de equipos tecnologicos">
    <x-slot:actions><a href="{{ route('admin.documentos-mantenimiento.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo documento</a></x-slot:actions>
</x-page-header>
<div class="card">
    <div class="card-body border-bottom">
        <form class="filters" method="GET">
            <div class="grow"><label class="form-label">Unidad</label><input class="form-control" name="unidad" value="{{ request('unidad') }}" placeholder="Numero economico"></div>
            <div><label class="form-label">Desde</label><input type="date" class="form-control" name="desde" value="{{ request('desde') }}"></div>
            <div><label class="form-label">Hasta</label><input type="date" class="form-control" name="hasta" value="{{ request('hasta') }}"></div>
            <div><button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>Folio</th><th>Fecha</th><th>Unidad</th><th>Operador</th><th>Tecnologia</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        @forelse ($documentos as $d)
            <tr>
                <td class="fw-semibold"><a href="{{ route('admin.documentos-mantenimiento.show', $d) }}">{{ $d->folio }}</a></td>
                <td class="tabular">{{ $d->fecha?->format('d/m/Y') }} <span class="sub">{{ substr((string) $d->hora, 0, 5) }}</span></td>
                <td>{{ $d->unidad?->numero_economico }}<span class="sub">{{ ucfirst($d->unidad?->zona?->nombre ?? '') }}</span></td>
                <td>{{ $d->operador?->nombre_completo ?? '-' }}</td>
                <td>@foreach($d->tecnologias as $t)<x-badge tone="primary" class="me-1">{{ \App\Models\DocumentoMantenimiento::TECNOLOGIAS[$t] ?? $t }}</x-badge>@endforeach</td>
                <td><x-badge :tone="$d->vigente ? 'success' : 'muted'">{{ $d->vigente ? 'Vigente' : 'No vigente' }}</x-badge></td>
                <td class="table-actions">
                    <x-icon-link :href="route('admin.documentos-mantenimiento.pdf', $d)" icon="bi-file-earmark-pdf" title="PDF" />
                    <x-icon-link :href="route('admin.documentos-mantenimiento.edit', $d)" icon="bi-pencil" title="Editar" />
                    <x-delete-button :action="route('admin.documentos-mantenimiento.destroy', $d)" />
                </td>
            </tr>
        @empty
            <tr><td colspan="7"><x-empty icon="bi-file-earmark-text" text="No hay documentos." /></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <x-pagination :paginator="$documentos" />
</div>
@endsection
