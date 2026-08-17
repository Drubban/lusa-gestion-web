

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Unidades</h1>
        <a href="<?php echo e(route('admin.unidades.create')); ?>" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus"></i> Nueva Unidad
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <!-- Panel de filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.unidades.index')); ?>" class="row g-3 align-items-end">
                <!-- Búsqueda general -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="N° Económico, nombre u operador" value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                
                <!-- Filtro por estado -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo" <?php echo e(request('estado') == 'activo' ? 'selected' : ''); ?>>Activos</option>
                        <option value="inactivo" <?php echo e(request('estado') == 'inactivo' ? 'selected' : ''); ?>>Inactivos</option>
                    </select>
                </div>
                
                <!-- Botones de filtro -->
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="<?php echo e(route('admin.unidades.index')); ?>" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-undo"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de unidades -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <!-- N° Económico -->
                            <th>
                                <a href="<?php echo e(route('admin.unidades.index', array_merge(request()->all(), ['sort' => 'numero_economico', 'direction' => request('sort') == 'numero_economico' && request('direction') == 'asc' ? 'desc' : 'asc']))); ?>" 
                                   class="text-dark text-decoration-none">
                                    N° Económico
                                    <?php if(request('sort') == 'numero_economico'): ?>
                                        <i class="fas fa-sort-<?php echo e(request('direction') == 'asc' ? 'up' : 'down'); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <!-- Nombre unidad -->
                            <th>
                                <a href="<?php echo e(route('admin.unidades.index', array_merge(request()->all(), ['sort' => 'nombre_unidad', 'direction' => request('sort') == 'nombre_unidad' && request('direction') == 'asc' ? 'desc' : 'asc']))); ?>" 
                                   class="text-dark text-decoration-none">
                                    Nombre unidad
                                    <?php if(request('sort') == 'nombre_unidad'): ?>
                                        <i class="fas fa-sort-<?php echo e(request('direction') == 'asc' ? 'up' : 'down'); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <!-- Zona -->
                            <th>
                                <a href="<?php echo e(route('admin.unidades.index', array_merge(request()->all(), ['sort' => 'zona', 'direction' => request('sort') == 'zona' && request('direction') == 'asc' ? 'desc' : 'asc']))); ?>" 
                                   class="text-dark text-decoration-none">
                                    Zona
                                    <?php if(request('sort') == 'zona'): ?>
                                        <i class="fas fa-sort-<?php echo e(request('direction') == 'asc' ? 'up' : 'down'); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <!-- Operador actual -->
                            <th>
                                <a href="<?php echo e(route('admin.unidades.index', array_merge(request()->all(), ['sort' => 'operador', 'direction' => request('sort') == 'operador' && request('direction') == 'asc' ? 'desc' : 'asc']))); ?>" 
                                   class="text-dark text-decoration-none">
                                    Operador actual
                                    <?php if(request('sort') == 'operador'): ?>
                                        <i class="fas fa-sort-<?php echo e(request('direction') == 'asc' ? 'up' : 'down'); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $unidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($unidad->numero_economico); ?></td>
                            <td><?php echo e($unidad->nombre_unidad ?? 'Sin nombre'); ?></td>
                            <td><?php echo e(ucfirst($unidad->zona->nombre ?? 'Sin zona')); ?></td>
                            <td>
                                <?php if($unidad->asignacionVigente && $unidad->asignacionVigente->operador): ?>
                                    <?php echo e($unidad->asignacionVigente->operador->nombre_completo); ?>

                                <?php else: ?>
                                    <span class="text-muted">Sin operador</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $unidad->activo ? '<span class="badge bg-success rounded-pill px-3">Activo</span>' : '<span class="badge bg-danger rounded-pill px-3">Inactivo</span>'; ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.unidades.show', $unidad)); ?>" class="btn btn-sm btn-outline-info rounded-circle" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.unidades.edit', $unidad)); ?>" class="btn btn-sm btn-outline-warning rounded-circle" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('admin.unidades.destroy', $unidad)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta unidad?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger rounded-circle" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center py-4">No hay unidades registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="small text-muted">
                    Mostrando <?php echo e($unidades->firstItem() ?? 0); ?> - <?php echo e($unidades->lastItem() ?? 0); ?> de <?php echo e($unidades->total()); ?> registros
                </div>
                <div>
                    <?php echo e($unidades->onEachSide(1)->links('pagination::bootstrap-5')); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Reducir tamaño de botones de paginación */
    .pagination {
        --bs-pagination-padding-x: 0.5rem;
        --bs-pagination-padding-y: 0.25rem;
        --bs-pagination-font-size: 0.75rem;
        margin-bottom: 0;
    }
    
    .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/unidades/index.blade.php ENDPATH**/ ?>