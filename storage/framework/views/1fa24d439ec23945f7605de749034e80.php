<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">
            <i class="fas fa-history me-2"></i>Historial de Mantenimiento
            <small class="text-muted fs-6"><?php echo e($unidad->numero_economico); ?> - <?php echo e($unidad->nombre_unidad ?? 'Sin nombre'); ?></small>
        </h1>
        <div>
            <a href="<?php echo e(route('admin.mantenimiento.dashboard')); ?>" class="btn btn-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Volver al tablero
            </a>
            <a href="<?php echo e(route('admin.documentos-mantenimiento.create')); ?>?unidad=<?php echo e($unidad->id); ?>" 
               class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>Nuevo mantenimiento
            </a>
        </div>
    </div>

    <!-- Info de la unidad -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Unidad</h6>
                    <h5><?php echo e($unidad->numero_economico); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Zona</h6>
                    <h5><?php echo e(ucfirst($unidad->zona->nombre ?? 'N/A')); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Operador</h6>
                    <h5><?php echo e($unidad->asignacionVigente->operador->nombre_completo ?? 'Sin operador'); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Tecnologias</h6>
                    <h5>
                        <?php $__currentLoopData = $unidad->tecnologias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge bg-secondary"><?php echo e(strtoupper($tec->tipo)); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white fw-bold">
            <i class="fas fa-list me-2"></i>Historial de Mantenimientos
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Zona</th>
                            <th>Tecnologia</th>
                            <th>Prueba barras</th>
                            <th>Comentarios</th>
                            <th>Vigente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $mantenimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(\Carbon\Carbon::parse($doc->fecha)->format('d/m/Y')); ?></td>
                            <td><?php echo e($doc->hora); ?></td>
                            <td><?php echo e(strtoupper($doc->rol ?? '')); ?></td>
                            <td><?php echo e($doc->tecnologia_reportada); ?></td>
                            <td><?php echo e($doc->prueba_barras ?? '-'); ?></td>
                            <td><?php echo e(\Str::limit($doc->comentarios, 30)); ?></td>
                            <td><?php echo $doc->vigente ? '<span class="badge bg-success">Vigente</span>' : '<span class="badge bg-secondary">No vigente</span>'; ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.documentos-mantenimiento.show', $doc)); ?>" 
                                   class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('admin.documentos-mantenimiento.exportar-pdf', $doc)); ?>" 
                                   class="btn btn-sm btn-outline-danger"><i class="fas fa-file-pdf"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-calendar-alt fa-2x d-block mb-2 text-muted"></i>
                                No hay mantenimientos registrados para esta unidad.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/mantenimiento/detalle.blade.php ENDPATH**/ ?>