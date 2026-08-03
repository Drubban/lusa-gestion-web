@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Operadores</h1>
        <a href="{{ route('admin.operadores.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus"></i> Nuevo Operador
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <!-- Panel de filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.operadores.index') }}" class="row g-3 align-items-end">
                <!-- Búsqueda general -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Clave, nombre o unidad" value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Filtro por zona -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Zona</label>
                    <select name="zona" class="form-select">
                        <option value="">Todas</option>
                        <option value="reyes" {{ request('zona') == 'reyes' ? 'selected' : '' }}>Reyes</option>
                        <option value="apaxco" {{ request('zona') == 'apaxco' ? 'selected' : '' }}>Apaxco</option>
                        <option value="citrus" {{ request('zona') == 'citrus' ? 'selected' : '' }}>Citrus</option>
                    </select>
                </div>

                <!-- Botones de filtro -->
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.operadores.index') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-undo"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de operadores -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <!-- Clave -->
                            <th>
                                <a href="{{ route('admin.operadores.index', array_merge(request()->all(), ['sort' => 'clave_operador', 'direction' => request('sort') == 'clave_operador' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="text-dark text-decoration-none">
                                    Clave
                                    @if(request('sort') == 'clave_operador')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                    <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <!-- Nombre -->
                            <th>
                                <a href="{{ route('admin.operadores.index', array_merge(request()->all(), ['sort' => 'nombre_completo', 'direction' => request('sort') == 'nombre_completo' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="text-dark text-decoration-none">
                                    Nombre
                                    @if(request('sort') == 'nombre_completo')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                    <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <!-- Unidad actual (N° Económico) -->
                            <th>
                                <a href="{{ route('admin.operadores.index', array_merge(request()->all(), ['sort' => 'unidad_numero', 'direction' => request('sort') == 'unidad_numero' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="text-dark text-decoration-none">
                                    Unidad actual
                                    @if(request('sort') == 'unidad_numero')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                    <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Zona</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($operadores as $operador)
                        <tr>
                            <td>{{ $operador->clave_operador }}</td>
                            <td>{{ $operador->nombre_completo }}</td>
                            <td>
                                @if($operador->asignacionVigente && $operador->asignacionVigente->unidad)
                                {{ $operador->asignacionVigente->unidad->numero_economico }}
                                @else
                                <span class="text-muted">Sin unidad</span>
                                @endif
                            </td>
                            <td>
                                @if($operador->asignacionVigente && $operador->asignacionVigente->unidad && $operador->asignacionVigente->unidad->zona)
                                {{ ucfirst($operador->asignacionVigente->unidad->zona->nombre) }}
                                @else
                                <span class="text-muted">Sin zona</span>
                                @endif
                            </td>
                            <td>{!! $operador->activo ? '<span class="badge bg-success rounded-pill px-3">Activo</span>' : '<span class="badge bg-danger rounded-pill px-3">Inactivo</span>' !!}</td>
                            <td>
                                <a href="{{ route('admin.operadores.show', $operador) }}" class="btn btn-sm btn-outline-info rounded-circle" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.operadores.edit', $operador) }}" class="btn btn-sm btn-outline-warning rounded-circle" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.operadores.destroy', $operador) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-circle" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No hay operadores registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-center">
                {{ $operadores->links('pagination::bootstrap-5', ['class' => 'pagination-sm']) }}
            </div>
        </div>
    </div>
</div>
@endsection