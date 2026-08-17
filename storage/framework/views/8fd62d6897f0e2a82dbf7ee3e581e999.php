

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Detalle de Usuario de App</h1>
        <div>
            <a href="<?php echo e(route('admin.usuarios-app.edit', $usuario)); ?>" class="btn btn-warning rounded-pill px-4">Editar</a>
            <a href="<?php echo e(route('admin.usuarios-app.index')); ?>" class="btn btn-secondary rounded-pill px-4">Volver</a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>ID:</strong></div>
                    <p><?php echo e($usuario->id); ?></p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Nombre de usuario:</strong></div>
                    <p><?php echo e($usuario->nombre_usuario); ?></p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Departamento:</strong></div>
                    <p><?php echo e(ucfirst($usuario->departamento->nombre ?? 'N/A')); ?></p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Permisos:</strong></div>
                    <p><?php echo $usuario->puede_generar_documentos ? '<span class="badge bg-info">Genera documentos</span>' : '<span class="badge bg-secondary">Solo movimientos</span>'; ?></p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Estado:</strong></div>
                    <p><?php echo $usuario->activo ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?></p>
                </div>
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2"><strong>Registrado:</strong></div>
                    <p><?php echo e($usuario->created_at->format('d/m/Y H:i')); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/usuarios/show.blade.php ENDPATH**/ ?>