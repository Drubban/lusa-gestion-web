

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Usuario de App</h1>
        <a href="<?php echo e(route('admin.usuarios-app.index')); ?>" class="btn btn-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(route('admin.usuarios-app.update', $usuario)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre de usuario *</label>
                        <input type="text" name="nombre_usuario" class="form-control <?php $__errorArgs = ['nombre_usuario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre_usuario', $usuario->nombre_usuario)); ?>" required>
                        <?php $__errorArgs = ['nombre_usuario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contraseña (dejar vacío para no cambiar)</label>
                        <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Departamento *</label>
                        <select name="departamento_id" class="form-select" required>
                            <?php $__currentLoopData = $departamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($depto->id); ?>" <?php if(old('departamento_id', $usuario->departamento_id)==$depto->id): echo 'selected'; endif; ?>><?php echo e(ucfirst($depto->nombre)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="puede_generar_documentos" class="form-check-input" value="1" <?php if(old('puede_generar_documentos', $usuario->puede_generar_documentos)): echo 'checked'; endif; ?>>
                            <label class="form-check-label">Puede generar documentos</label>
                        </div>
                        <div class="form-check mt-2">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" <?php if(old('activo', $usuario->activo)): echo 'checked'; endif; ?>>
                            <label class="form-check-label">Activo</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/usuarios/edit.blade.php ENDPATH**/ ?>