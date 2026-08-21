@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Detalle de Tecnología</h1>
        <div>
            <a href="{{ route('admin.tecnologias.edit', $tecnologia->id) }}" class="btn btn-warning rounded-pill px-4">
                <i class="fas fa-edit me-2"></i> Editar
            </a>
            <a href="{{ route('admin.tecnologias.index') }}" class="btn btn-secondary rounded-pill px-4">
                Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Unidad</strong>
                            </div>
                            <p class="fs-5">{{ $tecnologia->unidad->numero_economico ?? 'N/A' }}</p>
                            <small>{{ $tecnologia->unidad->nombre_unidad ?? '' }}</small>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Tipo</strong>
                            </div>
                            <p><span class="badge bg-primary fs-6">{{ $tecnologia->tipo_nombre }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Nombre</strong>
                            </div>
                            <p>{{ $tecnologia->nombre ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Estado</strong>
                            </div>
                            <p>
                                @if($tecnologia->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-12">
                            <hr class="border-2 border-secondary">
                            <h5><i class="fas fa-microchip me-2"></i>Datos Específicos</h5>
                        </div>

                        @php $datos = $tecnologia->datos; @endphp

                        @if($tecnologia->tipo == 'barras' && $datos)
                            <div class="col-md-3"><strong>ID Barra:</strong> {{ $datos['id_barra'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>Barras:</strong> {{ $datos['barras'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>Teléfono:</strong> {{ $datos['telefono'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>Plan:</strong> {{ $datos['plan'] ?? 'N/A' }}</div>
                        @elseif($tecnologia->tipo == 'telpo' && $datos)
                            <div class="col-md-3"><strong>IMEI Antes:</strong> {{ $datos['imei_antes'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>V. APK:</strong> {{ $datos['v_apk'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>Telpo:</strong> {{ $datos['telpo'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>IMEI Telpo:</strong> {{ $datos['imei_telpo'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>Teléfono:</strong> {{ $datos['telefono'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>Plan:</strong> {{ $datos['plan'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>Costo Plan:</strong> ${{ number_format($datos['costo_plan'] ?? 0, 2) }}</div>
                        @elseif($tecnologia->tipo == 'gps' && $datos)
                            <div class="col-md-4"><strong>IMEI GPS:</strong> {{ $datos['imei_gps'] ?? 'N/A' }}</div>
                            <div class="col-md-4"><strong>Teléfono:</strong> {{ $datos['telefono'] ?? 'N/A' }}</div>
                            <div class="col-md-4"><strong>Plan:</strong> {{ $datos['plan'] ?? 'N/A' }}</div>
                        @elseif($tecnologia->tipo == 'mdvr' && $datos)
                            <div class="col-md-3"><strong>DVR:</strong> {{ $datos['dvr'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>Modelo:</strong> {{ $datos['modelo'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>Cámaras:</strong> {{ $datos['camaras'] ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>Memoria:</strong> {{ $datos['memoria'] ?? 'N/A' }}</div>
                        @else
                            <div class="col-12 text-muted">No hay datos específicos para esta tecnología</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Información Adicional</h5>
                    <hr>
                    <div class="mb-3">
                        <strong class="text-muted">Registrado por</strong>
                        <p>{{ $tecnologia->creador->nombre_usuario ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Fecha de creación</strong>
                        <p>{{ $tecnologia->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Última actualización</strong>
                        <p>{{ $tecnologia->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">ID</strong>
                        <p><span class="badge bg-secondary">#{{ $tecnologia->id }}</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection