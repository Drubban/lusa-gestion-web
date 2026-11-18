<?php $__env->startSection('title', 'Importar datos'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Importar datos','subtitle' => 'Carga masiva desde CSV (separado por coma o punto y coma, UTF-8)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Importar datos','subtitle' => 'Carga masiva desde CSV (separado por coma o punto y coma, UTF-8)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

<?php if($errores = session('errores')): ?>
    <?php if(count($errores)): ?>
    <div class="card mb-3"><div class="card-header">Avisos de la importacion <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['tone' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tone' => 'warning']); ?><?php echo e(count($errores)); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?></div>
        <div class="card-body small" style="max-height:240px;overflow:auto"><ul class="mb-0 ps-3"><?php $__currentLoopData = $errores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<div class="row g-3">
    <?php $__currentLoopData = [
        ['unidades', 'Unidades', 'bi-bus-front', 'numero_economico, zona', 'nombre_unidad, activo'],
        ['operadores', 'Operadores', 'bi-person-badge', 'clave_operador, nombre_completo', 'numero_economico (asigna unidad), activo'],
        ['tecnologias', 'Tecnologias', 'bi-cpu', 'numero_economico, tipo', 'nombre, activo y los campos del tipo'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ruta, $titulo, $icono, $req, $opc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-lg-4">
            <form method="POST" action="<?php echo e(route('admin.importar.' . $ruta)); ?>" enctype="multipart/form-data" class="card h-100">
                <?php echo csrf_field(); ?>
                <div class="card-body">
                    <span class="stat-icon tone-primary mb-3"><i class="bi <?php echo e($icono); ?>"></i></span>
                    <h2 class="h5"><?php echo e($titulo); ?></h2>
                    <p class="small text-muted mb-1"><strong>Obligatorias:</strong> <?php echo e($req); ?></p>
                    <p class="small text-muted"><strong>Opcionales:</strong> <?php echo e($opc); ?></p>
                    <input type="file" name="archivo" accept=".csv,.txt" class="form-control" required>
                    <?php if($ruta === 'tecnologias'): ?>
                        <a href="<?php echo e(route('admin.importar.plantilla.tecnologias')); ?>" class="small d-inline-block mt-2"><i class="bi bi-download me-1"></i>Descargar plantilla</a>
                    <?php endif; ?>
                </div>
                <div class="card-body border-top"><button class="btn btn-primary w-100"><i class="bi bi-upload me-1"></i>Importar <?php echo e(strtolower($titulo)); ?></button></div>
            </form>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\Downloads\Pojekts\lusa-gestion-web\resources\views/admin/importacion/index.blade.php ENDPATH**/ ?>