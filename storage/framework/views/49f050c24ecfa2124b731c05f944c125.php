

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Detalle de Inventario</h1>
        <div>
            <a href="<?php echo e(route('admin.inventario.edit', $inventario->id)); ?>" class="btn btn-warning rounded-pill px-4">
                <i class="fas fa-edit me-2"></i> Editar
            </a>
            <a href="<?php echo e(route('admin.inventario.index')); ?>" class="btn btn-secondary rounded-pill px-4">
                Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Información principal -->
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Fecha de Entrega</strong>
                            </div>
                            <p class="fs-5"><?php echo e($inventario->fecha_entrega->format('d/m/Y')); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Categoría</strong>
                            </div>
                            <p><span class="badge bg-info fs-6"><?php echo e($inventario->nombre_categoria); ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Departamento</strong>
                            </div>
                            <p><?php echo e(ucfirst($inventario->departamento->nombre ?? 'N/A')); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Área</strong>
                            </div>
                            <p><?php echo e($inventario->area ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Nombre de quien recibe</strong>
                            </div>
                            <p class="fs-5"><?php echo e($inventario->nombre_recibe); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2 mb-2">
                                <strong class="text-muted">Clave de Empleado</strong>
                            </div>
                            <p><span class="badge bg-secondary"><?php echo e($inventario->clave_empleado); ?></span></p>
                        </div>

                        <!-- Campos específicos por categoría -->
                        <?php if($inventario->esCategoriaEquipo()): ?>
                            <div class="col-12">
                                <hr class="border-2 border-primary">
                                <h5 class="text-primary"><i class="fas fa-server me-2"></i>Datos del Equipo</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="border-bottom pb-2 mb-2">
                                    <strong class="text-muted">Nombre del Equipo</strong>
                                </div>
                                <p><?php echo e($inventario->nombre_equipo ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <div class="border-bottom pb-2 mb-2">
                                    <strong class="text-muted">Marca</strong>
                                </div>
                                <p><?php echo e($inventario->marca ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-4">
                                <div class="border-bottom pb-2 mb-2">
                                    <strong class="text-muted">Modelo</strong>
                                </div>
                                <p><?php echo e($inventario->modelo ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-4">
                                <div class="border-bottom pb-2 mb-2">
                                    <strong class="text-muted">Número de Serie</strong>
                                </div>
                                <p><span class="badge bg-dark"><?php echo e($inventario->numero_serie ?? 'N/A'); ?></span></p>
                            </div>
                            <div class="col-md-4">
                                <div class="border-bottom pb-2 mb-2">
                                    <strong class="text-muted">Datos Extra</strong>
                                </div>
                                <p><?php echo e($inventario->datos_extra ?? 'N/A'); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if($inventario->esCategoriaProducto()): ?>
                            <div class="col-12">
                                <hr class="border-2 border-success">
                                <h5 class="text-success"><i class="fas fa-box me-2"></i>Datos del Producto</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="border-bottom pb-2 mb-2">
                                    <strong class="text-muted">Nombre del Producto</strong>
                                </div>
                                <p><?php echo e($inventario->nombre_producto ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <div class="border-bottom pb-2 mb-2">
                                    <strong class="text-muted">Marca</strong>
                                </div>
                                <p><?php echo e($inventario->marca_producto ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-4">
                                <div class="border-bottom pb-2 mb-2">
                                    <strong class="text-muted">Cantidad</strong>
                                </div>
                                <p><span class="badge bg-success fs-6"><?php echo e($inventario->cantidad ?? 0); ?></span></p>
                            </div>
                            <div class="col-md-8">
                                <div class="border-bottom pb-2 mb-2">
                                    <strong class="text-muted">Descripción</strong>
                                </div>
                                <p><?php echo e($inventario->descripcion ?? 'N/A'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar con información adicional -->
        <div class="col-lg-4">
            <!-- 🔥 TARJETA DE IMAGEN -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title"><i class="fas fa-image me-2"></i>Hoja Firmada</h5>
                    <hr>
                    <?php if($inventario->imagen): ?>
                        <div class="text-center">
                            <a href="<?php echo e($inventario->imagen_url); ?>" target="_blank" class="d-block">
                                <img src="<?php echo e($inventario->imagen_url); ?>" 
                                     alt="Hoja firmada" 
                                     class="img-fluid rounded border"
                                     style="max-height: 300px; object-fit: contain;">
                            </a>
                            <a href="<?php echo e($inventario->imagen_url); ?>" target="_blank" class="btn btn-sm btn-primary mt-2">
                                <i class="fas fa-eye me-1"></i> Ver imagen completa
                            </a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">No hay imagen adjunta</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Información Adicional</h5>
                    <hr>
                    <div class="mb-3">
                        <strong class="text-muted">Registrado por</strong>
                        <p><?php echo e($inventario->creador->nombre_usuario ?? 'N/A'); ?></p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Fecha de creación</strong>
                        <p><?php echo e($inventario->created_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Última actualización</strong>
                        <p><?php echo e($inventario->updated_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">ID del registro</strong>
                        <p><span class="badge bg-secondary">#<?php echo e($inventario->id); ?></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/inventario/show.blade.php ENDPATH**/ ?>