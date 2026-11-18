@extends('layouts.app')
@section('title', $operador->nombre_completo)

@section('content')
<x-page-header :title="$operador->nombre_completo" :subtitle="'Clave ' . $operador->clave_operador" :back="route('admin.operadores.index')" back-label="Operadores">
    <x-slot:actions><a href="{{ route('admin.operadores.edit', $operador) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a></x-slot:actions>
</x-page-header>
<div class="row g-3">
    <div class="col-lg-4"><div class="card"><div class="card-body">
        <div class="card-title-sm">Datos</div>
        <x-dl :items="['Clave' => $operador->clave_operador, 'Zona' => ucfirst($operador->zona_nombre), 'Alta' => $operador->created_at?->format('d/m/Y')]">
            <dt>Estado</dt><dd><x-badge :tone="$operador->activo ? 'success' : 'muted'" dot>{{ $operador->activo ? 'Activo' : 'Inactivo' }}</x-badge></dd>
        </x-dl>
    </div></div></div>
    <div class="col-lg-8"><div class="card">
        <div class="card-header">Historial de unidades</div>
        <div class="table-responsive"><table class="table">
            <thead><tr><th>Unidad</th><th>Zona</th><th>Desde</th><th>Hasta</th></tr></thead>
            <tbody>
            @forelse ($operador->asignaciones as $a)
                <tr>
                    <td>@if($a->unidad)<a href="{{ route('admin.unidades.show', $a->unidad) }}">{{ $a->unidad->numero_economico }}</a>@endif</td>
                    <td>{{ ucfirst($a->unidad?->zona?->nombre ?? '-') }}</td>
                    <td class="tabular">{{ $a->fecha_inicio?->format('d/m/Y') }}</td>
                    <td>@if($a->vigente)<x-badge tone="success">Actual</x-badge>@else<span class="tabular">{{ $a->fecha_fin?->format('d/m/Y') }}</span>@endif</td>
                </tr>
            @empty
                <tr><td colspan="4"><x-empty text="Sin asignaciones." /></td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div></div>
</div>
@endsection
