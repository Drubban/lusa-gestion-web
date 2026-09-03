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

    .bg-orange {
        background-color: #fd7e14;
    }

    .bg-purple {
        background-color: #6f42c1;
    }

    .bg-teal {
        background-color: #20c997;
    }

    .table td {
        vertical-align: middle;
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

    /* Estilos para el buscador */
    .search-container {
        background: white;
        border-radius: 1rem;
        padding: 0.75rem 1.25rem;
        border: 1px solid #e9ecef;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .search-container .search-icon {
        color: #6c757d;
        font-size: 1.1rem;
    }

    .search-container .search-input {
        flex: 1;
        min-width: 200px;
        border: none;
        outline: none;
        padding: 0.4rem 0;
        font-size: 0.9rem;
        background: transparent;
    }

    .search-container .search-input:focus {
        border-bottom: 2px solid #0d6efd;
    }

    .search-container .search-input::placeholder {
        color: #adb5bd;
    }

    .search-container .search-badge {
        background: #e9ecef;
        padding: 0.2rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        color: #6c757d;
    }

    .search-container .search-clear {
        cursor: pointer;
        color: #6c757d;
        padding: 0 0.5rem;
        display: none;
    }

    .search-container .search-clear:hover {
        color: #dc3545;
    }

    .search-container .search-clear.visible {
        display: inline;
    }

    @media (max-width: 576px) {
        .search-container {
            flex-direction: column;
            align-items: stretch;
        }
        .search-container .search-input {
            min-width: unset;
        }
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">
            <i class="fas fa-chart-pie me-2"></i>Dashboard de Mantenimiento
            <small class="text-muted fs-6">Analisis y gestion de mantenimientos</small>
        </h1>
        <div class="d-flex gap-2">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-success rounded-pill px-3 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-export me-1"></i> Exportar
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.exportar.mantenimientos.csv') }}" target="_blank">
                            <i class="fas fa-file-csv text-success me-2"></i> Exportar a CSV (Programables)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.exportar.mantenimientos.excel') }}" target="_blank">
                            <i class="fas fa-file-excel text-success me-2"></i> Exportar a Excel (Programables)
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.exportar.mantenimientos.todos') }}" target="_blank">
                            <i class="fas fa-file-archive text-secondary me-2"></i> Exportar Todo (Historico)
                        </a>
                    </li>
                </ul>
            </div>
            <a href="{{ route('admin.documentos-mantenimiento.index') }}" class="btn btn-secondary rounded-pill px-4">
                <i class="fas fa-list me-2"></i>Ver documentos
            </a>
            <a href="{{ route('admin.documentos-mantenimiento.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>Nuevo mantenimiento
            </a>
        </div>
    </div>

    <!-- Tarjetas de estadisticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Unidades</h6>
                        <h2 class="mb-0">{{ $stats['total_unidades'] }}</h2>
                        <small>100% del parque vehicular</small>
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
                        <h6 class="card-title mb-0">Con Mantenimiento</h6>
                        <h2 class="mb-0">{{ $stats['con_mantenimiento'] }}</h2>
                        <small>{{ $stats['porcentaje_con_mantenimiento'] }}% del total</small>
                    </div>
                    <div class="icon-circle bg-white text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-danger text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Sin Mantenimiento</h6>
                        <h2 class="mb-0">{{ $stats['sin_mantenimiento'] }}</h2>
                        <small>{{ $stats['porcentaje_sin_mantenimiento'] }}% del total</small>
                    </div>
                    <div class="icon-circle bg-white text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Atencion Urgente</h6>
                        <h2 class="mb-0">{{ $stats['urgentes'] }}</h2>
                        <small>{{ $stats['porcentaje_urgentes'] }}% requiere atencion</small>
                    </div>
                    <div class="icon-circle bg-white text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graficos -->
    <div class="row">
        <div class="col-xl-4">
            <div class="chart-container">
                <h5 class="mb-3">Estado de Mantenimiento</h5>
                <div class="chart-wrapper">
                    <canvas id="estadosChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-container">
                <h5 class="mb-3">Distribucion por Zona</h5>
                <div class="chart-wrapper">
                    <canvas id="zonasChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-container">
                <h5 class="mb-3">Tendencia de Agendamientos</h5>
                <div class="chart-wrapper">
                    <canvas id="tendenciaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- AGENDAMIENTO MASIVO -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white fw-bold">
            <i class="fas fa-calendar-plus me-2"></i>Agendamiento Masivo
            <span class="badge bg-info ms-2">Selecciona multiples unidades</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.mantenimiento.agendar-masivo') }}" id="agendamientoForm">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fecha de Agendamiento</label>
                        <input type="date" name="fecha_agendada" class="form-control"
                            value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Observaciones</label>
                        <input type="text" name="observaciones" class="form-control"
                            value="Mantenimiento programado">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success rounded-pill px-4 w-100" id="btnAgendar">
                            <i class="fas fa-calendar-check me-2"></i>Agendar Unidades Seleccionadas
                        </button>
                    </div>
                </div>
                <div id="unidadesContainer"></div>
                <div class="mt-3">
                    <span id="contadorSeleccionados" class="badge bg-primary">0 unidades seleccionadas</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="seleccionarTodas">
                        <i class="fas fa-check-double me-1"></i>Seleccionar todas
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deseleccionarTodas">
                        <i class="fas fa-times me-1"></i>Deseleccionar todas
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="seleccionarUrgentes">
                        <i class="fas fa-exclamation-triangle me-1"></i>Seleccionar urgentes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLA DE UNIDADES CON BUSCADOR -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i>Lista de Unidades</span>
            <div>
                <span class="badge bg-secondary">Total: {{ count($dashboard) }}</span>
            </div>
        </div>
        <div class="card-body">
            <!-- BUSCADOR -->
            <div class="search-container">
                <span class="search-icon"><i class="fas fa-search"></i></span>
                <input type="text" class="search-input" id="searchUnidades"
                       placeholder="Buscar por unidad, zona, operador, estado..."
                       autocomplete="off">
                <span class="search-badge" id="resultadosCount">{{ count($dashboard) }} resultados</span>
                <span class="search-clear" id="searchClear" title="Limpiar busqueda">
                    <i class="fas fa-times-circle"></i>
                </span>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaUnidades">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Unidad</th>
                            <th>Zona</th>
                            <th>Operador</th>
                            <th>Ultimo Mantenimiento</th>
                            <th>Dias</th>
                            <th>Estado</th>
                            <th>Proximo</th>
                            <th>Agendamiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dashboard as $index => $item)
                        <tr data-index="{{ $index }}">
                            <td>
                                <input type="checkbox" class="form-check-input checkbox-unidad"
                                    value="{{ $item['unidad']->id }}"
                                    id="unidad_{{ $item['unidad']->id }}">
                            </td>
                            <td>
                                <strong>{{ $item['unidad']->numero_economico }}</strong>
                                <br><small class="text-muted">{{ $item['unidad']->nombre_unidad ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($item['unidad']->zona->nombre ?? 'N/A') }}</span>
                            </td>
                            <td>
                                @if($item['operador'])
                                {{ $item['operador']->nombre_completo }}
                                <br><small class="text-muted">{{ $item['operador']->clave_operador }}</small>
                                @else
                                <span class="text-muted">Sin operador</span>
                                @endif
                            </td>
                            <td>
                                @if($item['ultimo_mantenimiento'])
                                {{ \Carbon\Carbon::parse($item['ultimo_mantenimiento']->fecha)->format('d/m/Y') }}
                                <br><small class="text-muted">{{ $item['ultimo_mantenimiento']->hora }}</small>
                                @else
                                <span class="text-muted">Sin registros</span>
                                @endif
                            </td>
                            <td>
                                @if($item['dias_desde'] !== null)
                                <span class="fw-bold">{{ $item['dias_desde'] }} dias</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $item['color'] }} rounded-pill px-3">
                                    {{ $item['estado'] }}
                                </span>
                            </td>
                            <td>
                                @if($item['proximo_mantenimiento'])
                                <span class="text-primary">{{ $item['proximo_mantenimiento'] }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item['agendamiento'])
                                <span class="badge bg-info">{{ $item['fecha_agendada']->format('d/m/Y') }}</span>
                                @if($item['dias_restantes'] < 0)
                                    <br><span class="text-danger">Vencido hace {{ abs($item['dias_restantes']) }} dias</span>
                                    @elseif($item['dias_restantes'] <= 7)
                                        <br><span class="text-warning">Vence en {{ $item['dias_restantes'] }} dias</span>
                                        @else
                                        <br><span class="text-success">{{ $item['dias_restantes'] }} dias restantes</span>
                                        @endif
                                        @else
                                        <span class="text-muted">Sin agendar</span>
                                        @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.mantenimiento.detalle', $item['unidad']->id) }}"
                                        class="btn btn-sm btn-outline-info" title="Ver historial">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    <a href="{{ route('admin.unidades.show', $item['unidad']->id) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Ver unidad">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.documentos-mantenimiento.create') }}?unidad={{ $item['unidad']->id }}"
                                        class="btn btn-sm btn-outline-primary" title="Registrar mantenimiento">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-calendar-alt fa-2x d-block mb-2 text-muted"></i>
                                No hay unidades activas registradas.
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
        // ============================================
        // GRAFICOS CON COLORES MEJORADOS
        // ============================================
        const estadosData = @json($estadosData);
        const zonasData = @json($zonasData);
        const tendenciaData = @json($tendenciaAgendamientos);

        const coloresZona = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'];

        // Grafico 1: ESTADOS DE MANTENIMIENTO (Dona) - COLORES MEJORADOS
        new Chart(document.getElementById('estadosChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(estadosData),
                datasets: [{
                    data: Object.values(estadosData),
                    backgroundColor: [
                        '#198754',  // Reciente - Verde
                        '#ffc107',  // Atencion media - Amarillo
                        '#fd7e14',  // Requiere atencion - Naranja
                        '#dc3545',  // Atencion urgente - Rojo
                        '#6f42c1'   // Sin mantenimiento - Purpura
                    ],
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

        // Grafico 2: ZONAS (Barras)
        new Chart(document.getElementById('zonasChart'), {
            type: 'bar',
            data: {
                labels: zonasData.map(z => z.zona),
                datasets: [{
                    label: 'Unidades por zona',
                    data: zonasData.map(z => z.total),
                    backgroundColor: coloresZona.slice(0, zonasData.length),
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Grafico 3: TENDENCIA (Linea)
        new Chart(document.getElementById('tendenciaChart'), {
            type: 'line',
            data: {
                labels: tendenciaData.length > 0 ? tendenciaData.map(t => t.fecha) : ['Sin datos'],
                datasets: [{
                    label: 'Agendamientos',
                    data: tendenciaData.length > 0 ? tendenciaData.map(t => t.total) : [0],
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
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // ============================================
        // CHECKBOXES
        // ============================================
        const checkboxes = document.querySelectorAll('.checkbox-unidad');
        const selectAll = document.getElementById('selectAll');
        const contador = document.getElementById('contadorSeleccionados');
        const form = document.getElementById('agendamientoForm');
        const container = document.getElementById('unidadesContainer');

        function actualizarContador() {
            const seleccionados = document.querySelectorAll('.checkbox-unidad:checked').length;
            contador.textContent = seleccionados + ' unidades seleccionadas';
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            actualizarContador();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const todos = document.querySelectorAll('.checkbox-unidad');
                selectAll.checked = todos.length === document.querySelectorAll('.checkbox-unidad:checked').length;
                actualizarContador();
            });
        });

        document.getElementById('seleccionarTodas').addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = true);
            selectAll.checked = true;
            actualizarContador();
        });

        document.getElementById('deseleccionarTodas').addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = false);
            selectAll.checked = false;
            actualizarContador();
        });

        document.getElementById('seleccionarUrgentes').addEventListener('click', function() {
            checkboxes.forEach(cb => {
                const row = cb.closest('tr');
                if (row) {
                    const estadoCell = row.querySelector('td:nth-child(7)');
                    if (estadoCell) {
                        const estado = estadoCell.textContent.trim();
                        if (estado === 'Atencion urgente' || estado === 'Requiere atencion') {
                            cb.checked = true;
                        } else {
                            cb.checked = false;
                        }
                    }
                }
            });
            actualizarContador();
        });

        // ============================================
        // ENVIO DEL FORMULARIO - METODO TRADICIONAL
        // ============================================
        form.addEventListener('submit', function(e) {
            container.innerHTML = '';

            const selected = document.querySelectorAll('.checkbox-unidad:checked');

            if (selected.length === 0) {
                e.preventDefault();
                alert('Selecciona al menos una unidad para agendar.');
                return false;
            }

            selected.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'unidades[]';
                input.value = cb.value;
                container.appendChild(input);
            });
        });

        // ============================================
        // BUSCADOR MEJORADO
        // ============================================
        const searchInput = document.getElementById('searchUnidades');
        const tableRows = document.querySelectorAll('#tablaUnidades tbody tr');
        const resultadosCount = document.getElementById('resultadosCount');
        const searchClear = document.getElementById('searchClear');

        function filtrarTabla() {
            const search = searchInput.value.toLowerCase().trim();
            let visibles = 0;

            tableRows.forEach(row => {
                // Saltar fila de "no hay datos"
                if (row.querySelector('td[colspan]')) {
                    row.style.display = '';
                    return;
                }

                const text = row.textContent.toLowerCase();
                const match = text.includes(search);
                row.style.display = match ? '' : 'none';
                if (match) visibles++;
            });

            // Actualizar contador
            resultadosCount.textContent = visibles + ' resultados';

            // Mostrar/ocultar boton de limpiar
            if (search.length > 0) {
                searchClear.classList.add('visible');
            } else {
                searchClear.classList.remove('visible');
            }
        }

        // Evento de busqueda
        searchInput.addEventListener('keyup', filtrarTabla);

        // Limpiar busqueda
        searchClear.addEventListener('click', function() {
            searchInput.value = '';
            filtrarTabla();
            searchInput.focus();
        });

        actualizarContador();
    });
</script>
@endsection