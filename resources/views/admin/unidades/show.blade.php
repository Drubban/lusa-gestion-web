@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Detalle de Unidad</h1>
        <div>
            <a href="{{ route('admin.unidades.edit', $unidad->id) }}" class="btn btn-warning rounded-pill px-4">
                <i class="fas fa-edit me-2"></i> Editar
            </a>
            <a href="{{ route('admin.unidades.index') }}" class="btn btn-secondary rounded-pill px-4">
                Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Informacion basica -->
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Numero Economico</strong>
                            </div>
                            <p class="fs-5">{{ $unidad->numero_economico }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Nombre</strong>
                            </div>
                            <p>{{ $unidad->nombre_unidad ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Zona</strong>
                            </div>
                            <p>{{ $unidad->zona->nombre ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Estado</strong>
                            </div>
                            <p>
                                @if($unidad->activo)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-danger">Inactiva</span>
                                @endif
                            </p>
                        </div>

                        <!-- EQUIPOS ASIGNADOS - RESUMEN UNIFICADO -->
                        <div class="col-12">
                            <hr class="border-2 border-primary">
                            <h5 class="text-primary"><i class="fas fa-microchip me-2"></i>Equipos Asignados</h5>
                        </div>

                        <div class="col-12">
                            @php
                                $equipos = [];
                                if ($unidad->equipo_telpo) $equipos[] = 'E.T (Telpo)';
                                if ($unidad->equipo_gps) $equipos[] = 'E.G (GPS)';
                                if ($unidad->equipo_barras) $equipos[] = 'E.B (Barras)';
                            @endphp

                            @if(count($equipos) > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($equipos as $equipo)
                                        <span class="badge bg-success fs-6 p-2">
                                            <i class="fas fa-check-circle me-1"></i> {{ $equipo }}
                                        </span>
                                    @endforeach
                                    <span class="badge bg-secondary fs-6 p-2">
                                        <i class="fas fa-info-circle me-1"></i> Total: {{ count($equipos) }} equipo(s)
                                    </span>
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Esta unidad no tiene equipos asignados (ET, EG, EB)
                                </div>
                            @endif
                        </div>

                        <!-- QR -->
                        <div class="col-12 mt-3">
                            <hr class="border-2 border-secondary">
                            <h5><i class="fas fa-qrcode me-2"></i>Codigo QR</h5>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="border-bottom pb-2 mb-2">
                                        <strong class="text-muted">Codigo QR</strong>
                                    </div>
                                    <p><code>{{ $unidad->codigo_qr }}</code></p>
                                </div>
                                <div class="col-md-4">
                                    <div class="border-bottom pb-2 mb-2">
                                        <strong class="text-muted">Token</strong>
                                    </div>
                                    <p><code>{{ $unidad->token_qr ?? 'N/A' }}</code></p>
                                </div>
                            </div>
                        </div>

                        <!-- Operador Actual -->
                        <div class="col-12">
                            <hr class="border-2 border-secondary">
                            <h5><i class="fas fa-user me-2"></i>Operador Actual</h5>
                            @if($operadorActual)
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <strong>Nombre:</strong> {{ $operadorActual->nombre_completo }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Clave:</strong> {{ $operadorActual->clave_operador }}
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">No hay operador asignado actualmente</p>
                            @endif
                        </div>

                        <!-- Asignaciones Historicas -->
                        <div class="col-12">
                            <hr class="border-2 border-secondary">
                            <h5><i class="fas fa-history me-2"></i>Historial de Asignaciones</h5>
                            @if($unidad->asignaciones->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Operador</th>
                                                <th>Inicio</th>
                                                <th>Fin</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($unidad->asignaciones->sortByDesc('fecha_inicio') as $asignacion)
                                                <tr>
                                                    <td>{{ $asignacion->operador->nombre_completo ?? 'N/A' }}</td>
                                                    <td>{{ $asignacion->fecha_inicio->format('d/m/Y') }}</td>
                                                    <td>{{ $asignacion->fecha_fin ? $asignacion->fecha_fin->format('d/m/Y') : 'Actual' }}</td>
                                                    <td>
                                                        @if($asignacion->vigente)
                                                            <span class="badge bg-success">Vigente</span>
                                                        @else
                                                            <span class="badge bg-secondary">Finalizada</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No hay asignaciones registradas</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Informacion Adicional</h5>
                    <hr>
                    <div class="mb-3">
                        <strong class="text-muted">ID</strong>
                        <p><span class="badge bg-secondary">#{{ $unidad->id }}</span></p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Fecha de creacion</strong>
                        <p>{{ $unidad->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Ultima actualizacion</strong>
                        <p>{{ $unidad->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Codigo QR</strong>
                        <p><small><code>{{ substr($unidad->codigo_qr, 0, 20) }}...</code></small></p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Equipos instalados</strong>
                        <p>
                            @php
                                $totalEquipos = 0;
                                if ($unidad->equipo_telpo) $totalEquipos++;
                                if ($unidad->equipo_gps) $totalEquipos++;
                                if ($unidad->equipo_barras) $totalEquipos++;
                            @endphp
                            <span class="badge bg-primary">{{ $totalEquipos }} de 3</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection