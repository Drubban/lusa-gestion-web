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
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
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
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        margin-bottom: 1.5rem;
        /* Evitar expansión excesiva */
        overflow-x: auto;
    }
    /* Altura fija para los contenedores de canvas */
    .chart-wrapper {
        height: 320px;
        position: relative;
    }
    canvas {
        max-height: 100%;
        width: auto !important;
    }
</style>

<div class="container-fluid px-4" style="overflow-y: auto; max-height: calc(100vh - 70px);">
    <h1 class="mt-4">Panel de Administración</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Bienvenido al sistema Lusa</li>
    </ol>

    <!-- Tarjetas de estadísticas -->
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

    <!-- Gráficas -->
    <div class="row">
        <div class="col-xl-8">
            <div class="chart-container">
                <h5 class="mb-3">Movimientos de unidades (últimos 7 días)</h5>
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

    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <h5 class="mb-3">Documentos de mantenimiento (últimos 6 meses)</h5>
                <div class="chart-wrapper">
                    <canvas id="docsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fechas = @json(array_keys($fechas));
        const valores = @json(array_values($fechas));
        const deptosNombres = @json(array_column($movimientosPorDepto, 'nombre'));
        const deptosTotales = @json(array_column($movimientosPorDepto, 'total'));
        const meses = @json(array_column($docPorMes, 'mes'));
        const totalDocs = @json(array_column($docPorMes, 'total'));

        // Gráfico de líneas
        const ctxMov = document.getElementById('movimientosChart').getContext('2d');
        new Chart(ctxMov, {
            type: 'line',
            data: {
                labels: fechas.map(f => new Date(f).toLocaleDateString('es-ES', {day:'2-digit', month:'short'})),
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
                plugins: { legend: { position: 'top' } }
            }
        });

        // Gráfico de dona
        const ctxDept = document.getElementById('deptosChart').getContext('2d');
        new Chart(ctxDept, {
            type: 'doughnut',
            data: {
                labels: deptosNombres,
                datasets: [{
                    data: deptosTotales,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Gráfico de barras
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
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    });
</script>
@endsection