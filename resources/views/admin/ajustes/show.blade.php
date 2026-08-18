@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Detalle de Ajuste</h1>
        <div>
            <a href="{{ route('admin.ajustes.edit', $ajuste->id) }}" class="btn btn-warning rounded-pill px-4">
                <i class="fas fa-edit me-2"></i> Editar
            </a>
            <a href="{{ route('admin.ajustes.index') }}" class="btn btn-secondary rounded-pill px-4">
                Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Folio</strong>
                            </div>
                            <p class="fs-5 fw-bold">{{ $ajuste->folio }}</p>
                        </div>
                        <div class="col-md-4">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Fecha</strong>
                            </div>
                            <p>{{ $ajuste->fecha->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Hora</strong>
                            </div>
                            <p>{{ $ajuste->hora }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Zona</strong>
                            </div>
                            <p>{{ $ajuste->zona ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Monto Total</strong>
                            </div>
                            <p class="fs-4 fw-bold text-success">${{ number_format($ajuste->monto_total, 2) }}</p>
                        </div>
                        <div class="col-md-12">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Estado</strong>
                            </div>
                            <p>
                                @if($ajuste->firmado)
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check me-1"></i> Firmado
                                    </span>
                                @else
                                    <span class="badge bg-warning fs-6 text-dark">
                                        <i class="fas fa-clock me-1"></i> Pendiente de firma
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title"><i class="fas fa-user me-2"></i>Datos del Operador</h5>
                    <hr>
                    <div class="mb-3">
                        <strong class="text-muted">Nombre</strong>
                        <p class="fs-6">{{ $ajuste->operador->nombre_completo ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Clave</strong>
                        <p><span class="badge bg-secondary">{{ $ajuste->clave_operador }}</span></p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="card-title"><i class="fas fa-truck me-2"></i>Datos de la Unidad</h5>
                    <hr>
                    <div class="mb-3">
                        <strong class="text-muted">Número Económico</strong>
                        <p class="fs-6">{{ $ajuste->unidad->numero_economico ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Nombre</strong>
                        <p>{{ $ajuste->unidad->nombre_unidad ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Información Adicional</h5>
                    <hr>
                    <div class="mb-3">
                        <strong class="text-muted">Registrado por</strong>
                        <p>{{ $ajuste->creador->nombre_usuario ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Fecha de creación</strong>
                        <p>{{ $ajuste->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Última actualización</strong>
                        <p>{{ $ajuste->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection