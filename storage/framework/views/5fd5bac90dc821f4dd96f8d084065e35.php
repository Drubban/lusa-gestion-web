<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Unidad</h1>
        <a href="<?php echo e(route('admin.unidades.index')); ?>" class="btn btn-secondary rounded-pill px-4">
            Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(route('admin.unidades.update', $unidad->id)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="row g-4">
                    <!-- Número Económico -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Número Económico *</label>
                        <input type="text" name="numero_economico" 
                               class="form-control <?php $__errorArgs = ['numero_economico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('numero_economico', $unidad->numero_economico)); ?>" required>
                        <?php $__errorArgs = ['numero_economico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Nombre Unidad -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre de la Unidad</label>
                        <input type="text" name="nombre_unidad" 
                               class="form-control <?php $__errorArgs = ['nombre_unidad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('nombre_unidad', $unidad->nombre_unidad)); ?>">
                        <?php $__errorArgs = ['nombre_unidad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Zona -->
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
                                <option value="<?php echo e($zona->id); ?>" 
                                    <?php echo e(old('zona_id', $unidad->zona_id) == $zona->id ? 'selected' : ''); ?>>
                                    <?php echo e(ucfirst($zona->nombre)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['zona_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- 🔥 EQUIPOS -->
                    <div class="col-12">
                        <hr class="border-2 border-primary">
                        <h5 class="text-primary"><i class="fas fa-microchip me-2"></i>Equipos Asignados</h5>
                        <p class="text-muted small">Ingresa el identificador de cada equipo asignado a esta unidad</p>
                    </div>

                    <!-- E.T - Equipo Telpo -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">E.T (Equipo Telpo)</label>
                        <input type="text" name="equipo_telpo" 
                               class="form-control <?php $__errorArgs = ['equipo_telpo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('equipo_telpo', $unidad->equipo_telpo)); ?>"
                               placeholder="Ej: TEL-001">
                        <?php $__errorArgs = ['equipo_telpo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="text-muted">Identificador del equipo Telpo</small>
                    </div>

                    <!-- E.G - Equipo GPS -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">E.G (Equipo GPS)</label>
                        <input type="text" name="equipo_gps" 
                               class="form-control <?php $__errorArgs = ['equipo_gps'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('equipo_gps', $unidad->equipo_gps)); ?>"
                               placeholder="Ej: GPS-001">
                        <?php $__errorArgs = ['equipo_gps'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="text-muted">Identificador del equipo GPS</small>
                    </div>

                    <!-- E.B - Equipo Barras -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">E.B (Equipo Barras)</label>
                        <input type="text" name="equipo_barras" 
                               class="form-control <?php $__errorArgs = ['equipo_barras'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('equipo_barras', $unidad->equipo_barras)); ?>"
                               placeholder="Ej: BAR-001">
                        <?php $__errorArgs = ['equipo_barras'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="text-muted">Identificador del equipo de barras</small>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" 
                                   class="form-check-input" id="activo" 
                                   value="1" <?php echo e(old('activo', $unidad->activo) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="activo">
                                <i class="fas fa-check-circle me-1"></i> Unidad Activa
                            </label>
                        </div>
                    </div>

                    <!-- Operador -->
                    <div class="col-12">
                        <hr class="border-2 border-secondary">
                        <h5><i class="fas fa-user me-2"></i>Asignación de Operador</h5>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Operador Actual</label>
                        <select name="operador_id" class="form-select">
                            <option value="">-- Sin operador --</option>
                            <?php $__currentLoopData = $operadores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($operador->id); ?>" 
                                    <?php echo e(old('operador_id', $operadorActual?->id) == $operador->id ? 'selected' : ''); ?>>
                                    <?php echo e($operador->nombre_completo); ?> (<?php echo e($operador->clave_operador); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Selecciona el operador que estará asignado a esta unidad</small>
                    </div>

                    <div class="col-12">
                        <hr>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-save me-2"></i> Actualizar Unidad
                        </button>
                        <a href="<?php echo e(route('admin.unidades.index')); ?>" class="btn btn-secondary rounded-pill px-4 ms-2">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/unidades/edit.blade.php ENDPATH**/ ?>