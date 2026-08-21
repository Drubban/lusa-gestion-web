@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Tecnologías de Unidades</h1>
        <a href="{{ route('admin.tecnologias.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i> Agregar Tecnología
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.tecnologias.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Unidad..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        @foreach($tipos as $key => $nombre)
                            <option value="{{ $key }}" {{ request('tipo') == $key ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Unidad</label>
                    <select name="unidad_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($unidades as $unidad)
                            <option value="{{ $unidad->id }}" {{ request('unidad_id') == $unidad->id ? 'selected' : '' }}>
                                {{ $unidad->numero_economico }} - {{ $unidad->nombre_unidad ?? 'Sin nombre' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-search me-2"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.tecnologias.index') }}" class="btn btn-secondary rounded-pill px-4">
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
                            <th class="px-4 py-3">Unidad</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3">Datos</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tecnologias as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <strong>{{ $item->unidad->numero_economico ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $item->unidad->nombre_unidad ?? '' }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-primary">{{ $item->tipo_nombre }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $item->nombre ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $datos = $item->datos;
                                    @endphp
                                    @if($datos)
                                        <small class="text-muted">
                                            @if(isset($datos['imei_telpo']))
                                                IMEI: {{ $datos['imei_telpo'] }}
                                            @elseif(isset($datos['imei_gps']))
                                                IMEI: {{ $datos['imei_gps'] }}
                                            @elseif(isset($datos['id_barra']))
                                                ID: {{ $datos['id_barra'] }}
                                            @elseif(isset($datos['dvr']))
                                                DVR: {{ $datos['dvr'] }}
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">Sin datos</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($item->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.tecnologias.show', $item) }}" 
                                           class="btn btn-sm btn-outline-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.tecnologias.edit', $item) }}" 
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
                                          action="{{ route('admin.tecnologias.destroy', $item) }}" 
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-microchip fa-2x d-block mb-2"></i>
                                    No hay tecnologías registradas
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
            Mostrando {{ $tecnologias->firstItem() ?? 0 }} - {{ $tecnologias->lastItem() ?? 0 }} 
            de {{ $tecnologias->total() }} registros
        </div>
        <div>
            {{ $tecnologias->links() }}
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
                if (id && confirm('¿Estás seguro de eliminar esta tecnología?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        });
    });
</script>
@endsection