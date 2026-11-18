@extends('layouts.app')
@section('title', 'Capacitacion')

@section('content')
<x-page-header title="Conformidad de capacitacion" subtitle="Constancias firmadas por operador">
    <x-slot:actions><a href="{{ route('admin.documentos-capacitacion.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nueva constancia</a></x-slot:actions>
</x-page-header>
<div class="card">
    <div class="card-body border-bottom">
        <form class="filters" method="GET">
            <div class="grow"><label class="form-label">Buscar</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Operador o unidad"></div>
            <div><button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>Folio</th><th>Fecha</th><th>Operador</th><th>Unidad</th><th>Tipo</th><th>Vence</th><th></th></tr></thead>
        <tbody>
        @forelse ($documentos as $d)
            <tr>
                <td class="fw-semibold"><a href="{{ route('admin.documentos-capacitacion.show', $d) }}">{{ $d->folio }}</a></td>
                <td class="tabular">{{ $d->fecha?->format('d/m/Y') }}</td>
                <td>{{ $d->operador?->nombre_completo ?? '-' }}<span class="sub">{{ $d->operador?->clave_operador }}</span></td>
                <td>{{ $d->unidad?->numero_economico }}</td>
                <td>{{ $d->tipo_capacitacion ?? '-' }}</td>
                <td>@if($d->fecha_vencimiento)<x-badge :tone="$d->fecha_vencimiento->isPast() ? 'danger' : 'muted'">{{ $d->fecha_vencimiento->format('d/m/Y') }}</x-badge>@else - @endif</td>
                <td class="table-actions">
                    <x-icon-link :href="route('admin.documentos-capacitacion.pdf', $d)" icon="bi-file-earmark-pdf" title="PDF" />
                    <x-icon-link :href="route('admin.documentos-capacitacion.edit', $d)" icon="bi-pencil" title="Editar" />
                    <x-delete-button :action="route('admin.documentos-capacitacion.destroy', $d)" />
                </td>
            </tr>
        @empty
            <tr><td colspan="7"><x-empty icon="bi-mortarboard" text="No hay constancias." /></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <x-pagination :paginator="$documentos" />
</div>
@endsection
