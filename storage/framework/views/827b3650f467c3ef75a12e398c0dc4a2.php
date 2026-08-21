

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Detalle de Tecnología</h1>
        <div>
            <a href="<?php echo e(route('admin.tecnologias.edit', $tecnologia->id)); ?>" class="btn btn-warning rounded-pill px-4">
                <i class="fas fa-edit me-2"></i> Editar
            </a>
            <a href="<?php echo e(route('admin.tecnologias.index')); ?>" class="btn btn-secondary rounded-pill px-4">
                Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Unidad</strong>
                            </div>
                            <p class="fs-5"><?php echo e($tecnologia->unidad->numero_economico ?? 'N/A'); ?></p>
                            <small><?php echo e($tecnologia->unidad->nombre_unidad ?? ''); ?></small>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Tipo</strong>
                            </div>
                            <p><span class="badge bg-primary fs-6"><?php echo e($tecnologia->tipo_nombre); ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Nombre</strong>
                            </div>
                            <p><?php echo e($tecnologia->nombre ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Estado</strong>
                            </div>
                            <p>
                                <?php if($tecnologia->activo): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactivo</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="col-12">
                            <hr class="border-2 border-secondary">
                            <h5><i class="fas fa-microchip me-2"></i>Datos Específicos</h5>
                        </div>

                        <?php $datos = $tecnologia->datos; ?>

                        <?php if($tecnologia->tipo == 'barras' && $datos): ?>
                            <div class="col-md-3"><strong>ID Barra:</strong> <?php echo e($datos['id_barra'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>Barras:</strong> <?php echo e($datos['barras'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>Teléfono:</strong> <?php echo e($datos['telefono'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>Plan:</strong> <?php echo e($datos['plan'] ?? 'N/A'); ?></div>
                        <?php elseif($tecnologia->tipo == 'telpo' && $datos): ?>
                            <div class="col-md-3"><strong>IMEI Antes:</strong> <?php echo e($datos['imei_antes'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>V. APK:</strong> <?php echo e($datos['v_apk'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>Telpo:</strong> <?php echo e($datos['telpo'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>IMEI Telpo:</strong> <?php echo e($datos['imei_telpo'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>Teléfono:</strong> <?php echo e($datos['telefono'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>Plan:</strong> <?php echo e($datos['plan'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>Costo Plan:</strong> $<?php echo e(number_format($datos['costo_plan'] ?? 0, 2)); ?></div>
                        <?php elseif($tecnologia->tipo == 'gps' && $datos): ?>
                            <div class="col-md-4"><strong>IMEI GPS:</strong> <?php echo e($datos['imei_gps'] ?? 'N/A'); ?></div>
                            <div class="col-md-4"><strong>Teléfono:</strong> <?php echo e($datos['telefono'] ?? 'N/A'); ?></div>
                            <div class="col-md-4"><strong>Plan:</strong> <?php echo e($datos['plan'] ?? 'N/A'); ?></div>
                        <?php elseif($tecnologia->tipo == 'mdvr' && $datos): ?>
                            <div class="col-md-3"><strong>DVR:</strong> <?php echo e($datos['dvr'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>Modelo:</strong> <?php echo e($datos['modelo'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>Cámaras:</strong> <?php echo e($datos['camaras'] ?? 'N/A'); ?></div>
                            <div class="col-md-3"><strong>Memoria:</strong> <?php echo e($datos['memoria'] ?? 'N/A'); ?></div>
                        <?php else: ?>
                            <div class="col-12 text-muted">No hay datos específicos para esta tecnología</div>
                        <?php endif; ?>
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
                        <strong class="text-muted">Registrado por</strong>
                        <p><?php echo e($tecnologia->creador->nombre_usuario ?? 'N/A'); ?></p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Fecha de creación</strong>
                        <p><?php echo e($tecnologia->created_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Última actualización</strong>
                        <p><?php echo e($tecnologia->updated_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">ID</strong>
                        <p><span class="badge bg-secondary">#<?php echo e($tecnologia->id); ?></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/tecnologias/show.blade.php ENDPATH**/ ?>