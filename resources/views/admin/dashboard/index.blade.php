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
        height: 320px;
        position: relative;
    }

    canvas {
        max-height: 100%;
        width: auto !important;
    }

    .recent-list {
        max-height: 250px;
        overflow-y: auto;
    }

    .recent-list .list-group-item {
        border-left: 3px solid transparent;
    }

    .recent-list .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .badge-equipo {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
</style>

<div class="container-fluid px-4" style="overflow-y: auto; max-height: calc(100vh - 70px);">
    <h1 class="mt-4">Panel de Administracion</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Bienvenido al sistema Lusa</li>
    </ol>

    <!-- Fila 1: Tarjetas principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Unidades</h5>
                        <h2 class="mb-0">{{ $totalUnidades }}</h2>
                    </div>
                    <div class="icon-circle bg-white text-primary">
                        <i class="fas fa-bus"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Operadores</h5>
                        <h2 class="mb-0">{{ $totalOperadores }}</h2>
                    </div>
                    <div class="icon-circle bg-white text-success">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Mantenimientos</h5>
                        <h2 class="mb-0">{{ $totalMantenimientos }}</h2>
                    </div>
                    <div class="icon-circle bg-white text-warning">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Capacitaciones</h5>
                        <h2 class="mb-0">{{ $totalCapacitaciones }}</h2>
                    </div>
                    <div class="icon-circle bg-white text-info">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Tarjetas de nuevos modulos -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-danger text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Inventario</h5>
                        <h2 class="mb-0">{{ $totalInventario }}</h2>
                    </div>
                    <div class="icon-circle bg-white text-danger">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-secondary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Ajustes</h5>
                        <h2 class="mb-0">{{ $totalAjustes }}</h2>
                    </div>
                    <div class="icon-circle bg-white text-secondary">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-dark text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Tecnologias</h5>
                        <h2 class="mb-0">{{ $totalTecnologias }}</h2>
                    </div>
                    <div class="icon-circle bg-white text-dark">
                        <i class="fas fa-microchip"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-purple text-white" style="background-color: #6f42c1;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Asignaciones</h5>
                        <h2 class="mb-0">{{ $asignacionesVigentes }}</h2>
                    </div>
                    <div class="icon-circle bg-white text-purple" style="color: #6f42c1;">
                        <i class="fas fa-handshake"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tecnologias Asignadas (barras, telpo, gps, mdvr) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white fw-bold text-success">
                    <i class="fas fa-microchip me-2"></i>Tecnologias Asignadas
                    <small class="text-muted">(barras, telpo, gps, mdvr)</small>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3 border rounded-3">
                                <h5 class="text-info">Barras</h5>
                                <h3 class="mb-0">{{ $unidadesConBarras }}</h3>
                                <small class="text-muted">unidades con Barras</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded-3">
                                <h5 class="text-info">Telpo</h5>
                                <h3 class="mb-0">{{ $unidadesConTelpo }}</h3>
                                <small class="text-muted">unidades con Telpo</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded-3">
                                <h5 class="text-info">GPS</h5>
                                <h3 class="mb-0">{{ $unidadesConGps }}</h3>
                                <small class="text-muted">unidades con GPS</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded-3">
                                <h5 class="text-info">MDVR</h5>
                                <h3 class="mb-0">{{ $unidadesConMdvr }}</h3>
                                <small class="text-muted">unidades con MDVR</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 4: Graficos principales -->
    <div class="row">
        <div class="col-xl-8">
            <div class="chart-container">
                <h5 class="mb-3">Movimientos de unidades (ultimos 7 dias)</h5>
                <div class="chart-wrapper">
                    <canvas id="movimientosChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-container">
                <h5 class="mb-3">Movimientos por departamento</h5>
                <div class="chart-wrapper">
                    <canvas id="deptosChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 5: Graficos secundarios -->
    <div class="row">
        <div class="col-xl-6">
            <div class="chart-container">
                <h5 class="mb-3">Documentos de mantenimiento (ultimos 6 meses)</h5>
                <div class="chart-wrapper">
                    <canvas id="docsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="chart-container">
                <h5 class="mb-3">Tecnologias por tipo</h5>
                <div class="chart-wrapper">
                    <canvas id="tecnologiasChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 6: Graficos de inventario y ajustes -->
    <div class="row">
        <div class="col-xl-6">
            <div class="chart-container">
                <h5 class="mb-3">Inventario por categoria</h5>
                <div class="chart-wrapper">
                    <canvas id="inventarioChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="chart-container">
                <h5 class="mb-3">Ajustes por mes</h5>
                <div class="chart-wrapper">
                    <canvas id="ajustesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 7: Registros recientes -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-clock me-2"></i>Ultimos Mantenimientos
                </div>
                <div class="card-body recent-list p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($ultimosMantenimientos as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">{{ $item->asignacion->unidad->numero_economico ?? 'N/A' }}</span>
                                <span class="text-muted ms-2">{{ $item->tipo_mantenimiento ?? 'Sin tipo' }}</span>
                            </div>
                            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                        </li>
                        @empty
                        <li class="list-group-item text-muted text-center">Sin mantenimientos recientes</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-clock me-2"></i>Ultimos Inventarios
                </div>
                <div class="card-body recent-list p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($ultimosInventarios as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">{{ $item->nombre_recibe }}</span>
                                <span class="badge bg-info ms-2">{{ $item->nombre_categoria }}</span>
                            </div>
                            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                        </li>
                        @empty
                        <li class="list-group-item text-muted text-center">Sin inventarios recientes</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 8: Ajustes recientes -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-clock me-2"></i>Ultimos Ajustes
                </div>
                <div class="card-body recent-list p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($ultimosAjustes as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">{{ $item->folio }}</span>
                                <span class="text-muted ms-2">${{ number_format($item->monto_total, 2) }}</span>
                                <span class="badge {{ $item->firmado ? 'bg-success' : 'bg-warning' }} ms-2">
                                    {{ $item->firmado ? 'Firmado' : 'Pendiente' }}
                                </span>
                            </div>
                            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                        </li>
                        @empty
                        <li class="list-group-item text-muted text-center">Sin ajustes recientes</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-clock me-2"></i>Ultimas Capacitaciones
                </div>
                <div class="card-body recent-list p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($ultimasCapacitaciones as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">{{ $item->asignacion->operador->nombre_completo ?? 'N/A' }}</span>
                                <span class="text-muted ms-2">{{ $item->asignacion->unidad->numero_economico ?? 'N/A' }}</span>
                            </div>
                            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                        </li>
                        @empty
                        <li class="list-group-item text-muted text-center">Sin capacitaciones recientes</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos desde Laravel
        const fechas = @json(array_keys($fechas));
        const valores = @json(array_values($fechas));
        const deptosNombres = @json(array_column($movimientosPorDepto, 'nombre'));
        const deptosTotales = @json(array_column($movimientosPorDepto, 'total'));
        const meses = @json(array_column($docPorMes, 'mes'));
        const totalDocs = @json(array_column($docPorMes, 'total'));

        // Datos de inventario
        const inventarioCategorias = @json(array_column($inventarioPorCategoria, 'categoria'));
        const inventarioTotales = @json(array_column($inventarioPorCategoria, 'total'));

        // Datos de tecnologias
        const tecnologiasTipos = @json(array_column($tecnologiasPorTipo, 'tipo'));
        const tecnologiasTotales = @json(array_column($tecnologiasPorTipo, 'total'));

        // Datos de ajustes
        const ajustesMeses = @json(array_column($ajustesPorMes, 'mes'));
        const ajustesTotales = @json(array_column($ajustesPorMes, 'total'));

        // Colores predefinidos
        const colores = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c'];

        // Grafico de lineas - Movimientos
        const ctxMov = document.getElementById('movimientosChart').getContext('2d');
        new Chart(ctxMov, {
            type: 'line',
            data: {
                labels: fechas.map(f => new Date(f).toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: 'short'
                })),
                datasets: [{
                    label: 'Movimientos',
                    data: valores,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Grafico de dona - Departamentos
        const ctxDept = document.getElementById('deptosChart').getContext('2d');
        new Chart(ctxDept, {
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
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Grafico de barras - Documentos
        const ctxDocs = document.getElementById('docsChart').getContext('2d');
        new Chart(ctxDocs, {
            type: 'bar',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Documentos de mantenimiento',
                    data: totalDocs,
                    backgroundColor: '#ffc107',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Grafico de dona - Tecnologias
        const ctxTec = document.getElementById('tecnologiasChart').getContext('2d');
        new Chart(ctxTec, {
            type: 'doughnut',
            data: {
                labels: tecnologiasTipos.map(t => t.charAt(0).toUpperCase() + t.slice(1)),
                datasets: [{
                    data: tecnologiasTotales,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Grafico de barras - Inventario
        const ctxInv = document.getElementById('inventarioChart').getContext('2d');
        const inventarioLabels = inventarioCategorias.map(c => {
            const map = {
                'equipos_computo': 'Equipos Computo',
                'routers_switches': 'Routers/Switches',
                'telefonia': 'Telefonia',
                'consumibles': 'Consumibles',
                'perifericos': 'Perifericos',
                'tarjetas': 'Tarjetas'
            };
            return map[c] || c;
        });
        new Chart(ctxInv, {
            type: 'bar',
            data: {
                labels: inventarioLabels,
                datasets: [{
                    label: 'Registros de inventario',
                    data: inventarioTotales,
                    backgroundColor: '#0d6efd',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Grafico de barras - Ajustes
        const ctxAju = document.getElementById('ajustesChart').getContext('2d');
        new Chart(ctxAju, {
            type: 'bar',
            data: {
                labels: ajustesMeses,
                datasets: [{
                    label: 'Ajustes registrados',
                    data: ajustesTotales,
                    backgroundColor: '#6f42c1',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endsection