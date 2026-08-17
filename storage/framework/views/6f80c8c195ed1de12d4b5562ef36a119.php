

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Detalle del Operador</h1>
        <div>
            <a href="<?php echo e(route('admin.operadores.edit', $operador)); ?>" class="btn btn-warning rounded-pill px-4">Editar</a>
            <a href="<?php echo e(route('admin.operadores.index')); ?>" class="btn btn-secondary rounded-pill px-4">Volver</a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Clave operador:</strong></div>
                    <p><?php echo e($operador->clave_operador); ?></p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Nombre completo:</strong></div>
                    <p><?php echo e($operador->nombre_completo); ?></p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Unidad actual:</strong></div>
                    <p><?php echo e($unidadActual ? $unidadActual->numero_economico . ' - ' . $unidadActual->nombre_unidad : 'Sin unidad'); ?></p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Zona (según unidad):</strong></div>
                    <p><?php echo e($unidadActual && $unidadActual->zona ? ucfirst($unidadActual->zona->nombre) : 'Sin zona'); ?></p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Estado:</strong></div>
                    <p><?php echo $operador->activo ? '<span class="badge bg-success rounded-pill px-3">Activo</span>' : '<span class="badge bg-danger rounded-pill px-3">Inactivo</span>'; ?></p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Registrado:</strong></div>
                    <p><?php echo e($operador->created_at->format('d/m/Y H:i')); ?></p>
                </div>
            </div>

            <div class="mt-4">
                <div class="border-bottom pb-2 mb-2"><strong>Historial de asignaciones</strong></div>
                <ul class="list-group">
                    <?php $__empty_1 = true; $__currentLoopData = $operador->asignaciones->sortByDesc('fecha_inicio'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong><?php echo e($asig->unidad->numero_economico); ?></strong> - <?php echo e($asig->unidad->nombre_unidad); ?></span>
                        <span>Desde <?php echo e($asig->fecha_inicio->format('d/m/Y')); ?> <?php if($asig->fecha_fin): ?> hasta <?php echo e($asig->fecha_fin->format('d/m/Y')); ?> <?php else: ?> <span class="badge bg-success rounded-pill">Actual</span> <?php endif; ?></span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="list-group-item text-muted">Sin asignaciones previas</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/operadores/show.blade.php ENDPATH**/ ?>