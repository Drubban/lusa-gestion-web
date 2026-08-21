

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Agregar Tecnología a Unidad</h1>
        <a href="<?php echo e(route('admin.tecnologias.index')); ?>" class="btn btn-secondary rounded-pill px-4">
            Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(route('admin.tecnologias.store')); ?>" id="tecnologiaForm">
                <?php echo csrf_field(); ?>

                <div class="row g-4">
                    <!-- Unidad -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidad *</label>
                        <select name="unidad_id" class="form-select <?php $__errorArgs = ['unidad_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccione una unidad</option>
                            <?php $__currentLoopData = $unidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($unidad->id); ?>" <?php echo e(old('unidad_id') == $unidad->id ? 'selected' : ''); ?>>
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

                    <!-- Tipo -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tipo de Tecnología *</label>
                        <select name="tipo" id="tipo" class="form-select <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccione un tipo</option>
                            <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $nombre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(old('tipo') == $key ? 'selected' : ''); ?>>
                                    <?php echo e($nombre); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['tipo'];
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

                    <!-- Nombre -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" name="nombre" 
                               class="form-control <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('nombre')); ?>" placeholder="Nombre descriptivo">
                        <?php $__errorArgs = ['nombre'];
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

                    <!-- Activo -->
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" 
                                   class="form-check-input" id="activo" 
                                   value="1" <?php echo e(old('activo', true) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="activo">
                                <i class="fas fa-check-circle me-1"></i> Activo
                            </label>
                        </div>
                    </div>

                    <!-- Campos dinámicos según tipo -->
                    <div class="col-12">
                        <hr class="border-2 border-secondary">
                        <h5 id="titulo-campos"><i class="fas fa-microchip me-2"></i>Datos Específicos</h5>
                    </div>

                    <!-- 🔥 BARRAS -->
                    <div id="campos-barras" class="row g-3" style="display: none;">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-barcode me-2"></i>Datos de Barras</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ID Barra</label>
                            <input type="text" name="id_barra" class="form-control" value="<?php echo e(old('id_barra')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Barras</label>
                            <input type="text" name="barras" class="form-control" value="<?php echo e(old('barras')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono_barras" class="form-control" value="<?php echo e(old('telefono_barras')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Plan</label>
                            <input type="text" name="plan_barras" class="form-control" value="<?php echo e(old('plan_barras')); ?>">
                        </div>
                    </div>

                    <!-- 🔥 TELPO -->
                    <div id="campos-telpo" class="row g-3" style="display: none;">
                        <div class="col-12">
                            <h6 class="text-success"><i class="fas fa-mobile-alt me-2"></i>Datos de Telpo</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IMEI Antes</label>
                            <input type="text" name="imei_antes" class="form-control" value="<?php echo e(old('imei_antes')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">V. APK</label>
                            <input type="text" name="v_apk" class="form-control" value="<?php echo e(old('v_apk')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Telpo</label>
                            <input type="text" name="telpo" class="form-control" value="<?php echo e(old('telpo')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IMEI Telpo</label>
                            <input type="text" name="imei_telpo" class="form-control" value="<?php echo e(old('imei_telpo')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono_telpo" class="form-control" value="<?php echo e(old('telefono_telpo')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Plan</label>
                            <input type="text" name="plan_telpo" class="form-control" value="<?php echo e(old('plan_telpo')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Costo del Plan</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="costo_plan" class="form-control" value="<?php echo e(old('costo_plan')); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- 🔥 GPS -->
                    <div id="campos-gps" class="row g-3" style="display: none;">
                        <div class="col-12">
                            <h6 class="text-warning"><i class="fas fa-satellite me-2"></i>Datos de GPS</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IMEI GPS</label>
                            <input type="text" name="imei_gps" class="form-control" value="<?php echo e(old('imei_gps')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono_gps" class="form-control" value="<?php echo e(old('telefono_gps')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Plan</label>
                            <input type="text" name="plan_gps" class="form-control" value="<?php echo e(old('plan_gps')); ?>">
                        </div>
                    </div>

                    <!-- 🔥 MDVR -->
                    <div id="campos-mdvr" class="row g-3" style="display: none;">
                        <div class="col-12">
                            <h6 class="text-danger"><i class="fas fa-video me-2"></i>Datos de MDVR</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">DVR</label>
                            <input type="text" name="dvr" class="form-control" value="<?php echo e(old('dvr')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" name="modelo" class="form-control" value="<?php echo e(old('modelo')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cámaras</label>
                            <input type="text" name="camaras" class="form-control" value="<?php echo e(old('camaras')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Memoria</label>
                            <input type="text" name="memoria" class="form-control" value="<?php echo e(old('memoria')); ?>">
                        </div>
                    </div>

                    <div class="col-12">
                        <hr>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-save me-2"></i> Guardar Tecnología
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo');
        const camposBarras = document.getElementById('campos-barras');
        const camposTelpo = document.getElementById('campos-telpo');
        const camposGps = document.getElementById('campos-gps');
        const camposMdvr = document.getElementById('campos-mdvr');
        const tituloCampos = document.getElementById('titulo-campos');

        function toggleCampos() {
            const tipo = tipoSelect.value;
            
            // Ocultar todos
            camposBarras.style.display = 'none';
            camposTelpo.style.display = 'none';
            camposGps.style.display = 'none';
            camposMdvr.style.display = 'none';

            // Mostrar según tipo
            if (tipo === 'barras') {
                camposBarras.style.display = 'flex';
                tituloCampos.textContent = '';
            } else if (tipo === 'telpo') {
                camposTelpo.style.display = 'flex';
                tituloCampos.textContent = '';
            } else if (tipo === 'gps') {
                camposGps.style.display = 'flex';
                tituloCampos.textContent = '';
            } else if (tipo === 'mdvr') {
                camposMdvr.style.display = 'flex';
                tituloCampos.textContent = '';
            } else {
                tituloCampos.textContent = 'Seleccione un tipo de tecnología';
            }
        }

        // Ejecutar al cargar y al cambiar
        toggleCampos();
        tipoSelect.addEventListener('change', toggleCampos);
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/tecnologias/create.blade.php ENDPATH**/ ?>