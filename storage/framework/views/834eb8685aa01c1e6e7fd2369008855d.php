<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Documento de Mantenimiento</h1>
        <a href="<?php echo e(route('admin.documentos-mantenimiento.index')); ?>" class="btn btn-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(route('admin.documentos-mantenimiento.update', $documento)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Asignación (Operador + Unidad)</label>
                        <select name="asignacion_id" class="form-select <?php $__errorArgs = ['asignacion_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccione...</option>
                            <?php $__currentLoopData = $asignaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($asig->id); ?>" <?php if(old('asignacion_id', $documento->asignacion_id) == $asig->id): echo 'selected'; endif; ?>>
                                    <?php echo e($asig->operador->nombre_completo); ?> - <?php echo e($asig->unidad->numero_economico); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['asignacion_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Rol (zona)</label>
                        <input type="text" name="rol" class="form-control <?php $__errorArgs = ['rol'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('rol', $documento->rol)); ?>" required>
                        <?php $__errorArgs = ['rol'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Tecnología reportada (seleccione una o varias)</label>
                        <div class="row">
                            <?php $tecSeleccionadas = explode(',', $documento->tecnologia_reportada ?? ''); ?>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="barras" <?php echo e(in_array('barras', $tecSeleccionadas) ? 'checked' : ''); ?>> BARRAS</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="gps" <?php echo e(in_array('gps', $tecSeleccionadas) ? 'checked' : ''); ?>> GPS</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="varilla" <?php echo e(in_array('varilla', $tecSeleccionadas) ? 'checked' : ''); ?>> VARILLA</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="telpo" <?php echo e(in_array('telpo', $tecSeleccionadas) ? 'checked' : ''); ?>> TELPO</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="mdvr" <?php echo e(in_array('mdvr', $tecSeleccionadas) ? 'checked' : ''); ?>> MDVR</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="camaras" <?php echo e(in_array('camaras', $tecSeleccionadas) ? 'checked' : ''); ?>> CÁMARAS</label></div>
                        </div>
                        <?php $__errorArgs = ['tecnologia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prueba barras de conteo Optocontrol</label>
                        <select name="prueba_barras" class="form-select">
                            <option value="">Seleccione</option>
                            <option value="SI" <?php if(old('prueba_barras', $documento->prueba_barras) == 'SI'): echo 'selected'; endif; ?>>SI</option>
                            <option value="NO" <?php if(old('prueba_barras', $documento->prueba_barras) == 'NO'): echo 'selected'; endif; ?>>NO</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Comentarios / Observaciones</label>
                        <textarea name="comentarios" class="form-control" rows="3"><?php echo e(old('comentarios', $documento->comentarios)); ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha</label>
                        <input type="date" name="fecha" class="form-control <?php $__errorArgs = ['fecha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('fecha', $documento->fecha->format('Y-m-d'))); ?>" required>
                        <?php $__errorArgs = ['fecha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Hora</label>
                        <input type="time" name="hora" class="form-control <?php $__errorArgs = ['hora'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('hora', $documento->hora)); ?>" required>
                        <?php $__errorArgs = ['hora'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Número de adeudos</label>
                        <input type="number" name="veces_adeudo" class="form-control" value="<?php echo e(old('veces_adeudo', $documento->veces_adeudo)); ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Observaciones de adeudo</label>
                        <textarea name="observaciones_adeudo" class="form-control" rows="2"><?php echo e(old('observaciones_adeudo', $documento->observaciones_adeudo)); ?></textarea>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="vigente" class="form-check-input" value="1" <?php if(old('vigente', $documento->vigente)): echo 'checked'; endif; ?>>
                            <label class="form-check-label">Documento vigente</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/documentos/mantenimiento/edit.blade.php ENDPATH**/ ?>