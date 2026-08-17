

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Operadores</h1>
        <a href="<?php echo e(route('admin.operadores.create')); ?>" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus"></i> Nuevo Operador
        </a>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    
    <!-- Panel de filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.operadores.index')); ?>" class="row g-3 align-items-end">
                <!-- Búsqueda general -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Clave, nombre o unidad" value="<?php echo e(request('search')); ?>">
                    </div>
                </div>

                <!-- Filtro por zona -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Zona</label>
                    <select name="zona" class="form-select">
                        <option value="">Todas</option>
                        <option value="reyes" <?php echo e(request('zona') == 'reyes' ? 'selected' : ''); ?>>Reyes</option>
                        <option value="apaxco" <?php echo e(request('zona') == 'apaxco' ? 'selected' : ''); ?>>Apaxco</option>
                        <option value="citrus" <?php echo e(request('zona') == 'citrus' ? 'selected' : ''); ?>>Citrus</option>
                    </select>
                </div>

                <!-- Botones de filtro -->
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="<?php echo e(route('admin.operadores.index')); ?>" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-undo"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de operadores -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <!-- Clave -->
                            <th>
                                <a href="<?php echo e(route('admin.operadores.index', array_merge(request()->all(), ['sort' => 'clave_operador', 'direction' => request('sort') == 'clave_operador' && request('direction') == 'asc' ? 'desc' : 'asc']))); ?>"
                                    class="text-dark text-decoration-none">
                                    Clave
                                    <?php if(request('sort') == 'clave_operador'): ?>
                                    <i class="fas fa-sort-<?php echo e(request('direction') == 'asc' ? 'up' : 'down'); ?>"></i>
                                    <?php else: ?>
                                    <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <!-- Nombre -->
                            <th>
                                <a href="<?php echo e(route('admin.operadores.index', array_merge(request()->all(), ['sort' => 'nombre_completo', 'direction' => request('sort') == 'nombre_completo' && request('direction') == 'asc' ? 'desc' : 'asc']))); ?>"
                                    class="text-dark text-decoration-none">
                                    Nombre
                                    <?php if(request('sort') == 'nombre_completo'): ?>
                                    <i class="fas fa-sort-<?php echo e(request('direction') == 'asc' ? 'up' : 'down'); ?>"></i>
                                    <?php else: ?>
                                    <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <!-- Unidad actual (N° Económico) -->
                            <th>
                                <a href="<?php echo e(route('admin.operadores.index', array_merge(request()->all(), ['sort' => 'unidad_numero', 'direction' => request('sort') == 'unidad_numero' && request('direction') == 'asc' ? 'desc' : 'asc']))); ?>"
                                    class="text-dark text-decoration-none">
                                    Unidad actual
                                    <?php if(request('sort') == 'unidad_numero'): ?>
                                    <i class="fas fa-sort-<?php echo e(request('direction') == 'asc' ? 'up' : 'down'); ?>"></i>
                                    <?php else: ?>
                                    <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Zona</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $operadores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($operador->clave_operador); ?></td>
                            <td><?php echo e($operador->nombre_completo); ?></td>
                            <td>
                                <?php if($operador->asignacionVigente && $operador->asignacionVigente->unidad): ?>
                                <?php echo e($operador->asignacionVigente->unidad->numero_economico); ?>

                                <?php else: ?>
                                <span class="text-muted">Sin unidad</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($operador->asignacionVigente && $operador->asignacionVigente->unidad && $operador->asignacionVigente->unidad->zona): ?>
                                <?php echo e(ucfirst($operador->asignacionVigente->unidad->zona->nombre)); ?>

                                <?php else: ?>
                                <span class="text-muted">Sin zona</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $operador->activo ? '<span class="badge bg-success rounded-pill px-3">Activo</span>' : '<span class="badge bg-danger rounded-pill px-3">Inactivo</span>'; ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.operadores.show', $operador)); ?>" class="btn btn-sm btn-outline-info rounded-circle" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('admin.operadores.edit', $operador)); ?>" class="btn btn-sm btn-outline-warning rounded-circle" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('admin.operadores.destroy', $operador)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger rounded-circle" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No hay operadores registrados.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-center">
                <?php echo e($operadores->links('pagination::bootstrap-5', ['class' => 'pagination-sm'])); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/operadores/index.blade.php ENDPATH**/ ?>