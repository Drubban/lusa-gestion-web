<?php $__env->startSection('title', 'Iniciar sesion'); ?>

<?php $__env->startSection('body'); ?>
<div class="auth-wrap">
    <section class="auth-side">
        <div>
            <span class="brand-mark" style="font-size:2.4rem">LUSA</span>
            <p class="mt-3 text-muted" style="max-width:340px">Control de unidades, operadores, documentos e inventario del departamento de sistemas.</p>
        </div>
        <p class="small text-muted mb-0">Acceso exclusivo para personal autorizado.</p>
    </section>
    <section class="auth-form">
        <div class="inner">
            <h1 class="h3 mb-1">Bienvenido</h1>
            <p class="text-muted mb-4">Ingresa con tu usuario del sistema.</p>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger small"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login.store')); ?>" autocomplete="on">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label" for="nombre_usuario">Usuario</label>
                    <input class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?php echo e(old('nombre_usuario')); ?>" required autofocus autocomplete="username" maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Contrasena</label>
                    <input class="form-control" id="password" type="password" name="password" required autocomplete="current-password">
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="recordar" id="recordar" value="1">
                    <label class="form-check-label small" for="recordar">Mantener sesion en este equipo</label>
                </div>
                <button class="btn btn-primary w-100 py-2">Entrar</button>
            </form>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\Downloads\Pojekts\lusa-gestion-web\resources\views/auth/login.blade.php ENDPATH**/ ?>