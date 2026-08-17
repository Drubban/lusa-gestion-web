

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1>Movimientos de unidades</h1>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<!-- Filtros estilo tarjeta moderna -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <i class="fas fa-filter"></i> Filtros
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.movimientos.index')); ?>" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="entrada" <?php echo e(request('tipo') == 'entrada' ? 'selected' : ''); ?>>Entrada</option>
                    <option value="salida" <?php echo e(request('tipo') == 'salida' ? 'selected' : ''); ?>>Salida</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?php echo e(request('fecha')); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Unidad (N° Económico)</label>
                <input type="text" name="unidad" class="form-control" placeholder="Ej: ECO-001" value="<?php echo e(request('unidad')); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Clave de operador</label>
                <input type="text" name="clave_operador" class="form-control" placeholder="5 dígitos" value="<?php echo e(request('clave_operador')); ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Filtrar</button>
            </div>
        </form>
        <?php if(request()->anyFilled(['tipo','fecha','unidad','clave_operador'])): ?>
            <div class="mt-3">
                <a href="<?php echo e(route('admin.movimientos.index')); ?>" class="btn btn-sm btn-secondary">Limpiar filtros</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tabla de movimientos -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Unidad</th>
                        <th>Operador</th>
                        <th>Departamento</th>
                        <th>Tipo</th>
                        <th>Fecha/Hora</th>
                        <th>Observaciones</th>
                        <th>Sincronizado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        // Obtener operador en la fecha del movimiento (usamos el mismo método del controlador)
                        $operador = \Illuminate\Support\Facades\DB::table('asignacion_operador_unidad as a')
                            ->join('operadores as o', 'o.id', '=', 'a.operador_id')
                            ->where('a.unidad_id', $mov->unidad_id)
                            ->where('a.fecha_inicio', '<=', $mov->fecha_hora)
                            ->where(function ($q) use ($mov) {
                                $q->whereNull('a.fecha_fin')
                                  ->orWhere('a.fecha_fin', '>=', $mov->fecha_hora);
                            })
                            ->select('o.nombre_completo', 'o.clave_operador')
                            ->first();
                    ?>
                    <tr>
                        <td><?php echo e($mov->id); ?></td>
                        <td><?php echo e($mov->unidad->numero_economico ?? 'N/A'); ?></td>
                        <td>
                            <?php if($operador): ?>
                                <?php echo e($operador->nombre_completo); ?><br>
                                <small class="text-muted"><?php echo e($operador->clave_operador); ?></small>
                            <?php else: ?>
                                <span class="text-muted">No asignado</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($mov->departamento->nombre ?? 'N/A'); ?></td>
                        <td>
                            <?php if($mov->tipo == 'entrada'): ?>
                                <span class="badge bg-success"><i class="fas fa-sign-in-alt"></i> Entrada</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="fas fa-sign-out-alt"></i> Salida</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(\Carbon\Carbon::parse($mov->fecha_hora)->format('d/m/Y H:i')); ?></td>
                        <td><?php echo e(Str::limit($mov->observaciones, 50)); ?></td>
                        <td>
                            <?php if($mov->sincronizado): ?>
                                <span class="badge bg-info">Sincronizado</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?php echo e(route('admin.movimientos.show', $mov)); ?>" class="btn btn-outline-info" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('admin.movimientos.edit', $mov)); ?>" class="btn btn-outline-warning" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('admin.movimientos.destroy', $mov)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este movimiento?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center">No hay movimientos registrados.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($movimientos->hasPages()): ?>
        <div class="card-footer">
            <?php echo e($movimientos->appends(request()->query())->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/movimientos/index.blade.php ENDPATH**/ ?>