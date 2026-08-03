@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1>Movimientos de unidades</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Filtros estilo tarjeta moderna -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <i class="fas fa-filter"></i> Filtros
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.movimientos.index') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="entrada" {{ request('tipo') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                    <option value="salida" {{ request('tipo') == 'salida' ? 'selected' : '' }}>Salida</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Unidad (N° Económico)</label>
                <input type="text" name="unidad" class="form-control" placeholder="Ej: ECO-001" value="{{ request('unidad') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Clave de operador</label>
                <input type="text" name="clave_operador" class="form-control" placeholder="5 dígitos" value="{{ request('clave_operador') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Filtrar</button>
            </div>
        </form>
        @if(request()->anyFilled(['tipo','fecha','unidad','clave_operador']))
            <div class="mt-3">
                <a href="{{ route('admin.movimientos.index') }}" class="btn btn-sm btn-secondary">Limpiar filtros</a>
            </div>
        @endif
    </div>
</div>

<!-- Tabla de movimientos -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Unidad</th>
                        <th>Operador</th>
                        <th>Departamento</th>
                        <th>Tipo</th>
                        <th>Fecha/Hora</th>
                        <th>Observaciones</th>
                        <th>Sincronizado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                    @php
                        // Obtener operador en la fecha del movimiento (usamos el mismo método del controlador)
                        $operador = \Illuminate\Support\Facades\DB::table('asignacion_operador_unidad as a')
                            ->join('operadores as o', 'o.id', '=', 'a.operador_id')
                            ->where('a.unidad_id', $mov->unidad_id)
                            ->where('a.fecha_inicio', '<=', $mov->fecha_hora)
                            ->where(function ($q) use ($mov) {
                                $q->whereNull('a.fecha_fin')
                                  ->orWhere('a.fecha_fin', '>=', $mov->fecha_hora);
                            })
                            ->select('o.nombre_completo', 'o.clave_operador')
                            ->first();
                    @endphp
                    <tr>
                        <td>{{ $mov->id }}</td>
                        <td>{{ $mov->unidad->numero_economico ?? 'N/A' }}</td>
                        <td>
                            @if($operador)
                                {{ $operador->nombre_completo }}<br>
                                <small class="text-muted">{{ $operador->clave_operador }}</small>
                            @else
                                <span class="text-muted">No asignado</span>
                            @endif
                        </td>
                        <td>{{ $mov->departamento->nombre ?? 'N/A' }}</td>
                        <td>
                            @if($mov->tipo == 'entrada')
                                <span class="badge bg-success"><i class="fas fa-sign-in-alt"></i> Entrada</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-sign-out-alt"></i> Salida</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($mov->fecha_hora)->format('d/m/Y H:i') }}</td>
                        <td>{{ Str::limit($mov->observaciones, 50) }}</td>
                        <td>
                            @if($mov->sincronizado)
                                <span class="badge bg-info">Sincronizado</span>
                            @else
                                <span class="badge bg-warning">Pendiente</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.movimientos.show', $mov) }}" class="btn btn-outline-info" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.movimientos.edit', $mov) }}" class="btn btn-outline-warning" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.movimientos.destroy', $mov) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este movimiento?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No hay movimientos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($movimientos->hasPages())
        <div class="card-footer">
            {{ $movimientos->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection