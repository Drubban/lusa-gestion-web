@extends('admin.layouts.app')

@section('content')
<style>
    /* ============================================
       ESTILOS POWER BI
    ============================================ */
    :root {
        --pbi-primary: #0d6efd;
        --pbi-success: #198754;
        --pbi-warning: #ffc107;
        --pbi-danger: #dc3545;
        --pbi-info: #0dcaf0;
        --pbi-dark: #212529;
        --pbi-gray: #6c757d;
        --pbi-light: #f8f9fa;
        --pbi-purple: #6f42c1;
        --pbi-teal: #20c997;
        --pbi-pink: #e83e8c;
    }

    .pbi-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.04);
        transition: all 0.2s ease;
        height: 100%;
        overflow: hidden;
    }

    .pbi-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }

    .pbi-card-header {
        padding: 0.75rem 1.25rem;
        background: var(--pbi-light);
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--pbi-gray);
    }

    .pbi-card-body {
        padding: 1.25rem;
    }

    .pbi-kpi {
        text-align: center;
        padding: 0.5rem 0;
    }

    .pbi-kpi-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--pbi-dark);
    }

    .pbi-kpi-value .trend-up { color: var(--pbi-success); font-size: 0.875rem; }
    .pbi-kpi-value .trend-down { color: var(--pbi-danger); font-size: 0.875rem; }

    .pbi-kpi-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--pbi-gray);
        margin-top: 0.25rem;
    }

    .pbi-kpi-icon {
        font-size: 1.5rem;
        color: var(--pbi-primary);
        opacity: 0.5;
        margin-bottom: 0.5rem;
    }

    .pbi-progress {
        height: 4px;
        border-radius: 2px;
        background: #e9ecef;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .pbi-progress-bar {
        height: 100%;
        border-radius: 2px;
        transition: width 1s ease;
    }

    .pbi-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .pbi-badge-success { background: #d1e7dd; color: #0a3622; }
    .pbi-badge-danger { background: #f8d7da; color: #58151c; }
    .pbi-badge-warning { background: #fff3cd; color: #664d03; }
    .pbi-badge-info { background: #cff4fc; color: #055160; }
    .pbi-badge-secondary { background: #e9ecef; color: #41464b; }
    .pbi-badge-primary { background: #cfe2ff; color: #084298; }
    .pbi-badge-purple { background: #e2d9f3; color: #3d1f6e; }

    .chart-container {
        background: white;
        border-radius: 0.75rem;
        padding: 1rem;
        border: 1px solid #e9ecef;
        margin-bottom: 1.5rem;
    }

    .chart-wrapper {
        height: 220px;
        position: relative;
    }

    .chart-wrapper-sm {
        height: 180px;
        position: relative;
    }

    .recent-list {
        max-height: 200px;
        overflow-y: auto;
    }

    .recent-list .list-group-item {
        border-left: 3px solid transparent;
        padding: 0.4rem 0.75rem;
        font-size: 0.8rem;
        border-color: #e9ecef;
    }

    .recent-list .list-group-item:hover {
        background-color: var(--pbi-light);
    }

    .recent-list .list-group-item .badge {
        font-weight: 500;
        font-size: 0.65rem;
    }

    .pbi-row {
        margin-bottom: 1.25rem;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .pbi-animate {
        animation: fadeInUp 0.5s ease forwards;
    }

    .pbi-delay-1 { animation-delay: 0.05s; }
    .pbi-delay-2 { animation-delay: 0.10s; }
    .pbi-delay-3 { animation-delay: 0.15s; }
    .pbi-delay-4 { animation-delay: 0.20s; }

    .text-purple { color: var(--pbi-purple); }
    .text-teal { color: var(--pbi-teal); }
    .text-pink { color: var(--pbi-pink); }
    .bg-purple { background-color: var(--pbi-purple); }
    .bg-teal { background-color: var(--pbi-teal); }

    .pbi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
    }

    .pbi-mini-card {
        background: var(--pbi-light);
        border-radius: 0.5rem;
        padding: 0.75rem;
        text-align: center;
    }

    .pbi-mini-card .value {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .pbi-mini-card .label {
        font-size: 0.65rem;
        text-transform: uppercase;
        color: var(--pbi-gray);
        letter-spacing: 0.5px;
    }
</style>

<div class="container-fluid px-4">
    <!-- ============================================ -->
    <!-- HEADER -->
    <!-- ============================================ -->
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-0">
                <i class="fas fa-chart-pie me-2 text-primary"></i>Dashboard Lusa
            </h1>
            <p class="text-muted small">Panel de control y análisis de datos</p>
        </div>
        <div>
            <span class="badge bg-success rounded-pill px-3 py-2">
                <i class="fas fa-sync-alt me-1"></i> {{ now()->format('d/m/Y H:i') }}
            </span>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- FILA 1: KPIs PRINCIPALES -->
    <!-- ============================================ -->
    <div class="row pbi-row">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card pbi-animate pbi-delay-1">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-icon text-primary"><i class="fas fa-bus"></i></div>
                    <div class="pbi-kpi-value">{{ $totalUnidades }}</div>
                    <div class="pbi-kpi-label">Unidades</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card pbi-animate pbi-delay-2">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-icon text-success"><i class="fas fa-users"></i></div>
                    <div class="pbi-kpi-value">{{ $totalOperadores }}</div>
                    <div class="pbi-kpi-label">Operadores</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card pbi-animate pbi-delay-3">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-icon text-warning"><i class="fas fa-clipboard-list"></i></div>
                    <div class="pbi-kpi-value">{{ $totalMantenimientos }}</div>
                    <div class="pbi-kpi-label">Mantenimientos</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card pbi-animate pbi-delay-4">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-icon text-info"><i class="fas fa-graduation-cap"></i></div>
                    <div class="pbi-kpi-value">{{ $totalCapacitaciones }}</div>
                    <div class="pbi-kpi-label">Capacitaciones</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card pbi-animate">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-icon text-danger"><i class="fas fa-boxes"></i></div>
                    <div class="pbi-kpi-value">{{ $totalInventario }}</div>
                    <div class="pbi-kpi-label">Inventario</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card pbi-animate">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-icon text-purple"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="pbi-kpi-value">{{ $totalAjustes }}</div>
                    <div class="pbi-kpi-label">Ajustes</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- FILA 2: KPIs SECUNDARIOS -->
    <!-- ============================================ -->
    <div class="row pbi-row">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-value text-success">{{ $unidadesConMantenimientoReciente }}</div>
                    <div class="pbi-kpi-label">Con Mantenimiento</div>
                    <div class="pbi-progress">
                        <div class="pbi-progress-bar" style="width: {{ $porcentajeUnidadesConMantenimiento }}%; background: var(--pbi-success);"></div>
                    </div>
                    <small class="text-muted">{{ $porcentajeUnidadesConMantenimiento }}%</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-value text-danger">{{ $unidadesSinMantenimiento }}</div>
                    <div class="pbi-kpi-label">Sin Mantenimiento</div>
                    <div class="pbi-progress">
                        <div class="pbi-progress-bar" style="width: {{ $porcentajeSinMantenimiento }}%; background: var(--pbi-danger);"></div>
                    </div>
                    <small class="text-muted">{{ $porcentajeSinMantenimiento }}%</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-value text-primary">{{ $promedioDiasMantenimiento ?? 'N/A' }}</div>
                    <div class="pbi-kpi-label">Días Promedio</div>
                    <small class="text-muted">entre mantenimientos</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-value text-warning">${{ number_format($montoTotalAjustes, 0) }}</div>
                    <div class="pbi-kpi-label">Monto Total Ajustes</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-value text-info">{{ $agendamientosPendientes }}</div>
                    <div class="pbi-kpi-label">Agendamientos Pendientes</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-body text-center">
                    <div class="pbi-kpi-value text-danger">{{ $agendamientosVencidos }}</div>
                    <div class="pbi-kpi-label">Agendamientos Vencidos</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- FILA 3: GRAFICOS PRINCIPALES -->
    <!-- ============================================ -->
    <div class="row pbi-row">
        <div class="col-xl-8">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i>Movimientos (Últimos 7 días)</h6>
                    <span class="badge bg-secondary rounded-pill">+{{ array_sum($fechas) }} total</span>
                </div>
                <div class="chart-wrapper">
                    <canvas id="movimientosChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-container">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-pie me-2 text-success"></i>Movimientos por Depto</h6>
                <div class="chart-wrapper">
                    <canvas id="deptosChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- FILA 4: GRAFICOS SECUNDARIOS -->
    <!-- ============================================ -->
    <div class="row pbi-row">
        <div class="col-xl-4">
            <div class="chart-container">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2 text-warning"></i>Mantenimientos (6 meses)</h6>
                <div class="chart-wrapper">
                    <canvas id="docsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-container">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2 text-info"></i>Capacitaciones (6 meses)</h6>
                <div class="chart-wrapper">
                    <canvas id="capChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-container">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2 text-purple"></i>Ajustes por Mes</h6>
                <div class="chart-wrapper">
                    <canvas id="ajustesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- FILA 5: TENDENCIAS E INVENTARIO -->
    <!-- ============================================ -->
    <div class="row pbi-row">
        <div class="col-xl-6">
            <div class="chart-container">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-line me-2 text-danger"></i>Tendencia de Mantenimientos (12 semanas)</h6>
                <div class="chart-wrapper">
                    <canvas id="tendenciaMantenimientoChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="chart-container">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2 text-primary"></i>Inventario por Categoría</h6>
                <div class="chart-wrapper">
                    <canvas id="inventarioChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- FILA 6: ESTADO DE MANTENIMIENTO -->
    <!-- ============================================ -->
    <div class="row pbi-row">
        <div class="col-xl-6">
            <div class="chart-container">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-doughnut me-2 text-warning"></i>Estado de Mantenimiento</h6>
                <div class="chart-wrapper">
                    <canvas id="estadosMantenimientoChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="chart-container">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2 text-success"></i>Inventario por Departamento</h6>
                <div class="chart-wrapper">
                    <canvas id="inventarioDeptoChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- FILA 7: REGISTROS RECIENTES (TODAS LAS CATEGORÍAS) -->
    <!-- ============================================ -->
    <div class="row pbi-row">
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-header">
                    <i class="fas fa-clipboard-list me-2 text-warning"></i>Últimos Mantenimientos
                </div>
                <div class="pbi-card-body p-0">
                    <div class="recent-list">
                        <ul class="list-group list-group-flush">
                            @forelse($ultimosMantenimientos as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">{{ $item->asignacion->unidad->numero_economico ?? 'N/A' }}</span>
                                    <span class="text-muted ms-1 small">{{ $item->tipo_mantenimiento ?? 'Sin tipo' }}</span>
                                </div>
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </li>
                            @empty
                            <li class="list-group-item text-muted text-center">Sin registros</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-header">
                    <i class="fas fa-graduation-cap me-2 text-info"></i>Últimas Capacitaciones
                </div>
                <div class="pbi-card-body p-0">
                    <div class="recent-list">
                        <ul class="list-group list-group-flush">
                            @forelse($ultimasCapacitaciones as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">{{ $item->asignacion->operador->nombre_completo ?? 'N/A' }}</span>
                                    <span class="text-muted ms-1 small">{{ $item->asignacion->unidad->numero_economico ?? '' }}</span>
                                </div>
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </li>
                            @empty
                            <li class="list-group-item text-muted text-center">Sin registros</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-header">
                    <i class="fas fa-boxes me-2 text-danger"></i>Últimos Inventarios
                </div>
                <div class="pbi-card-body p-0">
                    <div class="recent-list">
                        <ul class="list-group list-group-flush">
                            @forelse($ultimosInventarios as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">{{ $item->nombre_recibe }}</span>
                                    <span class="pbi-badge pbi-badge-info ms-1">{{ $item->nombre_categoria }}</span>
                                </div>
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </li>
                            @empty
                            <li class="list-group-item text-muted text-center">Sin registros</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-header">
                    <i class="fas fa-file-invoice-dollar me-2 text-purple"></i>Últimos Ajustes
                </div>
                <div class="pbi-card-body p-0">
                    <div class="recent-list">
                        <ul class="list-group list-group-flush">
                            @forelse($ultimosAjustes as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">{{ $item->folio }}</span>
                                    <span class="text-muted ms-1">${{ number_format($item->monto_total, 2) }}</span>
                                    <span class="pbi-badge {{ $item->firmado ? 'pbi-badge-success' : 'pbi-badge-warning' }} ms-1">
                                        {{ $item->firmado ? 'Firmado' : 'Pendiente' }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </li>
                            @empty
                            <li class="list-group-item text-muted text-center">Sin registros</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-header">
                    <i class="fas fa-exchange-alt me-2 text-primary"></i>Últimos Movimientos
                </div>
                <div class="pbi-card-body p-0">
                    <div class="recent-list">
                        <ul class="list-group list-group-flush">
                            @forelse($ultimosMovimientos as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">{{ $item->departamento->nombre ?? 'N/A' }}</span>
                                    <span class="pbi-badge {{ $item->tipo == 'entrada' ? 'pbi-badge-success' : 'pbi-badge-danger' }} ms-1">
                                        {{ ucfirst($item->tipo ?? '') }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </li>
                            @empty
                            <li class="list-group-item text-muted text-center">Sin registros</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="pbi-card">
                <div class="pbi-card-header">
                    <i class="fas fa-calendar-check me-2 text-info"></i>Últimos Agendamientos
                </div>
                <div class="pbi-card-body p-0">
                    <div class="recent-list">
                        <ul class="list-group list-group-flush">
                            @forelse($ultimosAgendamientos as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">{{ $item->unidad->numero_economico ?? 'N/A' }}</span>
                                    <span class="pbi-badge {{ $item->estado == 'pendiente' ? 'pbi-badge-warning' : 'pbi-badge-success' }} ms-1">
                                        {{ ucfirst($item->estado) }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </li>
                            @empty
                            <li class="list-group-item text-muted text-center">Sin registros</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============================================
        // DATOS
        // ============================================
        const fechas = @json(array_keys($fechas));
        const valores = @json(array_values($fechas));
        const deptosNombres = @json(array_column($movimientosPorDepto, 'nombre'));
        const deptosTotales = @json(array_column($movimientosPorDepto, 'total'));
        const meses = @json(array_column($docPorMes, 'mes'));
        const totalDocs = @json(array_column($docPorMes, 'total'));
        const capMeses = @json(array_column($capPorMes, 'mes'));
        const totalCaps = @json(array_column($capPorMes, 'total'));
        const ajustesMeses = @json(array_column($ajustesPorMes, 'mes'));
        const ajustesTotales = @json(array_column($ajustesPorMes, 'total'));

        const inventarioCategorias = @json(array_column($inventarioPorCategoria, 'categoria'));
        const inventarioTotales = @json(array_column($inventarioPorCategoria, 'total'));
        const inventarioDeptoNombres = @json(array_column($inventarioPorDepto, 'nombre'));
        const inventarioDeptoTotales = @json(array_column($inventarioPorDepto, 'total'));

        const estadosMantenimientoLabels = @json(array_keys($estadosMantenimiento));
        const estadosMantenimientoValues = @json(array_values($estadosMantenimiento));

        const tendenciaMantenimientoLabels = @json(array_column($tendenciaMantenimientos, 'semana'));
        const tendenciaMantenimientoValues = @json(array_column($tendenciaMantenimientos, 'total'));

        const colores = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c'];
        const coloresPastel = ['#cfe2ff', '#d1e7dd', '#fff3cd', '#f8d7da', '#e2d9f3', '#cff4fc', '#d1f2eb', '#f5c2d3'];

        // ============================================
        // GRAFICO: MOVIMIENTOS
        // ============================================
        new Chart(document.getElementById('movimientosChart'), {
            type: 'line',
            data: {
                labels: fechas.map(f => new Date(f).toLocaleDateString('es-ES', { day: '2-digit', month: 'short' })),
                datasets: [{
                    label: 'Movimientos',
                    data: valores,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#0d6efd',
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // ============================================
        // GRAFICO: DEPARTAMENTOS (Dona)
        // ============================================
        new Chart(document.getElementById('deptosChart'), {
            type: 'doughnut',
            data: {
                labels: deptosNombres,
                datasets: [{
                    data: deptosTotales,
                    backgroundColor: colores.slice(0, deptosNombres.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } },
                cutout: '65%'
            }
        });

        // ============================================
        // GRAFICO: MANTENIMIENTOS (Barras)
        // ============================================
        new Chart(document.getElementById('docsChart'), {
            type: 'bar',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Mantenimientos',
                    data: totalDocs,
                    backgroundColor: '#ffc107',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // ============================================
        // GRAFICO: CAPACITACIONES (Barras)
        // ============================================
        new Chart(document.getElementById('capChart'), {
            type: 'bar',
            data: {
                labels: capMeses,
                datasets: [{
                    label: 'Capacitaciones',
                    data: totalCaps,
                    backgroundColor: '#0dcaf0',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // ============================================
        // GRAFICO: AJUSTES (Barras)
        // ============================================
        new Chart(document.getElementById('ajustesChart'), {
            type: 'bar',
            data: {
                labels: ajustesMeses,
                datasets: [{
                    label: 'Ajustes',
                    data: ajustesTotales,
                    backgroundColor: '#6f42c1',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // ============================================
        // GRAFICO: TENDENCIA MANTENIMIENTOS
        // ============================================
        new Chart(document.getElementById('tendenciaMantenimientoChart'), {
            type: 'line',
            data: {
                labels: tendenciaMantenimientoLabels,
                datasets: [{
                    label: 'Mantenimientos',
                    data: tendenciaMantenimientoValues,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220,53,69,0.1)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#dc3545',
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // ============================================
        // GRAFICO: INVENTARIO POR CATEGORIA
        // ============================================
        const inventarioLabels = inventarioCategorias.map(c => {
            const map = {
                'equipos_computo': 'Computo',
                'routers_switches': 'Routers/Switches',
                'telefonia': 'Telefonia',
                'consumibles': 'Consumibles',
                'perifericos': 'Perifericos',
                'tarjetas': 'Tarjetas'
            };
            return map[c] || c;
        });
        new Chart(document.getElementById('inventarioChart'), {
            type: 'bar',
            data: {
                labels: inventarioLabels,
                datasets: [{
                    label: 'Registros',
                    data: inventarioTotales,
                    backgroundColor: colores.slice(0, inventarioLabels.length),
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // ============================================
        // GRAFICO: ESTADOS DE MANTENIMIENTO
        // ============================================
        new Chart(document.getElementById('estadosMantenimientoChart'), {
            type: 'doughnut',
            data: {
                labels: estadosMantenimientoLabels,
                datasets: [{
                    data: estadosMantenimientoValues,
                    backgroundColor: ['#198754', '#ffc107', '#fd7e14', '#dc3545', '#6c757d'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 9 } } } },
                cutout: '65%'
            }
        });

        // ============================================
        // GRAFICO: INVENTARIO POR DEPARTAMENTO
        // ============================================
        new Chart(document.getElementById('inventarioDeptoChart'), {
            type: 'bar',
            data: {
                labels: inventarioDeptoNombres,
                datasets: [{
                    label: 'Registros',
                    data: inventarioDeptoTotales,
                    backgroundColor: '#20c997',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    });
</script>
@endsection