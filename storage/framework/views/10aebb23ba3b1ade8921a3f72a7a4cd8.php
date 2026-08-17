

<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1>Detalle de Unidad</h1>
        <div>
            <a href="<?php echo e(route('admin.unidades.edit', $unidad)); ?>" class="btn btn-warning">Editar</a>
            <a href="<?php echo e(route('admin.unidades.index')); ?>" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Información general</div>
                <div class="card-body">
                    <p><strong>ID:</strong> <?php echo e($unidad->id); ?></p>
                    <p><strong>Número económico:</strong> <?php echo e($unidad->numero_economico); ?></p>
                    <p><strong>Nombre unidad:</strong> <?php echo e($unidad->nombre_unidad ?? 'Sin nombre'); ?></p>
                    <p><strong>Zona:</strong> <?php echo e(ucfirst($unidad->zona->nombre ?? 'Sin asignar')); ?></p>
                    <p><strong>Código QR (token):</strong> <code><?php echo e($unidad->token_qr); ?></code></p>
                    <p><strong>Estado:</strong>
                        <?php echo $unidad->activo ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?>

                    </p>
                    <p><strong>Registrado:</strong> <?php echo e($unidad->created_at->format('d/m/Y H:i')); ?></p>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?> 
                        <a href="<?php echo e(route('admin.unidades.regenerar-token', $unidad)); ?>"
                            class="btn btn-sm btn-secondary">Regenerar token QR</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Operador actual</div>
                <div class="card-body">
                    <?php if($operadorActual): ?>
                        <p><strong>Nombre:</strong> <?php echo e($operadorActual->nombre_completo); ?></p>
                        <p><strong>Clave:</strong> <?php echo e($operadorActual->clave_operador); ?></p>
                        <p><strong>Zona:</strong> <?php echo e($operadorActual->zona->nombre ?? 'N/A'); ?></p>
                    <?php else: ?>
                        <p class="text-muted">No tiene operador asignado actualmente.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Historial de asignaciones</div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php $__empty_1 = true; $__currentLoopData = $unidad->asignaciones->sortByDesc('fecha_inicio'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="list-group-item">
                                <strong><?php echo e($asig->operador->nombre_completo); ?></strong>
                                (<?php echo e($asig->operador->clave_operador); ?>)<br>
                                Desde <?php echo e($asig->fecha_inicio->format('d/m/Y')); ?>

                                <?php if($asig->fecha_fin): ?> hasta <?php echo e($asig->fecha_fin->format('d/m/Y')); ?> <?php else: ?> <span
                                class="badge bg-success">Actual</span> <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="list-group-item">Sin asignaciones previas</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">Últimos movimientos (entrada/salida)</div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php $__empty_1 = true; $__currentLoopData = $unidad->movimientos->sortByDesc('fecha_hora')->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="list-group-item">
                                <?php echo e(ucfirst($mov->tipo)); ?> - <?php echo e($mov->departamento->nombre ?? 'N/A'); ?><br>
                                <small><?php echo e($mov->fecha_hora->format('d/m/Y H:i')); ?></small>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="list-group-item">Sin movimientos registrados</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/unidades/show.blade.php ENDPATH**/ ?>