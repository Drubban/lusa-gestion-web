

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1>Nueva Unidad</h1>
    <a href="<?php echo e(route('admin.unidades.index')); ?>" class="btn btn-secondary">Volver</a>
</div>

<form method="POST" action="<?php echo e(route('admin.unidades.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Número económico *</label>
            <input type="text" name="numero_economico" class="form-control <?php $__errorArgs = ['numero_economico'];
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
        <div class="col-md-6 mb-3">
            <label class="form-label">Nombre de la unidad (opcional)</label>
            <input type="text" name="nombre_unidad" class="form-control">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Zona *</label>
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
                <option value="<?php echo e($zona->id); ?>" <?php echo e(old('zona_id') == $zona->id ? 'selected' : ''); ?>>
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
        <div class="col-md-6 mb-3">
            <label class="form-label">Asignar operador (opcional)</label>
            <select name="operador_id" class="form-select">
                <option value="">-- Sin operador --</option>
                <?php $__currentLoopData = $operadores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($operador->id); ?>"><?php echo e($operador->nombre_completo); ?> (<?php echo e($operador->clave_operador); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <div class="form-check mt-4">
                <input type="checkbox"
                    name="activo"
                    class="form-check-input"
                    id="activo"
                    value="1"
                <?php echo e(old('activo', true) ? 'checked' : ''); ?>>
                <label class="form-check-label" for="activo">Activo</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Guardar unidad</button>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/unidades/create.blade.php ENDPATH**/ ?>