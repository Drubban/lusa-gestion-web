@extends('admin.layouts.app')

@section('content')
<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 1rem;
        border: none;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .table td {
        vertical-align: middle;
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">
            <i class="fas fa-clipboard-list me-2"></i>Revisiones Optocontrol
            <small class="text-muted fs-6">Control de unidades enviadas a revision</small>
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.revisiones-optocontrol.dashboard') }}" class="btn btn-info rounded-pill px-4 text-white">
                <i class="fas fa-chart-pie me-2"></i>Dashboard
            </a>
            <a href="{{ route('admin.revisiones-optocontrol.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>Nueva revision
            </a>
        </div>
    </div>

    <!-- Tarjetas resumen -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Revisiones</h6>
                        <h2 class="mb-0">{{ $stats['total'] ?? $revisiones->total() }}</h2>
                        <small>Registros en el sistema</small>
                    </div>
                    <div class="icon-circle bg-white text-primary">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-warning text-dark">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Pendientes</h6>
                        <h2 class="mb-0">{{ $stats['pendientes'] ?? 0 }}</h2>
                        <small>Requieren atencion</small>
                    </div>
                    <div class="icon-circle bg-white text-warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Resueltos</h6>
                        <h2 class="mb-0">{{ $stats['resueltos'] ?? 0 }}</h2>
                        <small>Completados</small>
                    </div>
                    <div class="icon-circle bg-white text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Este Mes</h6>
                        <h2 class="mb-0">{{ $stats['mes'] ?? 0 }}</h2>
                        <small>Revisiones del mes</small>
                    </div>
                    <div class="icon-circle bg-white text-info">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white fw-bold">
            <i class="fas fa-filter me-2"></i>Filtros
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.revisiones-optocontrol.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Dispositivo</label>
                    <select name="disp" class="form-select">
                        <option value="">Todos</option>
                        @foreach($disp_opciones as $opcion)
                            <option value="{{ $opcion }}" {{ request('disp') == $opcion ? 'selected' : '' }}>
                                {{ ucfirst($opcion) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        @foreach($status_opciones as $opcion)
                            <option value="{{ $opcion }}" {{ request('status') == $opcion ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $opcion)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
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
                    <label class="form-label fw-semibold">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 w-100">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                    <a href="{{ route('admin.revisiones-optocontrol.index') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla principal -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i>Lista de Revisiones</span>
            <span class="badge bg-secondary">Total: {{ $revisiones->total() }}</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Dispositivo</th>
                            <th>Unidad</th>
                            <th>Ruta</th>
                            <th>Fecha Reporte</th>
                            <th>Hora Entrada</th>
                            <th>Hora Salida</th>
                            <th>Tiempo</th>
                            <th>Estado</th>
                            <th>Responsable</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revisiones as $revision)
                        <tr>
                            <td><strong>#{{ $revision->id }}</strong></td>
                            <td>
                                <span class="badge bg-primary">{{ $revision->disp_label }}</span>
                            </td>
                            <td>
                                <strong>{{ $revision->unidad->numero_economico ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $revision->nombre_unidad }}</small>
                            </td>
                            <td>{{ $revision->ruta ?? 'N/A' }}</td>
                            <td>{{ $revision->fecha_reporte->format('d/m/Y') }}</td>
                            <td>{{ $revision->hora_entrada ? date('H:i', strtotime($revision->hora_entrada)) : '-' }}</td>
                            <td>{{ $revision->hora_salida ? date('H:i', strtotime($revision->hora_salida)) : '-' }}</td>
                            <td>
                                @if($revision->tiempo_estancia)
                                    <span class="fw-bold">{{ $revision->tiempo_estancia }} hrs</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $revision->status_color }} rounded-pill px-3">
                                    {{ $revision->status_label }}
                                </span>
                            </td>
                            <td>{{ $revision->responsable ?? 'No asignado' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.revisiones-optocontrol.show', $revision->id) }}"
                                       class="btn btn-sm btn-outline-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.revisiones-optocontrol.edit', $revision->id) }}"
                                       class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.revisiones-optocontrol.destroy', $revision->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Estas seguro de eliminar esta revision?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-2x d-block mb-2 text-muted"></i>
                                No hay revisiones registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $revisiones->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('select[name="disp"], select[name="status"], select[name="unidad_id"]').forEach(el => {
        el.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
</script>
@endpush
@endsection