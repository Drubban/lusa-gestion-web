<?php $__env->startSection('title', $documento->folio); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $documento->folio,'subtitle' => 'Documento de mantenimiento','back' => route('admin.documentos-mantenimiento.index'),'backLabel' => 'Documentos']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($documento->folio),'subtitle' => 'Documento de mantenimiento','back' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.documentos-mantenimiento.index')),'back-label' => 'Documentos']); ?>
     <?php $__env->slot('actions', null, []); ?> 
        <a href="<?php echo e(route('admin.documentos-mantenimiento.pdf', $documento)); ?>" class="btn btn-light"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        <a href="<?php echo e(route('admin.documentos-mantenimiento.word', $documento)); ?>" class="btn btn-light"><i class="bi bi-file-earmark-word me-1"></i>Word</a>
        <a href="<?php echo e(route('admin.documentos-mantenimiento.edit', $documento)); ?>" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
     <?php $__env->endSlot(); ?>
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
<div class="row g-3">
    <div class="col-lg-7"><div class="card"><div class="card-body">
        <?php if (isset($component)) { $__componentOriginal64fbf62b41da3c17a653bead84176a19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal64fbf62b41da3c17a653bead84176a19 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dl','data' => ['items' => [
            'Unidad' => $documento->unidad?->etiqueta,
            'Zona' => ucfirst($documento->unidad?->zona?->nombre ?? ''),
            'Operador' => $documento->operador ? $documento->operador->nombre_completo . ' (' . $documento->operador->clave_operador . ')' : null,
            'Fecha y hora' => $documento->fecha?->format('d/m/Y') . ' ' . substr((string) $documento->hora, 0, 5),
            'Estado de camaras' => $documento->estado_camaras,
            'Prueba de barras' => $documento->prueba_barras,
            'Comentarios' => $documento->comentarios,
            'Adeudos' => $documento->veces_adeudo . ($documento->observaciones_adeudo ? ' - ' . $documento->observaciones_adeudo : ''),
            'Capturado por' => $documento->usuario?->nombre_visible,
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dl'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            'Unidad' => $documento->unidad?->etiqueta,
            'Zona' => ucfirst($documento->unidad?->zona?->nombre ?? ''),
            'Operador' => $documento->operador ? $documento->operador->nombre_completo . ' (' . $documento->operador->clave_operador . ')' : null,
            'Fecha y hora' => $documento->fecha?->format('d/m/Y') . ' ' . substr((string) $documento->hora, 0, 5),
            'Estado de camaras' => $documento->estado_camaras,
            'Prueba de barras' => $documento->prueba_barras,
            'Comentarios' => $documento->comentarios,
            'Adeudos' => $documento->veces_adeudo . ($documento->observaciones_adeudo ? ' - ' . $documento->observaciones_adeudo : ''),
            'Capturado por' => $documento->usuario?->nombre_visible,
        ])]); ?>
            <dt>Tecnologia</dt><dd><?php $__currentLoopData = $documento->tecnologias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['tone' => 'primary','class' => 'me-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tone' => 'primary','class' => 'me-1']); ?><?php echo e(\App\Models\DocumentoMantenimiento::TECNOLOGIAS[$t] ?? $t); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></dd>
            <dt>Estado</dt><dd><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['tone' => $documento->vigente ? 'success' : 'muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($documento->vigente ? 'success' : 'muted')]); ?><?php echo e($documento->vigente ? 'Vigente' : 'No vigente'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?></dd>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal64fbf62b41da3c17a653bead84176a19)): ?>
<?php $attributes = $__attributesOriginal64fbf62b41da3c17a653bead84176a19; ?>
<?php unset($__attributesOriginal64fbf62b41da3c17a653bead84176a19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal64fbf62b41da3c17a653bead84176a19)): ?>
<?php $component = $__componentOriginal64fbf62b41da3c17a653bead84176a19; ?>
<?php unset($__componentOriginal64fbf62b41da3c17a653bead84176a19); ?>
<?php endif; ?>
    </div></div></div>
    <div class="col-lg-5"><div class="card"><div class="card-body">
        <div class="card-title-sm">Firmas</div>
        <?php $__currentLoopData = \App\Models\DocumentoMantenimiento::FIRMAS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo => $etiqueta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $firma = $documento->firma($tipo); ?>
            <div class="mb-3 pb-3 border-bottom">
                <div class="small text-muted mb-1"><?php echo e($etiqueta); ?></div>
                <?php if($firma): ?>
                    <img src="<?php echo e($firma->data_uri); ?>" class="firma-img" alt="<?php echo e($etiqueta); ?>">
                    <?php if (! ($firma->integridadValida())): ?><div class="small text-danger"><i class="bi bi-shield-exclamation"></i> La firma no coincide con su huella original.</div><?php endif; ?>
                <?php else: ?>
                    <span class="text-muted small">Sin firma</span>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div></div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\Downloads\Pojekts\lusa-gestion-web\resources\views/admin/documentos/mantenimiento/show.blade.php ENDPATH**/ ?>