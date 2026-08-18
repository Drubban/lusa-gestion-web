@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Ajustes</h1>
        <a href="{{ route('admin.ajustes.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i> Nuevo Ajuste
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.ajustes.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Folio, operador, unidad..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Firmado</label>
                    <select name="firmado" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('firmado') == '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ request('firmado') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-search me-2"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.ajustes.index') }}" class="btn btn-secondary rounded-pill px-4">
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
                            <th class="px-4 py-3">Folio</th>
                            <th class="px-4 py-3">Fecha/Hora</th>
                            <th class="px-4 py-3">Zona</th>
                            <th class="px-4 py-3">Operador</th>
                            <th class="px-4 py-3">Unidad</th>
                            <th class="px-4 py-3">Monto</th>
                            <th class="px-4 py-3">Firmado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ajustes as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <strong>{{ $item->folio }}</strong>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $item->fecha->format('d/m/Y') }}
                                    <br><small class="text-muted">{{ $item->hora }}</small>
                                </td>
                                <td class="px-4 py-3">{{ $item->zona ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <strong>{{ $item->operador->nombre_completo ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">Clave: {{ $item->clave_operador }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $item->unidad->numero_economico ?? 'N/A' }}
                                    <br><small class="text-muted">{{ $item->unidad->nombre_unidad ?? '' }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="fw-bold">${{ number_format($item->monto_total, 2) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($item->firmado)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i> Firmado
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i> Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.ajustes.show', $item) }}" 
                                           class="btn btn-sm btn-outline-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.ajustes.edit', $item) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger btn-delete" 
                                                title="Eliminar"
                                                data-id="{{ $item->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $item->id }}" 
                                          action="{{ route('admin.ajustes.destroy', $item) }}" 
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-file-invoice fa-2x d-block mb-2"></i>
                                    No hay registros de ajustes
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
            Mostrando {{ $ajustes->firstItem() ?? 0 }} - {{ $ajustes->lastItem() ?? 0 }} 
            de {{ $ajustes->total() }} registros
        </div>
        <div>
            {{ $ajustes->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.btn-delete');
            if (deleteBtn) {
                e.preventDefault();
                const id = deleteBtn.getAttribute('data-id');
                if (id && confirm('¿Estás seguro de eliminar este ajuste?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        });
    });
</script>
@endsection