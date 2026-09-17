@extends('admin.layouts.app')

@section('title', 'Detalle Revisión Optocontrol')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3">Revisión Optocontrol #{{ $revision->id }}</h1>
                <div>
                    <a href="{{ route('admin.revisiones-optocontrol.edit', $revision->id) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('admin.revisiones-optocontrol.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Información General</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">ID</th>
                                    <td>{{ $revision->id }}</td>
                                </tr>
                                <tr>
                                    <th>Dispositivo</th>
                                    <td>{{ $revision->disp_label }}</td>
                                </tr>
                                <tr>
                                    <th>Unidad</th>
                                    <td>
                                        {{ $revision->unidad->numero_economico ?? 'N/A' }}
                                        <br>
                                        <small>{{ $revision->nombre_unidad }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ruta</th>
                                    <td>{{ $revision->ruta ?? 'No especificada' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Reporte</th>
                                    <td>{{ $revision->fecha_reporte->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Hora Entrada</th>
                                    <td>{{ $revision->hora_entrada ? date('H:i', strtotime($revision->hora_entrada)) : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Hora Salida</th>
                                    <td>{{ $revision->hora_salida ? date('H:i', strtotime($revision->hora_salida)) : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tiempo Estancia</th>
                                    <td>{{ $revision->tiempo_estancia ? $revision->tiempo_estancia . ' horas' : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Estado</th>
                                    <td>
                                        <span class="badge bg-{{ $revision->status_color }}">
                                            {{ $revision->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Responsable</th>
                                    <td>{{ $revision->responsable ?? 'No asignado' }}</td>
                                </tr>
                                <tr>
                                    <th>Creado por</th>
                                    <td>{{ $revision->creador->nombre_usuario ?? 'Sistema' }}</td>
                                </tr>
                                <tr>
                                    <th>Creado el</th>
                                    <td>{{ $revision->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Última actualización</th>
                                    <td>{{ $revision->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Problema</h5>
                        </div>
                        <div class="card-body">
                            <p>{{ $revision->problema }}</p>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Solución</h5>
                        </div>
                        <div class="card-body">
                            @if($revision->solucion)
                                <p>{{ $revision->solucion }}</p>
                            @else
                                <p class="text-muted">Sin solución registrada</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection