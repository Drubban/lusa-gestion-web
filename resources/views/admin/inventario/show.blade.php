@extends('layouts.app')
@section('title', $inventario->nombre)

@section('content')
<x-page-header :title="$inventario->nombre" :subtitle="$inventario->categoria?->etiqueta()" :back="route('admin.inventario.index')" back-label="Inventario">
    <x-slot:actions>
        <a href="{{ route('admin.inventario.edit', $inventario) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
        <x-delete-button :action="route('admin.inventario.destroy', $inventario)" label="Eliminar" />
    </x-slot:actions>
</x-page-header>
<div class="row g-3">
    <div class="col-lg-7"><div class="card"><div class="card-body">
        <div class="card-title-sm">Articulo</div>
        <x-dl :items="['Marca' => $inventario->marca, 'Modelo' => $inventario->modelo, 'Numero de serie' => $inventario->numero_serie, 'Cantidad' => $inventario->cantidad, 'Descripcion' => $inventario->descripcion]" />
        <hr>
        <div class="card-title-sm">Entrega</div>
        <x-dl :items="[
            'Fecha' => $inventario->fecha_entrega?->format('d/m/Y'),
            'Recibe' => $inventario->nombre_recibe . ' (' . $inventario->clave_empleado . ')',
            'Departamento' => ucfirst($inventario->departamento?->nombre ?? ''),
            'Area' => $inventario->area,
            'Registrado por' => $inventario->creador?->nombre_visible,
        ]" />
    </div></div></div>
    <div class="col-lg-5"><div class="card"><div class="card-body">
        <div class="card-title-sm">Evidencia</div>
        @if ($inventario->imagen_url)
            @if (str_ends_with(strtolower($inventario->imagen), '.pdf'))
                <a href="{{ $inventario->imagen_url }}" target="_blank" rel="noopener" class="btn btn-light"><i class="bi bi-file-earmark-pdf me-1"></i>Abrir PDF</a>
            @else
                <img src="{{ $inventario->imagen_url }}" class="img-fluid rounded" alt="Evidencia">
            @endif
        @else
            <x-empty icon="bi-image" text="Sin archivo adjunto." />
        @endif
    </div></div></div>
</div>
@endsection
