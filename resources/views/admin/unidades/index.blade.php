@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Unidades</h1>
        <a href="{{ route('admin.unidades.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus"></i> Nueva Unidad
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Panel de filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.unidades.index') }}" class="row g-3 align-items-end">
                <!-- Búsqueda general -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="N° Económico, nombre u operador" value="{{ request('search') }}">
                    </div>
                </div>
                
                <!-- Filtro por estado -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                
                <!-- Botones de filtro -->
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.unidades.index') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-undo"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de unidades -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <!-- N° Económico -->
                            <th>
                                <a href="{{ route('admin.unidades.index', array_merge(request()->all(), ['sort' => 'numero_economico', 'direction' => request('sort') == 'numero_economico' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-dark text-decoration-none">
                                    N° Económico
                                    @if(request('sort') == 'numero_economico')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <!-- Nombre unidad -->
                            <th>
                                <a href="{{ route('admin.unidades.index', array_merge(request()->all(), ['sort' => 'nombre_unidad', 'direction' => request('sort') == 'nombre_unidad' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-dark text-decoration-none">
                                    Nombre unidad
                                    @if(request('sort') == 'nombre_unidad')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <!-- Zona -->
                            <th>
                                <a href="{{ route('admin.unidades.index', array_merge(request()->all(), ['sort' => 'zona', 'direction' => request('sort') == 'zona' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-dark text-decoration-none">
                                    Zona
                                    @if(request('sort') == 'zona')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <!-- Operador actual -->
                            <th>
                                <a href="{{ route('admin.unidades.index', array_merge(request()->all(), ['sort' => 'operador', 'direction' => request('sort') == 'operador' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-dark text-decoration-none">
                                    Operador actual
                                    @if(request('sort') == 'operador')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unidades as $unidad)
                        <tr>
                            <td>{{ $unidad->numero_economico }}</td>
                            <td>{{ $unidad->nombre_unidad ?? 'Sin nombre' }}</td>
                            <td>{{ ucfirst($unidad->zona->nombre ?? 'Sin zona') }}</td>
                            <td>
                                @if($unidad->asignacionVigente && $unidad->asignacionVigente->operador)
                                    {{ $unidad->asignacionVigente->operador->nombre_completo }}
                                @else
                                    <span class="text-muted">Sin operador</span>
                                @endif
                            </td>
                            <td>{!! $unidad->activo ? '<span class="badge bg-success rounded-pill px-3">Activo</span>' : '<span class="badge bg-danger rounded-pill px-3">Inactivo</span>' !!}</td>
                            <td>
                                <a href="{{ route('admin.unidades.show', $unidad) }}" class="btn btn-sm btn-outline-info rounded-circle" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.unidades.edit', $unidad) }}" class="btn btn-sm btn-outline-warning rounded-circle" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.unidades.destroy', $unidad) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta unidad?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-circle" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4">No hay unidades registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="small text-muted">
                    Mostrando {{ $unidades->firstItem() ?? 0 }} - {{ $unidades->lastItem() ?? 0 }} de {{ $unidades->total() }} registros
                </div>
                <div>
                    {{ $unidades->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Reducir tamaño de botones de paginación */
    .pagination {
        --bs-pagination-padding-x: 0.5rem;
        --bs-pagination-padding-y: 0.25rem;
        --bs-pagination-font-size: 0.75rem;
        margin-bottom: 0;
    }
    
    .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endsection