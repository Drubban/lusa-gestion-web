@extends('admin.layouts.app')

@section('content')
<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 1rem;
        border: none;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .chart-container {
        background: white;
        border-radius: 1rem;
        padding: 1rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
        overflow-x: auto;
    }

    .chart-wrapper {
        height: 280px;
        position: relative;
    }

    .filter-card {
        background: #f8f9fa;
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .kpi-card {
        border-radius: 1rem;
        border: none;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .kpi-card .kpi-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 3rem;
        opacity: 0.25;
    }

    .kpi-card .kpi-value {
        font-size: 2.2rem;
        font-weight: 700;
        line-height: 1;
    }

    .kpi-card .kpi-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.9;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
    }

    .bg-gradient-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #565e64 100%);
    }

    .table td {
        vertical-align: middle;
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">
            <i class="fas fa-chart-pie me-2"></i>Dashboard Revisiones Optocontrol
            <small class="text-muted fs-6">Analisis y gestion de revisiones</small>
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.revisiones-optocontrol.index') }}" class="btn btn-secondary rounded-pill px-4">
                <i class="fas fa-list me-2"></i>Ver revisiones
            </a>
            <a href="{{ route('admin.revisiones-optocontrol.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>Nueva revision
            </a>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white fw-bold">
            <i class="fas fa-filter me-2"></i>Filtros
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.revisiones-optocontrol.dashboard') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Dispositivo</label>
                    <select name="disp" class="form-select">
                        <option value="">Todos</option>
                        @foreach($disp_opciones as $opcion)
                            <option value="{{ $opcion }}" {{ request('disp') == $opcion ? 'selected' : '' }}>
                                {{ ucfirst($opcion) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        @foreach($status_opciones as $opcion)
                            <option value="{{ $opcion }}" {{ request('status') == $opcion ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $opcion)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Unidad, problema..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 w-100">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                    <a href="{{ route('admin.revisiones-optocontrol.dashboard') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPIs PRINCIPALES -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="kpi-card bg-gradient-primary p-3">
                <div class="kpi-label">Total</div>
                <div class="kpi-value">{{ $total }}</div>
                <i class="fas fa-clipboard-list kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="kpi-card bg-gradient-warning p-3 text-dark">
                <div class="kpi-label">Pendientes</div>
                <div class="kpi-value">{{ $pendientes }}</div>
                <i class="fas fa-hourglass-half kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="kpi-card bg-gradient-info p-3 text-dark">
                <div class="kpi-label">En Proceso</div>
                <div class="kpi-value">{{ $en_proceso }}</div>
                <i class="fas fa-spinner kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="kpi-card bg-gradient-success p-3">
                <div class="kpi-label">Resueltos</div>
                <div class="kpi-value">{{ $resueltos }}</div>
                <i class="fas fa-check-circle kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="kpi-card bg-gradient-danger p-3">
                <div class="kpi-label">Cancelados</div>
                <div class="kpi-value">{{ $cancelados }}</div>
                <i class="fas fa-times-circle kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="kpi-card bg-gradient-secondary p-3">
                <div class="kpi-label">Ult. 30 dias</div>
                <div class="kpi-value">{{ $ultimos30 }}</div>
                <i class="fas fa-calendar-alt kpi-icon"></i>
            </div>
        </div>
    </div>

    <!-- GRAFICOS -->
    <div class="row">
        <div class="col-xl-4">
            <div class="chart-container">
                <h5 class="mb-3">Distribucion por Dispositivo</h5>
                <div class="chart-wrapper">
                    <canvas id="chartDisp"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-container">
                <h5 class="mb-3">Distribucion por Estado</h5>
                <div class="chart-wrapper">
                    <canvas id="chartStatus"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-container">
                <h5 class="mb-3">Tendencia (Ultimos 30 dias)</h5>
                <div class="chart-wrapper">
                    <canvas id="chartTendencia"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="chart-container">
                <h5 class="mb-3">Revisiones por Ruta/Zona</h5>
                <div class="chart-wrapper">
                    <canvas id="chartRutas"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="chart-container">
                <h5 class="mb-3">Top 5 Unidades con mas Revisiones</h5>
                <div class="chart-wrapper">
                    <canvas id="chartUnidades"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLA ULTIMAS REVISIONES -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-clock me-2"></i>Ultimas 10 Revisiones</span>
            <a href="{{ route('admin.revisiones-optocontrol.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                Ver todas <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Dispositivo</th>
                            <th>Unidad</th>
                            <th>Ruta</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Tiempo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recientes as $revision)
                        <tr>
                            <td><strong>#{{ $revision->id }}</strong></td>
                            <td>
                                <span class="badge bg-primary">{{ $revision->disp_label }}</span>
                            </td>
                            <td>
                                <strong>{{ $revision->unidad->numero_economico ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $revision->nombre_unidad }}</small>
                            </td>
                            <td>{{ $revision->ruta ?? 'N/A' }}</td>
                            <td>{{ $revision->fecha_reporte->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $revision->status_color }} rounded-pill px-3">
                                    {{ $revision->status_label }}
                                </span>
                            </td>
                            <td>
                                @if($revision->tiempo_estancia)
                                    <span class="fw-bold">{{ $revision->tiempo_estancia }} hrs</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.revisiones-optocontrol.show', $revision->id) }}"
                                   class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-2x d-block mb-2 text-muted"></i>
                                No hay revisiones registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colores = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#0dcaf0'];

        // Grafico 1: DISPOSITIVOS
        new Chart(document.getElementById('chartDisp'), {
            type: 'doughnut',
            data: {
                labels: @json($labels_disp),
                datasets: [{
                    data: @json(array_values($distribucion_disp)),
                    backgroundColor: colores,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            padding: 12,
                            font: { size: 12, weight: '500' },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Grafico 2: ESTADOS
        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: @json($labels_status),
                datasets: [{
                    data: @json(array_values($distribucion_status)),
                    backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            padding: 12,
                            font: { size: 12, weight: '500' },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Grafico 3: TENDENCIA
        new Chart(document.getElementById('chartTendencia'), {
            type: 'line',
            data: {
                labels: @json($tendencia_labels),
                datasets: [{
                    label: 'Revisiones',
                    data: @json($tendencia_data),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#0d6efd',
                    pointRadius: 4,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // Grafico 4: RUTAS
        new Chart(document.getElementById('chartRutas'), {
            type: 'bar',
            data: {
                labels: @json($rutas_labels),
                datasets: [{
                    label: 'Revisiones',
                    data: @json($rutas_data),
                    backgroundColor: colores,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // Grafico 5: TOP UNIDADES
        new Chart(document.getElementById('chartUnidades'), {
            type: 'bar',
            data: {
                labels: @json($unidades_labels),
                datasets: [{
                    label: 'Revisiones',
                    data: @json($unidades_data),
                    backgroundColor: '#6f42c1',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
</script>
@endsection