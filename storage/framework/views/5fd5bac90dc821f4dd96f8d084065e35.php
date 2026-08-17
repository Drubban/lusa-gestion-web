

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Unidad</h1>
        <a href="<?php echo e(route('admin.unidades.index')); ?>" class="btn btn-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(route('admin.unidades.update', $unidad)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Número económico *</label>
                        <input type="text" name="numero_economico" value="<?php echo e(old('numero_economico', $unidad->numero_economico)); ?>" class="form-control <?php $__errorArgs = ['numero_economico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <?php $__errorArgs = ['numero_economico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre de la unidad</label>
                        <input type="text" name="nombre_unidad" value="<?php echo e(old('nombre_unidad', $unidad->nombre_unidad)); ?>" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Zona *</label>
                        <select name="zona_id" class="form-select <?php $__errorArgs = ['zona_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccione una zona</option>
                            <?php $__currentLoopData = $zonas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zona): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($zona->id); ?>" <?php echo e(old('zona_id', $unidad->zona_id) == $zona->id ? 'selected' : ''); ?>>
                                    <?php echo e(ucfirst($zona->nombre)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['zona_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Asignar operador</label>
                        <select name="operador_id" class="form-select">
                            <option value="">-- Sin operador --</option>
                            <?php $__currentLoopData = $operadores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($operador->id); ?>" <?php echo e(old('operador_id', $operadorActual->id ?? '') == $operador->id ? 'selected' : ''); ?>>
                                    <?php echo e($operador->nombre_completo); ?> (<?php echo e($operador->clave_operador); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" <?php echo e(old('activo', $unidad->activo) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar unidad</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/unidades/edit.blade.php ENDPATH**/ ?>