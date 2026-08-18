

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Ajuste</h1>
        <a href="<?php echo e(route('admin.ajustes.index')); ?>" class="btn btn-secondary rounded-pill px-4">
            Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(route('admin.ajustes.update', $ajuste->id)); ?>" id="ajusteForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="row g-4">
                    <!-- Folio -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Folio *</label>
                        <input type="text" name="folio" 
                               class="form-control <?php $__errorArgs = ['folio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('folio', $ajuste->folio)); ?>" placeholder="Ej: AJ-2024-001" required>
                        <?php $__errorArgs = ['folio'];
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

                    <!-- Fecha -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha *</label>
                        <input type="date" name="fecha" 
                               class="form-control <?php $__errorArgs = ['fecha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('fecha', $ajuste->fecha->format('Y-m-d'))); ?>" required>
                        <?php $__errorArgs = ['fecha'];
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

                    <!-- Hora -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Hora *</label>
                        <input type="time" name="hora" 
                               class="form-control <?php $__errorArgs = ['hora'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('hora', $ajuste->hora)); ?>" required>
                        <?php $__errorArgs = ['hora'];
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
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Zona</label>
                        <input type="text" name="zona" 
                               class="form-control <?php $__errorArgs = ['zona'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('zona', $ajuste->zona)); ?>" placeholder="Ej: Zona Norte">
                        <?php $__errorArgs = ['zona'];
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

                    <!-- Monto Total -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Monto Total *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="monto_total" 
                                   class="form-control <?php $__errorArgs = ['monto_total'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   value="<?php echo e(old('monto_total', $ajuste->monto_total)); ?>" required>
                        </div>
                        <?php $__errorArgs = ['monto_total'];
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

                    <!-- Firmado -->
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="firmado" 
                                   class="form-check-input" id="firmado" 
                                   value="1" <?php echo e(old('firmado', $ajuste->firmado) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="firmado">
                                <i class="fas fa-signature me-1"></i> Firmado
                            </label>
                        </div>
                    </div>

                    <!-- Separador -->
                    <div class="col-12">
                        <hr class="border-2 border-secondary">
                        <h5><i class="fas fa-user me-2"></i>Datos del Operador</h5>
                    </div>

                    <!-- Operador -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Operador *</label>
                        <select name="operador_id" id="operador_id" 
                                class="form-select <?php $__errorArgs = ['operador_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccione un operador</option>
                            <?php $__currentLoopData = $operadores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($operador->id); ?>" 
                                        data-clave="<?php echo e($operador->clave_operador); ?>"
                                        <?php echo e(old('operador_id', $ajuste->operador_id) == $operador->id ? 'selected' : ''); ?>>
                                    <?php echo e($operador->nombre_completo); ?> (<?php echo e($operador->clave_operador); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['operador_id'];
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

                    <!-- Clave Operador -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Clave del Operador *</label>
                        <input type="text" name="clave_operador" id="clave_operador"
                               class="form-control <?php $__errorArgs = ['clave_operador'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('clave_operador', $ajuste->clave_operador)); ?>" required>
                        <?php $__errorArgs = ['clave_operador'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="text-muted">Se autocompleta al seleccionar el operador</small>
                    </div>

                    <!-- Separador -->
                    <div class="col-12">
                        <hr class="border-2 border-secondary">
                        <h5><i class="fas fa-truck me-2"></i>Datos de la Unidad</h5>
                    </div>

                    <!-- Unidad -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidad *</label>
                        <select name="unidad_id" id="unidad_id" 
                                class="form-select <?php $__errorArgs = ['unidad_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccione una unidad</option>
                            <?php $__currentLoopData = $unidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($unidad->id); ?>" 
                                    <?php echo e(old('unidad_id', $ajuste->unidad_id) == $unidad->id ? 'selected' : ''); ?>>
                                    <?php echo e($unidad->numero_economico); ?> - <?php echo e($unidad->nombre_unidad ?? 'Sin nombre'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['unidad_id'];
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

                    <div class="col-12">
                        <hr>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-save me-2"></i> Actualizar Ajuste
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SELECT2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#operador_id').select2({
            placeholder: 'Buscar operador por nombre o clave...',
            allowClear: true,
            theme: 'bootstrap-5',
            width: '100%'
        });

        $('#unidad_id').select2({
            placeholder: 'Buscar unidad...',
            allowClear: true,
            theme: 'bootstrap-5',
            width: '100%'
        });

        function actualizarClave() {
            var select = document.getElementById('operador_id');
            var selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                var clave = selectedOption.getAttribute('data-clave') || '';
                document.getElementById('clave_operador').value = clave;
                console.log('✅ Clave actualizada a: ' + clave);
            } else {
                document.getElementById('clave_operador').value = '';
                console.log('⚠️ No hay operador seleccionado');
            }
        }

        $('#operador_id').on('change', function() {
            actualizarClave();
        });

        $('#operador_id').on('select2:select', function(e) {
            var selectedElement = e.params.data.element;
            if (selectedElement) {
                var clave = selectedElement.getAttribute('data-clave') || '';
                document.getElementById('clave_operador').value = clave;
                console.log('✅ Clave actualizada a: ' + clave);
            }
        });

        $('#operador_id').on('select2:clear', function() {
            document.getElementById('clave_operador').value = '';
        });

        if ($('#operador_id').val()) {
            actualizarClave();
        }
    });
</script>

<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        border-radius: 0.375rem;
        border-color: #ced4da;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-top: 6px;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #ced4da;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/ajustes/edit.blade.php ENDPATH**/ ?>