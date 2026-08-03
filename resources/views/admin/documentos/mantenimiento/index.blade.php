@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Documentos de Mantenimiento</h1>
        <a href="{{ route('admin.documentos-mantenimiento.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus"></i> Nuevo documento
        </a>
    </div>

    <!-- Filtro rápido por unidad -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.documentos-mantenimiento.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Unidad (N° económico)</label>
                    <input type="text" name="unidad" class="form-control" placeholder="Ej: ECO-001" value="{{ request('unidad') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="fas fa-filter"></i> Filtrar</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.documentos-mantenimiento.index') }}" class="btn btn-secondary w-100 rounded-pill"><i class="fas fa-undo"></i> Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Unidad</th>
                            <th>Operador</th>
                            <th>Rol</th>
                            <th>Tecnología</th>
                            <th>Prueba barras</th>
                            <th>Fecha</th>
                            <th>Vigente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                        <tr>
                            <td>{{ $doc->id }}</td>
                            <td>{{ $doc->asignacion->unidad->numero_economico ?? 'N/A' }}</td>
                            <td>{{ $doc->asignacion->operador->nombre_completo ?? 'N/A' }}</td>
                            <td>{{ strtoupper($doc->rol ?? '') }}</td>
                            <td>{{ $doc->tecnologia_reportada }}</td>
                            <td>{{ $doc->prueba_barras ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }} {{ $doc->hora }}</td>
                            <td>{!! $doc->vigente ? '<span class="badge bg-success rounded-pill px-3">Vigente</span>' : '<span class="badge bg-secondary rounded-pill px-3">No vigente</span>' !!}</td>
                            <td>
                                <a href="{{ route('admin.documentos-mantenimiento.show', $doc) }}" class="btn btn-sm btn-outline-info rounded-circle"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.documentos-mantenimiento.edit', $doc) }}" class="btn btn-sm btn-outline-warning rounded-circle"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('admin.documentos-mantenimiento.exportar-pdf', $doc) }}" class="btn btn-sm btn-outline-primary rounded-circle"><i class="fas fa-file-pdf"></i></a>
                                <form action="{{ route('admin.documentos-mantenimiento.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-circle"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">No hay documentos de mantenimiento registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $documentos->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection