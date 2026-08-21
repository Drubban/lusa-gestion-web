

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Tecnologías de Unidades</h1>
        <a href="<?php echo e(route('admin.tecnologias.create')); ?>" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i> Agregar Tecnología
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.tecnologias.index')); ?>" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Unidad..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $nombre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('tipo') == $key ? 'selected' : ''); ?>>
                                <?php echo e($nombre); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Unidad</label>
                    <select name="unidad_id" class="form-select">
                        <option value="">Todas</option>
                        <?php $__currentLoopData = $unidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($unidad->id); ?>" <?php echo e(request('unidad_id') == $unidad->id ? 'selected' : ''); ?>>
                                <?php echo e($unidad->numero_economico); ?> - <?php echo e($unidad->nombre_unidad ?? 'Sin nombre'); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-search me-2"></i> Filtrar
                    </button>
                    <a href="<?php echo e(route('admin.tecnologias.index')); ?>" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-undo me-2"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Unidad</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3">Datos</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $tecnologias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <strong><?php echo e($item->unidad->numero_economico ?? 'N/A'); ?></strong>
                                    <br><small class="text-muted"><?php echo e($item->unidad->nombre_unidad ?? ''); ?></small>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-primary"><?php echo e($item->tipo_nombre); ?></span>
                                </td>
                                <td class="px-4 py-3"><?php echo e($item->nombre ?? 'N/A'); ?></td>
                                <td class="px-4 py-3">
                                    <?php
                                        $datos = $item->datos;
                                    ?>
                                    <?php if($datos): ?>
                                        <small class="text-muted">
                                            <?php if(isset($datos['imei_telpo'])): ?>
                                                IMEI: <?php echo e($datos['imei_telpo']); ?>

                                            <?php elseif(isset($datos['imei_gps'])): ?>
                                                IMEI: <?php echo e($datos['imei_gps']); ?>

                                            <?php elseif(isset($datos['id_barra'])): ?>
                                                ID: <?php echo e($datos['id_barra']); ?>

                                            <?php elseif(isset($datos['dvr'])): ?>
                                                DVR: <?php echo e($datos['dvr']); ?>

                                            <?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">Sin datos</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if($item->activo): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('admin.tecnologias.show', $item)); ?>" 
                                           class="btn btn-sm btn-outline-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.tecnologias.edit', $item)); ?>" 
                                           class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger btn-delete" 
                                                title="Eliminar"
                                                data-id="<?php echo e($item->id); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-<?php echo e($item->id); ?>" 
                                          action="<?php echo e(route('admin.tecnologias.destroy', $item)); ?>" 
                                          method="POST" style="display: none;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-microchip fa-2x d-block mb-2"></i>
                                    No hay tecnologías registradas
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4 d-flex justify-content-between align-items-center">
        <div>
            Mostrando <?php echo e($tecnologias->firstItem() ?? 0); ?> - <?php echo e($tecnologias->lastItem() ?? 0); ?> 
            de <?php echo e($tecnologias->total()); ?> registros
        </div>
        <div>
            <?php echo e($tecnologias->links()); ?>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.btn-delete');
            if (deleteBtn) {
                e.preventDefault();
                const id = deleteBtn.getAttribute('data-id');
                if (id && confirm('¿Estás seguro de eliminar esta tecnología?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/tecnologias/index.blade.php ENDPATH**/ ?>