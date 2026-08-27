

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">
            <i class="fas fa-calendar-check me-2"></i>Tablero de Mantenimiento
            <small class="text-muted fs-6">Cronograma de atencion (max 3-4 semanas)</small>
        </h1>
        <div>
            <a href="<?php echo e(route('admin.documentos-mantenimiento.index')); ?>" class="btn btn-secondary rounded-pill px-4">
                <i class="fas fa-list me-2"></i>Ver todos los documentos
            </a>
            <a href="<?php echo e(route('admin.documentos-mantenimiento.create')); ?>" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>Nuevo mantenimiento
            </a>
        </div>
    </div>

    <!-- Estadisticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Unidades</h6>
                        <h2 class="mb-0"><?php echo e($stats['total_unidades'] ?? 0); ?></h2>
                    </div>
                    <div class="icon-circle bg-white text-primary">
                        <i class="fas fa-bus"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Recientes (&lt; 7 dias)</h6>
                        <h2 class="mb-0"><?php echo e($stats['recientes'] ?? 0); ?></h2>
                    </div>
                    <div class="icon-circle bg-white text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-danger text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Atencion Urgente</h6>
                        <h2 class="mb-0"><?php echo e($stats['atencion_urgente'] ?? 0); ?></h2>
                    </div>
                    <div class="icon-circle bg-white text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-secondary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Sin Mantenimiento</h6>
                        <h2 class="mb-0"><?php echo e($stats['sin_mantenimiento'] ?? 0); ?></h2>
                    </div>
                    <div class="icon-circle bg-white text-secondary">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla del Dashboard -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="mantenimientoTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Unidad</th>
                            <th>Zona</th>
                            <th>Operador</th>
                            <th>Tecnologias</th>
                            <th>Ultimo Mantenimiento</th>
                            <th>Dias</th>
                            <th>Estado</th>
                            <th>Proximo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $dashboard; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
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
                                <?php if(count($item['tecnologias']) > 0): ?>
                                <?php $__currentLoopData = $item['tecnologias']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-secondary"><?php echo e(strtoupper($tec)); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <span class="text-muted">N/A</span>
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
                            <td colspan="9" class="text-center py-4">
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

    .table td {
        vertical-align: middle;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('mantenimientoTable');
        const rows = table.querySelectorAll('tbody tr');

        // Agregar input de busqueda rapida en el header
        const headerRow = table.querySelector('thead tr');
        const filterCell = document.createElement('th');
        filterCell.innerHTML = `
            <input type="text" class="form-control form-control-sm" 
                   placeholder="Buscar..." id="searchMantenimiento" 
                   style="min-width: 120px;">
        `;
        const actionsIndex = headerRow.cells.length - 1;
        headerRow.insertBefore(filterCell, headerRow.cells[actionsIndex]);

        document.getElementById('searchMantenimiento').addEventListener('keyup', function() {
            const search = this.value.toLowerCase();
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(search) ? '' : 'none';
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/mantenimiento/dashboard.blade.php ENDPATH**/ ?>