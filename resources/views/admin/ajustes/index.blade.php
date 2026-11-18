@extends('layouts.app')
@section('title', 'Ajustes')

@section('content')
<x-page-header title="Ajustes" subtitle="Ajustes de recaudo por operador y unidad">
    <x-slot:actions><a href="{{ route('admin.ajustes.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo ajuste</a></x-slot:actions>
</x-page-header>
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-lg-3"><x-stat label="Ajustes en el filtro" :value="number_format($totales->cantidad)" icon="bi-receipt" tone="primary" /></div>
    <div class="col-sm-6 col-lg-3"><x-stat label="Monto total" :value="'$' . number_format($totales->monto, 2)" icon="bi-cash-stack" tone="accent" /></div>
</div>
<div class="card">
    <div class="card-body border-bottom">
        <form class="filters" method="GET">
            <div class="grow"><label class="form-label">Buscar</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Folio, operador o unidad"></div>
            <div><label class="form-label">Desde</label><input type="date" class="form-control" name="desde" value="{{ request('desde') }}"></div>
            <div><label class="form-label">Hasta</label><input type="date" class="form-control" name="hasta" value="{{ request('hasta') }}"></div>
            <x-form.select name="firmado" label="Firmado" :options="['1' => 'Firmados', '0' => 'Pendientes']" :selected="request('firmado')" placeholder="Todos" wrapper="" />
            <div><button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>Folio</th><th>Fecha</th><th>Operador</th><th>Unidad</th><th>Zona</th><th class="text-end">Monto</th><th>Firma</th><th></th></tr></thead>
        <tbody>
        @forelse ($ajustes as $a)
            <tr>
                <td class="fw-semibold"><a href="{{ route('admin.ajustes.show', $a) }}">{{ $a->folio }}</a></td>
                <td class="tabular">{{ $a->fecha?->format('d/m/Y') }}</td>
                <td>{{ $a->operador?->nombre_completo }}<span class="sub">{{ $a->operador?->clave_operador }}</span></td>
                <td>{{ $a->unidad?->numero_economico }}</td>
                <td>{{ ucfirst($a->zona?->nombre ?? '-') }}</td>
                <td class="text-end tabular">${{ number_format($a->monto_total, 2) }}</td>
                <td><x-badge :tone="$a->firmado ? 'success' : 'warning'" dot>{{ $a->firmado ? 'Firmado' : 'Pendiente' }}</x-badge></td>
                <td class="table-actions">
                    <x-icon-link :href="route('admin.ajustes.edit', $a)" icon="bi-pencil" title="Editar" />
                    <x-delete-button :action="route('admin.ajustes.destroy', $a)" />
                </td>
            </tr>
        @empty
            <tr><td colspan="8"><x-empty icon="bi-receipt" text="Sin ajustes." /></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <x-pagination :paginator="$ajustes" />
</div>
@endsection
