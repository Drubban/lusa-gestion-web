
<?php $__env->startSection('content'); ?>
<h1>Exportar Códigos QR</h1>
<div class="row">
    <?php $__currentLoopData = $unidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-3 text-center mb-4">
        <div class="card" style="width: 9cm; height: 9cm; margin: auto;">
            <div class="card-body">
                <img src="<?php echo e(route('admin.qr.generar', $unidad->id)); ?>" style="width: 7cm; height: 7cm;">
                <p class="mt-2"><?php echo e($unidad->numero_economico); ?><br><?php echo e($unidad->nombre_unidad); ?></p>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<a href="<?php echo e(route('admin.qr.descargar-pdf')); ?>" class="btn btn-success">Descargar todos (PDF)</a>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/qr/exportar.blade.php ENDPATH**/ ?>