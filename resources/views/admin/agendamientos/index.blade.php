@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">
            <i class="fas fa-calendar-plus me-2"></i>Agendamientos de Mantenimiento
        </h1>
        <a href="{{ route('admin.agendamientos.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i>Nuevo Agendamiento
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Unidad</label>
                    <select name="unidad_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($unidades as $unidad)
                            <option value="{{ $unidad->id }}" {{ request('unidad_id') == $unidad->id ? 'selected' : '' }}>
                                {{ $unidad->numero_economico }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="cumplido" {{ request('estado') == 'cumplido' ? 'selected' : '' }}>Cumplido</option>
                        <option value="no_cumplido" {{ request('estado') == 'no_cumplido' ? 'selected' : '' }}>No cumplido</option>
                        <option value="reagendado" {{ request('estado') == 'reagendado' ? 'selected' : '' }}>Reagendado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                    <a href="{{ route('admin.agendamientos.index') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-undo me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Unidad</th>
                            <th>Fecha Agendada</th>
                            <th>Estado</th>
                            <th>Días Restantes</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agendamientos as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->unidad->numero_economico }}</strong>
                                <br><small class="text-muted">{{ $item->unidad->nombre_unidad ?? '' }}</small>
                            </td>
                            <td>{{ $item->fecha_agendada->format('d/m/Y') }}</td>
                            <td>{!! $item->estado_badge !!}</td>
                            <td>
                                @if($item->estado == 'pendiente')
                                    @php
                                        $dias = $item->dias_restantes;
                                    @endphp
                                    @if($dias > 7)
                                        <span class="text-success">{{ $dias }} días</span>
                                    @elseif($dias >= 0)
                                        <span class="text-warning">{{ $dias }} días</span>
                                    @else
                                        <span class="text-danger">{{ abs($dias) }} días vencido</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ \Str::limit($item->observaciones, 30) }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.agendamientos.edit', $item) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($item->estado == 'pendiente')
                                        <a href="{{ route('admin.agendamientos.marcar-cumplido', $item) }}" 
                                           class="btn btn-sm btn-outline-success" title="Marcar como cumplido"
                                           onclick="return confirm('¿Marcar este agendamiento como cumplido?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-info" 
                                                title="Reagendar"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reagendarModal"
                                                data-id="{{ $item->id }}"
                                                data-unidad="{{ $item->unidad->numero_economico }}"
                                                data-fecha="{{ $item->fecha_agendada->format('Y-m-d') }}">
                                            <i class="fas fa-calendar-plus"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('admin.agendamientos.destroy', $item) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar este agendamiento?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-calendar-alt fa-2x d-block mb-2 text-muted"></i>
                                No hay agendamientos registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $agendamientos->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Modal para Reagendar -->
<div class="modal fade" id="reagendarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="reagendarForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reagendar Mantenimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Unidad: <strong id="modalUnidad"></strong></p>
                    <p>Fecha actual: <strong id="modalFecha"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Nueva Fecha *</label>
                        <input type="date" name="nueva_fecha" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo</label>
                        <textarea name="motivo" class="form-control" rows="2" placeholder="¿Por qué se reagenda?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Reagendar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('reagendarModal');
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const unidad = button.getAttribute('data-unidad');
            const fecha = button.getAttribute('data-fecha');
            
            document.getElementById('modalUnidad').textContent = unidad;
            document.getElementById('modalFecha').textContent = fecha;
            
            const form = document.getElementById('reagendarForm');
            form.action = '/admin/agendamientos/' + id + '/reagendar';
        });
    });
</script>
@endsection