<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Detalle de Unidad</h1>
        <div>
            <a href="<?php echo e(route('admin.unidades.edit', $unidad->id)); ?>" class="btn btn-warning rounded-pill px-4">
                <i class="fas fa-edit me-2"></i> Editar
            </a>
            <a href="<?php echo e(route('admin.unidades.index')); ?>" class="btn btn-secondary rounded-pill px-4">
                Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Información básica -->
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Número Económico</strong>
                            </div>
                            <p class="fs-5"><?php echo e($unidad->numero_economico); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Nombre</strong>
                            </div>
                            <p><?php echo e($unidad->nombre_unidad ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Zona</strong>
                            </div>
                            <p><?php echo e($unidad->zona->nombre ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Estado</strong>
                            </div>
                            <p>
                                <?php if($unidad->activo): ?>
                                    <span class="badge bg-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactiva</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <!-- 🔥 NUEVA SECCIÓN: EQUIPOS -->
                        <div class="col-12">
                            <hr class="border-2 border-primary">
                            <h5 class="text-primary"><i class="fas fa-microchip me-2"></i>Equipos Asignados</h5>
                        </div>

                        <div class="col-md-4">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">E.T (Equipo Telpo)</strong>
                            </div>
                            <p>
                                <?php if($unidad->equipo_telpo): ?>
                                    <span class="badge bg-success fs-6"><?php echo e($unidad->equipo_telpo); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Sin asignar</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="col-md-4">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">E.G (Equipo GPS)</strong>
                            </div>
                            <p>
                                <?php if($unidad->equipo_gps): ?>
                                    <span class="badge bg-success fs-6"><?php echo e($unidad->equipo_gps); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Sin asignar</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="col-md-4">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">E.B (Equipo Barras)</strong>
                            </div>
                            <p>
                                <?php if($unidad->equipo_barras): ?>
                                    <span class="badge bg-success fs-6"><?php echo e($unidad->equipo_barras); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Sin asignar</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <!-- QR -->
                        <div class="col-12">
                            <hr class="border-2 border-secondary">
                            <h5><i class="fas fa-qrcode me-2"></i>Código QR</h5>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="border-bottom pb-2 mb-2">
                                        <strong class="text-muted">Código QR</strong>
                                    </div>
                                    <p><code><?php echo e($unidad->codigo_qr); ?></code></p>
                                </div>
                                <div class="col-md-4">
                                    <div class="border-bottom pb-2 mb-2">
                                        <strong class="text-muted">Token</strong>
                                    </div>
                                    <p><code><?php echo e($unidad->token_qr ?? 'N/A'); ?></code></p>
                                </div>
                            </div>
                        </div>

                        <!-- Operador Actual -->
                        <div class="col-12">
                            <hr class="border-2 border-secondary">
                            <h5><i class="fas fa-user me-2"></i>Operador Actual</h5>
                            <?php if($operadorActual): ?>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <strong>Nombre:</strong> <?php echo e($operadorActual->nombre_completo); ?>

                                    </div>
                                    <div class="col-md-6">
                                        <strong>Clave:</strong> <?php echo e($operadorActual->clave_operador); ?>

                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No hay operador asignado actualmente</p>
                            <?php endif; ?>
                        </div>

                        <!-- Asignaciones Históricas -->
                        <div class="col-12">
                            <hr class="border-2 border-secondary">
                            <h5><i class="fas fa-history me-2"></i>Historial de Asignaciones</h5>
                            <?php if($unidad->asignaciones->count() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Operador</th>
                                                <th>Inicio</th>
                                                <th>Fin</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $unidad->asignaciones->sortByDesc('fecha_inicio'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asignacion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($asignacion->operador->nombre_completo ?? 'N/A'); ?></td>
                                                    <td><?php echo e($asignacion->fecha_inicio->format('d/m/Y')); ?></td>
                                                    <td><?php echo e($asignacion->fecha_fin ? $asignacion->fecha_fin->format('d/m/Y') : 'Actual'); ?></td>
                                                    <td>
                                                        <?php if($asignacion->vigente): ?>
                                                            <span class="badge bg-success">Vigente</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Finalizada</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No hay asignaciones registradas</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Información Adicional</h5>
                    <hr>
                    <div class="mb-3">
                        <strong class="text-muted">ID</strong>
                        <p><span class="badge bg-secondary">#<?php echo e($unidad->id); ?></span></p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Fecha de creación</strong>
                        <p><?php echo e($unidad->created_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Última actualización</strong>
                        <p><?php echo e($unidad->updated_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Código QR</strong>
                        <p><small><code><?php echo e(substr($unidad->codigo_qr, 0, 20)); ?>...</code></small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/unidades/show.blade.php ENDPATH**/ ?>