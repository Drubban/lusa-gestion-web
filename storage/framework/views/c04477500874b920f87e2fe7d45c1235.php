<?php $__env->startSection('content'); ?>
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

    .filter-card {
        background: #f8f9fa;
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .reporte-card {
        border-left: 4px solid #dc3545;
    }

    .reporte-hoy-card {
        border-left: 4px solid #fd7e14;
    }

    .badge-estado {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
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
                        <a class="dropdown-item" href="<?php echo e(route('admin.exportar.mantenimientos.csv')); ?>" target="_blank">
                            <i class="fas fa-file-csv text-success me-2"></i> Exportar a CSV (Programables)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?php echo e(route('admin.exportar.mantenimientos.excel')); ?>" target="_blank">
                            <i class="fas fa-file-excel text-success me-2"></i> Exportar a Excel (Programables)
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?php echo e(route('admin.exportar.mantenimientos.todos')); ?>" target="_blank">
                            <i class="fas fa-file-archive text-secondary me-2"></i> Exportar Todo (Historico)
                        </a>
                    </li>
                </ul>
            </div>
            <a href="<?php echo e(route('admin.documentos-mantenimiento.index')); ?>" class="btn btn-secondary rounded-pill px-4">
                <i class="fas fa-list me-2"></i>Ver documentos
            </a>
            <a href="<?php echo e(route('admin.documentos-mantenimiento.create')); ?>" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>Nuevo mantenimiento
            </a>
        </div>
    </div>

    <!-- FILTROS PRINCIPALES -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white fw-bold">
            <i class="fas fa-filter me-2"></i>Filtros
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.mantenimiento.dashboard')); ?>" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="Reciente" <?php echo e(request('estado') == 'Reciente' ? 'selected' : ''); ?>>Reciente</option>
                        <option value="Atencion media" <?php echo e(request('estado') == 'Atencion media' ? 'selected' : ''); ?>>Atencion media</option>
                        <option value="Requiere atencion" <?php echo e(request('estado') == 'Requiere atencion' ? 'selected' : ''); ?>>Requiere atencion</option>
                        <option value="Atencion urgente" <?php echo e(request('estado') == 'Atencion urgente' ? 'selected' : ''); ?>>Atencion urgente</option>
                        <option value="Sin mantenimiento" <?php echo e(request('estado') == 'Sin mantenimiento' ? 'selected' : ''); ?>>Sin mantenimiento</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Zona</label>
                    <select name="zona" class="form-select">
                        <option value="">Todas</option>
                        <option value="reyes" <?php echo e(request('zona') == 'reyes' ? 'selected' : ''); ?>>Reyes</option>
                        <option value="apaxco" <?php echo e(request('zona') == 'apaxco' ? 'selected' : ''); ?>>Apaxco</option>
                        <option value="citrus" <?php echo e(request('zona') == 'citrus' ? 'selected' : ''); ?>>Citrus</option>
                        <option value="tranzumpango" <?php echo e(request('zona') == 'tranzumpango' ? 'selected' : ''); ?>>Tranzumpango</option>
                        <option value="corredor bc" <?php echo e(request('zona') == 'corredor bc' ? 'selected' : ''); ?>>Corredor BC</option>
                        <option value="odz" <?php echo e(request('zona') == 'odz' ? 'selected' : ''); ?>>ODZ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Agendamiento</label>
                    <select name="agendamiento" class="form-select">
                        <option value="">Todos</option>
                        <option value="con" <?php echo e(request('agendamiento') == 'con' ? 'selected' : ''); ?>>Con agendamiento</option>
                        <option value="sin" <?php echo e(request('agendamiento') == 'sin' ? 'selected' : ''); ?>>Sin agendamiento</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Unidad, operador..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                    <a href="<?php echo e(route('admin.mantenimiento.dashboard')); ?>" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-undo me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- REPORTE DE UNIDADES NO PRESENTADAS (CON FILTRO) -->
    <!-- ============================================ -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 reporte-hoy-card">
        <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>
                <i class="fas fa-calendar-times me-2 text-warning"></i>Unidades No Presentadas
                <?php if($modoNoPresentadas === 'hoy'): ?>
                    <span class="badge bg-warning text-dark ms-2">HOY</span>
                <?php elseif($modoNoPresentadas === 'vencidos'): ?>
                    <span class="badge bg-danger ms-2">VENCIDAS</span>
                <?php elseif($modoNoPresentadas === 'rango'): ?>
                    <span class="badge bg-info ms-2">
                        <?php echo e($fechaNoPresentadasDesde->format('d/m/Y')); ?> - <?php echo e($fechaNoPresentadasHasta->format('d/m/Y')); ?>

                    </span>
                <?php else: ?>
                    <span class="badge bg-secondary ms-2">TODAS</span>
                <?php endif; ?>
            </span>
            <div>
                <span class="badge bg-warning text-dark me-1"><?php echo e($totalNoPresentadasHoy); ?> hoy</span>
                <span class="badge bg-danger me-1"><?php echo e($totalNoPresentadasVencidas); ?> vencidas</span>
                <span class="badge bg-secondary"><?php echo e($totalNoPresentadas); ?> total</span>
            </div>
        </div>

        <div class="card-body">
            <!-- FILTROS DE NO PRESENTADAS -->
            <div class="filter-card mb-3">
                <form method="GET" action="<?php echo e(route('admin.mantenimiento.dashboard')); ?>" class="row g-2 align-items-end" id="formFiltroNoPresentadas">
                    
                    <?php $__currentLoopData = ['estado', 'zona', 'agendamiento', 'search']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filtro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(request($filtro)): ?>
                            <input type="hidden" name="<?php echo e($filtro); ?>" value="<?php echo e(request($filtro)); ?>">
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-eye me-1"></i>Ver
                        </label>
                        <select name="np_modo" class="form-select" id="np_modo">
                            <option value="hoy" <?php echo e($modoNoPresentadas == 'hoy' ? 'selected' : ''); ?>>
                                Solo HOY (<?php echo e($totalNoPresentadasHoy); ?>)
                            </option>
                            <option value="vencidos" <?php echo e($modoNoPresentadas == 'vencidos' ? 'selected' : ''); ?>>
                                Todas las vencidas (<?php echo e($totalNoPresentadasVencidas); ?>)
                            </option>
                            <option value="rango" <?php echo e($modoNoPresentadas == 'rango' ? 'selected' : ''); ?>>
                                Rango de fechas personalizado
                            </option>
                            <option value="todas" <?php echo e($modoNoPresentadas == 'todas' ? 'selected' : ''); ?>>
                                Todas (<?php echo e($totalNoPresentadas); ?>)
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 rango-fechas-field" style="<?php echo e($modoNoPresentadas === 'rango' ? '' : 'display:none;'); ?>">
                        <label class="form-label fw-semibold mb-1">Desde</label>
                        <input type="date" name="np_desde" class="form-control form-control-sm"
                               value="<?php echo e($fechaNoPresentadasDesde->format('Y-m-d')); ?>">
                    </div>

                    <div class="col-md-3 rango-fechas-field" style="<?php echo e($modoNoPresentadas === 'rango' ? '' : 'display:none;'); ?>">
                        <label class="form-label fw-semibold mb-1">Hasta</label>
                        <input type="date" name="np_hasta" class="form-control form-control-sm"
                               value="<?php echo e($fechaNoPresentadasHasta->format('Y-m-d')); ?>">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 flex-fill">
                            <i class="fas fa-filter me-1"></i>Aplicar
                        </button>
                        <a href="<?php echo e(route('admin.mantenimiento.dashboard')); ?>"
                           class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>

            <?php if($totalNoPresentadas > 0): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <?php if($modoNoPresentadas === 'hoy'): ?>
                        Estas unidades tenían agendamiento para hoy y no han sido revisadas.
                    <?php elseif($modoNoPresentadas === 'vencidos'): ?>
                        Estas unidades tenían agendamiento en días anteriores y no se presentaron.
                    <?php elseif($modoNoPresentadas === 'rango'): ?>
                        Unidades no presentadas entre
                        <strong><?php echo e($fechaNoPresentadasDesde->format('d/m/Y')); ?></strong> y
                        <strong><?php echo e($fechaNoPresentadasHasta->format('d/m/Y')); ?></strong>.
                    <?php else: ?>
                        Todas las unidades con agendamiento sin cumplir.
                    <?php endif; ?>
                    <strong>Reporta la no presentación</strong> para reprogramar su mantenimiento.
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Unidad</th>
                                <th>Zona</th>
                                <th>Operador</th>
                                <th>Fecha Agendada</th>
                                <th>Dias</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $noPresentadas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agendamiento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $fechaAg = Carbon\Carbon::parse($agendamiento->fecha_agendada);
                                    $diasAtras = $fechaAg->diffInDays(now());
                                    $esHoy = $fechaAg->isToday();
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($agendamiento->unidad->numero_economico ?? 'N/A'); ?></strong>
                                        <br><small class="text-muted"><?php echo e($agendamiento->unidad->nombre_unidad ?? ''); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo e(ucfirst($agendamiento->unidad->zona->nombre ?? 'N/A')); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($agendamiento->unidad->asignacionVigente->operador ?? null): ?>
                                            <?php echo e($agendamiento->unidad->asignacionVigente->operador->nombre_completo); ?>

                                            <br><small class="text-muted"><?php echo e($agendamiento->unidad->asignacionVigente->operador->clave_operador); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Sin operador</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($esHoy ? 'warning text-dark' : 'secondary'); ?>">
                                            <?php echo e($fechaAg->format('d/m/Y')); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($esHoy): ?>
                                            <span class="badge bg-warning text-dark">Hoy</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><?php echo e($diasAtras); ?> dias</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($agendamiento->estado === 'pendiente' ? 'warning text-dark' : 'danger'); ?>">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $agendamiento->estado))); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reportarModal<?php echo e($agendamiento->id); ?>">
                                            <i class="fas fa-flag me-1"></i>Reportar No Presentado
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Reportar No Presentado -->
                                <div class="modal fade" id="reportarModal<?php echo e($agendamiento->id); ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="<?php echo e(route('admin.agendamientos.store')); ?>">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="unidad_id" value="<?php echo e($agendamiento->unidad_id); ?>">
                                                <input type="hidden" name="fecha_agendada" value="<?php echo e($fechaAg->format('Y-m-d')); ?>">
                                                <input type="hidden" name="agendamiento_id" value="<?php echo e($agendamiento->id); ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        Reportar No Presentado -
                                                        <?php echo e($agendamiento->unidad->numero_economico ?? ''); ?>

                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <strong><?php echo e($agendamiento->unidad->numero_economico ?? ''); ?></strong>
                                                        <br>Tenía agendamiento para el <?php echo e($fechaAg->format('d/m/Y')); ?>

                                                        <?php if(!$esHoy): ?>
                                                            <br><span class="text-danger"><?php echo e($diasAtras); ?> días sin presentarse</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Motivo de no presentación *</label>
                                                        <select name="motivo_no_presentado" class="form-select" required>
                                                            <option value="">Seleccione un motivo...</option>
                                                            <option value="taller">En taller</option>
                                                            <option value="averia">Avería mecánica</option>
                                                            <option value="falta_operador">Falta de operador</option>
                                                            <option value="documentacion">Falta de documentación</option>
                                                            <option value="cliente">Unidad en ruta con cliente</option>
                                                            <option value="otros">Otro motivo</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Observaciones</label>
                                                        <textarea name="observaciones" class="form-control" rows="2"
                                                                  placeholder="Detalles adicionales..."></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Nueva Fecha de Agendamiento</label>
                                                        <input type="date" name="fecha_reprogramada" class="form-control"
                                                               value="<?php echo e(date('Y-m-d', strtotime('+7 days'))); ?>">
                                                        <small class="text-muted">Se recomienda agendar con al menos 7 días de anticipación</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger">Guardar Reporte y Reprogramar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center text-success py-3">
                    <i class="fas fa-check-circle fa-3x mb-2 d-block"></i>
                    <h5>
                        <?php if($modoNoPresentadas === 'hoy'): ?>
                            Todas las unidades programadas para hoy se presentaron
                        <?php elseif($modoNoPresentadas === 'vencidos'): ?>
                            No hay unidades vencidas sin reportar
                        <?php elseif($modoNoPresentadas === 'rango'): ?>
                            No hay unidades no presentadas en el rango seleccionado
                        <?php else: ?>
                            No hay unidades no presentadas
                        <?php endif; ?>
                    </h5>
                    <p class="text-muted">No hay reportes de no presentación pendientes</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- REPORTE DE UNIDADES VENCIDAS (más de 21 días) -->
    <!-- ============================================ -->
    <?php
        $noPresentadas21 = array_filter($dashboard, function($item) {
            return $item['estado'] === 'Atencion urgente' &&
                   $item['dias_desde'] !== null &&
                   $item['dias_desde'] > 21;
        });
        $totalNoPresentadas21 = count($noPresentadas21);
    ?>

    <?php if($totalNoPresentadas21 > 0): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4 reporte-card">
        <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Unidades con Mantenimiento Vencido (+21 días)</span>
            <span class="badge bg-danger"><?php echo e($totalNoPresentadas21); ?> unidades</span>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Estas unidades tienen más de 21 días sin mantenimiento.
                <strong>Requieren atención urgente</strong>.
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Unidad</th>
                            <th>Zona</th>
                            <th>Ultimo Mantenimiento</th>
                            <th>Dias</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $noPresentadas21; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><strong><?php echo e($item['unidad']->numero_economico); ?></strong></td>
                            <td><?php echo e(ucfirst($item['unidad']->zona->nombre ?? 'N/A')); ?></td>
                            <td><?php echo e($item['ultimo_mantenimiento'] ? Carbon\Carbon::parse($item['ultimo_mantenimiento']->fecha)->format('d/m/Y') : 'Sin registro'); ?></td>
                            <td><span class="badge bg-danger"><?php echo e($item['dias_desde']); ?> dias</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reportar21Modal<?php echo e($item['unidad']->id); ?>">
                                    <i class="fas fa-calendar-plus me-1"></i>Agendar Mantenimiento
                                </button>
                            </td>
                        </tr>

                        <!-- Modal para Agendar Mantenimiento -->
                        <div class="modal fade" id="reportar21Modal<?php echo e($item['unidad']->id); ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="<?php echo e(route('admin.mantenimiento.agendar-masivo')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="unidades[]" value="<?php echo e($item['unidad']->id); ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Agendar Mantenimiento - <?php echo e($item['unidad']->numero_economico); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-danger">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong><?php echo e($item['dias_desde']); ?> días sin mantenimiento</strong>
                                                <br>La unidad requiere atención urgente.
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Fecha de Agendamiento *</label>
                                                <input type="date" name="fecha_agendada" class="form-control" value="<?php echo e(date('Y-m-d', strtotime('+7 days'))); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Observaciones</label>
                                                <textarea name="observaciones" class="form-control" rows="2">Mantenimiento urgente - Unidad vencida</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">Agendar Mantenimiento</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tarjetas de estadisticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Unidades</h6>
                        <h2 class="mb-0"><?php echo e($stats['total_unidades']); ?></h2>
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
                        <h2 class="mb-0"><?php echo e($stats['con_mantenimiento']); ?></h2>
                        <small><?php echo e($stats['porcentaje_con_mantenimiento']); ?>% del total</small>
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
                        <h2 class="mb-0"><?php echo e($stats['sin_mantenimiento']); ?></h2>
                        <small><?php echo e($stats['porcentaje_sin_mantenimiento']); ?>% del total</small>
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
                        <h2 class="mb-0"><?php echo e($stats['urgentes']); ?></h2>
                        <small><?php echo e($stats['porcentaje_urgentes']); ?>% requiere atencion</small>
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
            <form method="POST" action="<?php echo e(route('admin.mantenimiento.agendar-masivo')); ?>" id="agendamientoForm">
                <?php echo csrf_field(); ?>
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fecha de Agendamiento</label>
                        <input type="date" name="fecha_agendada" class="form-control"
                            value="<?php echo e(date('Y-m-d', strtotime('+7 days'))); ?>">
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

    <!-- TABLA DE UNIDADES -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i>Lista de Unidades</span>
            <div>
                <span class="badge bg-secondary">Total: <?php echo e(count($dashboard)); ?></span>
            </div>
        </div>
        <div class="card-body">
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
                        <?php $__empty_1 = true; $__currentLoopData = $dashboard; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr data-index="<?php echo e($index); ?>">
                            <td>
                                <input type="checkbox" class="form-check-input checkbox-unidad"
                                    value="<?php echo e($item['unidad']->id); ?>"
                                    id="unidad_<?php echo e($item['unidad']->id); ?>">
                            </td>
                            <td>
                                <strong><?php echo e($item['unidad']->numero_economico); ?></strong>
                                <br><small class="text-muted"><?php echo e($item['unidad']->nombre_unidad ?? ''); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo e(ucfirst($item['unidad']->zona->nombre ?? 'N/A')); ?></span>
                            </td>
                            <td>
                                <?php if($item['operador']): ?>
                                <?php echo e($item['operador']->nombre_completo); ?>

                                <br><small class="text-muted"><?php echo e($item['operador']->clave_operador); ?></small>
                                <?php else: ?>
                                <span class="text-muted">Sin operador</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($item['ultimo_mantenimiento']): ?>
                                <?php echo e(\Carbon\Carbon::parse($item['ultimo_mantenimiento']->fecha)->format('d/m/Y')); ?>

                                <br><small class="text-muted"><?php echo e($item['ultimo_mantenimiento']->hora); ?></small>
                                <?php else: ?>
                                <span class="text-muted">Sin registros</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($item['dias_desde'] !== null): ?>
                                <span class="fw-bold"><?php echo e($item['dias_desde']); ?> dias</span>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($item['color']); ?> rounded-pill px-3">
                                    <?php echo e($item['estado']); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($item['proximo_mantenimiento']): ?>
                                <span class="text-primary"><?php echo e($item['proximo_mantenimiento']); ?></span>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($item['agendamiento']): ?>
                                <span class="badge bg-info"><?php echo e($item['fecha_agendada']->format('d/m/Y')); ?></span>
                                <?php if($item['dias_restantes'] < 0): ?>
                                    <br><span class="text-danger">Vencido hace <?php echo e(abs($item['dias_restantes'])); ?> dias</span>
                                <?php elseif($item['dias_restantes'] <= 7): ?>
                                    <br><span class="text-warning">Vence en <?php echo e($item['dias_restantes']); ?> dias</span>
                                <?php else: ?>
                                    <br><span class="text-success"><?php echo e($item['dias_restantes']); ?> dias restantes</span>
                                <?php endif; ?>
                                <?php else: ?>
                                <span class="text-muted">Sin agendar</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('admin.mantenimiento.detalle', $item['unidad']->id)); ?>"
                                        class="btn btn-sm btn-outline-info" title="Ver historial">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.unidades.show', $item['unidad']->id)); ?>"
                                        class="btn btn-sm btn-outline-secondary" title="Ver unidad">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.documentos-mantenimiento.create')); ?>?unidad=<?php echo e($item['unidad']->id); ?>"
                                        class="btn btn-sm btn-outline-primary" title="Registrar mantenimiento">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-calendar-alt fa-2x d-block mb-2 text-muted"></i>
                                No hay unidades activas registradas.
                            </td>
                        </tr>
                        <?php endif; ?>
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
        // GRAFICOS
        // ============================================
        const estadosData = <?php echo json_encode($estadosData, 15, 512) ?>;
        const zonasData = <?php echo json_encode($zonasData, 15, 512) ?>;
        const tendenciaData = <?php echo json_encode($tendenciaAgendamientos, 15, 512) ?>;

        const coloresZona = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'];

        // Grafico 1: ESTADOS DE MANTENIMIENTO
        new Chart(document.getElementById('estadosChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(estadosData),
                datasets: [{
                    data: Object.values(estadosData),
                    backgroundColor: [
                        '#198754', '#ffc107', '#fd7e14', '#dc3545', '#6f42c1'
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
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Grafico 2: ZONAS
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
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Grafico 3: TENDENCIA
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
                    legend: {
                        display: false
                    }
                }
            }
        });

        // ============================================
        // FILTRO DE NO PRESENTADAS
        // ============================================
        const npModo = document.getElementById('np_modo');
        if (npModo) {
            npModo.addEventListener('change', function() {
                const rangoFields = document.querySelectorAll('.rango-fechas-field');

                if (this.value === 'rango') {
                    // Mostrar campos de rango, esperar a que el usuario elija fechas
                    rangoFields.forEach(el => el.style.display = '');
                } else {
                    // Ocultar campos de rango y enviar automáticamente
                    rangoFields.forEach(el => el.style.display = 'none');
                    document.getElementById('formFiltroNoPresentadas').submit();
                }
            });
        }

        // Auto-submit cuando cambian fechas en modo rango
        document.querySelectorAll('input[name="np_desde"], input[name="np_hasta"]').forEach(el => {
            el.addEventListener('change', function() {
                if (document.getElementById('np_modo').value === 'rango') {
                    document.getElementById('formFiltroNoPresentadas').submit();
                }
            });
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
        // ENVIO DEL FORMULARIO DE AGENDAMIENTO MASIVO
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

        actualizarContador();
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/mantenimiento/dashboard.blade.php ENDPATH**/ ?>