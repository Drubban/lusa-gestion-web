<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Nuevo Documento de Mantenimiento</h1>
        <a href="<?php echo e(route('admin.documentos-mantenimiento.index')); ?>" class="btn btn-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(route('admin.documentos-mantenimiento.store')); ?>">
                <?php echo csrf_field(); ?>

                <div class="row g-4">
                    <!-- Seleccion de Unidad -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidad *</label>
                        <select name="unidad_id" id="unidad_id" class="form-select <?php $__errorArgs = ['unidad_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccione una unidad...</option>
                            <?php $__currentLoopData = $unidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($unidad->id); ?>"
                                data-zona="<?php echo e($unidad->zona->nombre ?? ''); ?>"
                                <?php echo e((old('unidad_id', $unidadSeleccionada?->id) == $unidad->id) ? 'selected' : ''); ?>>
                                <?php echo e($unidad->numero_economico); ?> - <?php echo e($unidad->nombre_unidad ?? 'Sin nombre'); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['unidad_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Zona (Radio Button) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Zona *</label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check">
                                <input type="radio" name="zona" id="zona_reyes" value="reyes" class="form-check-input"
                                    <?php echo e(old('zona', $unidadSeleccionada?->zona?->nombre ?? '') == 'reyes' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="zona_reyes">Reyes</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="zona" id="zona_apaxco" value="apaxco" class="form-check-input"
                                    <?php echo e(old('zona', $unidadSeleccionada?->zona?->nombre ?? '') == 'apaxco' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="zona_apaxco">Apaxco</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="zona" id="zona_citrus" value="citrus" class="form-check-input"
                                    <?php echo e(old('zona', $unidadSeleccionada?->zona?->nombre ?? '') == 'citrus' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="zona_citrus">Citrus</label>
                            </div>
                        </div>
                        <?php $__errorArgs = ['zona'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Tecnologia reportada -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Tecnologia reportada (seleccione una o varias)</label>
                        <div class="row">
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="barras"> BARRAS</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="gps"> GPS</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="varilla"> VARILLA</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="telpo"> TELPO</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="mdvr"> MDVR</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="camaras"> CAMARAS</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="tubo_corrugado"> TUBO CORRUGADO</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="limpieza_camaras"> LIMPIEZA DE CAMARAS</label></div>
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

                    <!-- Estado de Camaras -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Estado de Camaras</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="camara1" value="1" class="form-check-input" id="camara1">
                                    <label class="form-check-label" for="camara1">Camara 1 funcionando</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="camara2" value="1" class="form-check-input" id="camara2">
                                    <label class="form-check-label" for="camara2">Camara 2 funcionando</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="camara3" value="1" class="form-check-input" id="camara3">
                                    <label class="form-check-label" for="camara3">Camara 3 funcionando</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="camara4" value="1" class="form-check-input" id="camara4">
                                    <label class="form-check-label" for="camara4">Camara 4 funcionando</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prueba barras de conteo Optocontrol (Radio Button) -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Prueba barras de conteo Optocontrol</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input type="radio" name="prueba_barras" id="prueba_si" value="SI" class="form-check-input">
                                <label class="form-check-label" for="prueba_si">SI</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="prueba_barras" id="prueba_no" value="NO" class="form-check-input" checked>
                                <label class="form-check-label" for="prueba_no">NO</label>
                            </div>
                        </div>
                        <?php $__errorArgs = ['prueba_barras'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Comentarios / Observaciones -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Comentarios / Observaciones</label>
                        <textarea name="comentarios" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- Fecha y Hora -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha *</label>
                        <input type="date" name="fecha" class="form-control <?php $__errorArgs = ['fecha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(date('Y-m-d')); ?>" required>
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
                        <label class="form-label fw-semibold">Hora *</label>
                        <input type="time" name="hora" class="form-control <?php $__errorArgs = ['hora'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(date('H:i')); ?>" required>
                        <?php $__errorArgs = ['hora'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Vigente -->
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="vigente" class="form-check-input" checked>
                            <label class="form-check-label">Documento vigente</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar documento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unidadSelect = document.getElementById('unidad_id');
        const zonaRadios = document.querySelectorAll('input[name="zona"]');

        // Auto-seleccionar zona al cambiar de unidad
        unidadSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const zona = selectedOption.getAttribute('data-zona');

            if (zona) {
                const zonaLower = zona.toLowerCase();
                zonaRadios.forEach(radio => {
                    radio.checked = (radio.value === zonaLower);
                });
            }
        });

        // Si ya hay una unidad seleccionada al cargar, auto-seleccionar su zona
        if (unidadSelect.value) {
            const selectedOption = unidadSelect.options[unidadSelect.selectedIndex];
            const zona = selectedOption.getAttribute('data-zona');
            if (zona) {
                const zonaLower = zona.toLowerCase();
                zonaRadios.forEach(radio => {
                    radio.checked = (radio.value === zonaLower);
                });
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/documentos/mantenimiento/create.blade.php ENDPATH**/ ?>