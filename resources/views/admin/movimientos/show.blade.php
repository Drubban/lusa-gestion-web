@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1>Detalle del Movimiento #{{ $movimiento->id }}</h1>
    <div>
        <a href="{{ route('admin.movimientos.edit', $movimiento) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('admin.movimientos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <i class="fas fa-info-circle"></i> Información general
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th style="width: 180px;">ID:</th><td>{{ $movimiento->id }}</td></tr>
                    <tr><th>Unidad:</th><td>{{ $movimiento->unidad->numero_economico ?? 'N/A' }} - {{ $movimiento->unidad->nombre_unidad ?? '' }}</td></tr>
                    <tr><th>Operador (en fecha):</th>
                        <td>
                            @if($operador)
                                {{ $operador->nombre_completo }} (clave: {{ $operador->clave_operador }})
                            @else
                                <span class="text-muted">No asignado</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Departamento:</th><td>{{ $movimiento->departamento->nombre ?? 'N/A' }}</td></tr>
                    <tr><th>Tipo:</th>
                        <td>
                            @if($movimiento->tipo == 'entrada')
                                <span class="badge bg-success">Entrada</span>
                            @else
                                <span class="badge bg-danger">Salida</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Fecha y hora:</th><td>{{ \Carbon\Carbon::parse($movimiento->fecha_hora)->format('d/m/Y H:i:s') }}</td></tr>
                    <tr><th>Observaciones:</th><td>{{ $movimiento->observaciones ?? 'Ninguna' }}</td></tr>
                    <tr><th>Sincronizado:</th>
                        <td>
                            @if($movimiento->sincronizado)
                                <span class="badge bg-success">Sí</span>
                            @else
                                <span class="badge bg-warning">No (pendiente desde app)</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Registrado por:</th><td>{{ $movimiento->usuario->nombre_usuario ?? 'Sistema' }} ({{ $movimiento->usuario->departamento->nombre ?? '' }})</td></tr>
                    <tr><th>Fecha de creación:</th><td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td></tr>
                    <tr><th>Última actualización:</th><td>{{ $movimiento->updated_at->format('d/m/Y H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <i class="fas fa-map-marked-alt"></i> Contexto del movimiento
            </div>
            <div class="card-body">
                <p>Este movimiento fue registrado en el departamento de <strong>{{ $movimiento->departamento->nombre ?? '' }}</strong>.</p>
                <p>La unidad {{ $movimiento->unidad->numero_economico ?? '' }} se encontraba en estado de <strong>{{ $movimiento->tipo == 'entrada' ? 'ingreso' : 'salida' }}</strong>.</p>
                <hr>
                <h6>Historial de la unidad</h6>
                <ul>
                    <li>Últimos 3 movimientos de esta unidad:</li>
                    @php
                        $ultimos = \App\Models\MovimientoDepartamento::where('unidad_id', $movimiento->unidad_id)
                                    ->where('id', '!=', $movimiento->id)
                                    ->orderBy('fecha_hora', 'desc')
                                    ->limit(3)
                                    ->get();
                    @endphp
                    @forelse($ultimos as $ult)
                        <li>{{ $ult->tipo }} - {{ $ult->departamento->nombre }} el {{ $ult->fecha_hora->format('d/m/Y H:i') }}</li>
                    @empty
                        <li>No hay otros movimientos previos.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection