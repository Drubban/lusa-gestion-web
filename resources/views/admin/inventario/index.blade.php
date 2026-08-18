@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Inventario</h1>
        <a href="{{ route('admin.inventario.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i> Nuevo Registro
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.inventario.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Nombre, clave, serie..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Categoría</label>
                    <select name="categoria" class="form-select">
                        <option value="">Todas</option>
                        @foreach($categorias as $key => $nombre)
                        <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                        @endforeach
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
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-search me-2"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.inventario.index') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-undo me-2"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Recibe</th>
                            <th class="px-4 py-3">Departamento</th>
                            <th class="px-4 py-3">Zona</th>
                            <th class="px-4 py-3">Área</th>
                            <th class="px-4 py-3">Imagen</th>
                            <th class="px-4 py-3">Categoría</th>
                            <th class="px-4 py-3">Producto/Equipo</th>
                            <th class="px-4 py-3">Cantidad/Serie</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventarios as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->fecha_entrega->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <strong>{{ $item->nombre_recibe }}</strong>
                                <br><small class="text-muted">Clave: {{ $item->clave_empleado }}</small>
                            </td>
                            <td class="px-4 py-3">{{ $item->departamento->nombre ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $item->zona->nombre ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $item->area ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                @if($item->imagen)
                                <a href="{{ $item->imagen_url }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-image"></i>
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-info">{{ $item->nombre_categoria }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($item->esCategoriaEquipo())
                                {{ $item->nombre_equipo ?? 'N/A' }}
                                @else
                                {{ $item->nombre_producto ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($item->esCategoriaEquipo())
                                <span class="badge bg-secondary">Serie: {{ $item->numero_serie ?? 'N/A' }}</span>
                                @else
                                <span class="badge bg-success">Cant: {{ $item->cantidad ?? 0 }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.inventario.show', $item) }}"
                                        class="btn btn-sm btn-outline-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.inventario.edit', $item) }}"
                                        class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- 🔥 CORREGIDO: Usar data-id en lugar de onclick directo -->
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger btn-delete"
                                        title="Eliminar"
                                        data-id="{{ $item->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <!-- Formulario oculto para eliminar -->
                                <form id="delete-form-{{ $item->id }}"
                                    action="{{ route('admin.inventario.destroy', $item) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x d-block mb-2"></i>
                                No hay registros de inventario
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4 d-flex justify-content-between align-items-center">
        <div>
            Mostrando {{ $inventarios->firstItem() ?? 0 }} - {{ $inventarios->lastItem() ?? 0 }}
            de {{ $inventarios->total() }} registros
        </div>
        <div>
            {{ $inventarios->links() }}
        </div>
    </div>
</div>

<!-- 🔥 SCRIPT CORREGIDO: Usar event delegation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Usar event delegation para manejar todos los botones de eliminar
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.btn-delete');
            if (deleteBtn) {
                e.preventDefault();
                const id = deleteBtn.getAttribute('data-id');
                if (id && confirm('¿Estás seguro de eliminar este registro?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        });
    });
</script>
@endsection