@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Detalle del Operador</h1>
        <div>
            <a href="{{ route('admin.operadores.edit', $operador) }}" class="btn btn-warning rounded-pill px-4">Editar</a>
            <a href="{{ route('admin.operadores.index') }}" class="btn btn-secondary rounded-pill px-4">Volver</a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Clave operador:</strong></div>
                    <p>{{ $operador->clave_operador }}</p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Nombre completo:</strong></div>
                    <p>{{ $operador->nombre_completo }}</p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Unidad actual:</strong></div>
                    <p>{{ $unidadActual ? $unidadActual->numero_economico . ' - ' . $unidadActual->nombre_unidad : 'Sin unidad' }}</p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Zona (según unidad):</strong></div>
                    <p>{{ $unidadActual && $unidadActual->zona ? ucfirst($unidadActual->zona->nombre) : 'Sin zona' }}</p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Estado:</strong></div>
                    <p>{!! $operador->activo ? '<span class="badge bg-success rounded-pill px-3">Activo</span>' : '<span class="badge bg-danger rounded-pill px-3">Inactivo</span>' !!}</p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Registrado:</strong></div>
                    <p>{{ $operador->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="mt-4">
                <div class="border-bottom pb-2 mb-2"><strong>Historial de asignaciones</strong></div>
                <ul class="list-group">
                    @forelse($operador->asignaciones->sortByDesc('fecha_inicio') as $asig)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>{{ $asig->unidad->numero_economico }}</strong> - {{ $asig->unidad->nombre_unidad }}</span>
                        <span>Desde {{ $asig->fecha_inicio->format('d/m/Y') }} @if($asig->fecha_fin) hasta {{ $asig->fecha_fin->format('d/m/Y') }} @else <span class="badge bg-success rounded-pill">Actual</span> @endif</span>
                    </li>
                    @empty
                    <li class="list-group-item text-muted">Sin asignaciones previas</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection