@extends('admin.layouts.app')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1>Detalle de Unidad</h1>
        <div>
            <a href="{{ route('admin.unidades.edit', $unidad) }}" class="btn btn-warning">Editar</a>
            <a href="{{ route('admin.unidades.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Información general</div>
                <div class="card-body">
                    <p><strong>ID:</strong> {{ $unidad->id }}</p>
                    <p><strong>Número económico:</strong> {{ $unidad->numero_economico }}</p>
                    <p><strong>Nombre unidad:</strong> {{ $unidad->nombre_unidad ?? 'Sin nombre' }}</p>
                    <p><strong>Zona:</strong> {{ ucfirst($unidad->zona->nombre ?? 'Sin asignar') }}</p>
                    <p><strong>Código QR (token):</strong> <code>{{ $unidad->token_qr }}</code></p>
                    <p><strong>Estado:</strong>
                        {!! $unidad->activo ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>' !!}
                    </p>
                    <p><strong>Registrado:</strong> {{ $unidad->created_at->format('d/m/Y H:i') }}</p>
                    @can('admin') {{-- o un permiso específico --}}
                        <a href="{{ route('admin.unidades.regenerar-token', $unidad) }}"
                            class="btn btn-sm btn-secondary">Regenerar token QR</a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Operador actual</div>
                <div class="card-body">
                    @if($operadorActual)
                        <p><strong>Nombre:</strong> {{ $operadorActual->nombre_completo }}</p>
                        <p><strong>Clave:</strong> {{ $operadorActual->clave_operador }}</p>
                        <p><strong>Zona:</strong> {{ $operadorActual->zona->nombre ?? 'N/A' }}</p>
                    @else
                        <p class="text-muted">No tiene operador asignado actualmente.</p>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-header">Historial de asignaciones</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($unidad->asignaciones->sortByDesc('fecha_inicio') as $asig)
                            <li class="list-group-item">
                                <strong>{{ $asig->operador->nombre_completo }}</strong>
                                ({{ $asig->operador->clave_operador }})<br>
                                Desde {{ $asig->fecha_inicio->format('d/m/Y') }}
                                @if($asig->fecha_fin) hasta {{ $asig->fecha_fin->format('d/m/Y') }} @else <span
                                class="badge bg-success">Actual</span> @endif
                            </li>
                        @empty
                            <li class="list-group-item">Sin asignaciones previas</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">Últimos movimientos (entrada/salida)</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($unidad->movimientos->sortByDesc('fecha_hora')->take(5) as $mov)
                            <li class="list-group-item">
                                {{ ucfirst($mov->tipo) }} - {{ $mov->departamento->nombre ?? 'N/A' }}<br>
                                <small>{{ $mov->fecha_hora->format('d/m/Y H:i') }}</small>
                            </li>
                        @empty
                            <li class="list-group-item">Sin movimientos registrados</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection