<?php
    $menu = [
        'Operacion' => [
            ['admin.dashboard', 'admin.dashboard', 'bi-grid-1x2', 'Inicio'],
            ['admin.movimientos.index', 'admin.movimientos.*', 'bi-arrow-left-right', 'Entradas y salidas'],
            ['admin.mantenimiento.index', 'admin.mantenimiento.*', 'bi-speedometer2', 'Estado de mantenimiento'],
            ['admin.agendamientos.index', 'admin.agendamientos.*', 'bi-calendar2-check', 'Agendamientos'],
            ['admin.revisiones-optocontrol.index', 'admin.revisiones-optocontrol.*', 'bi-tools', 'Revisiones optocontrol'],
        ],
        'Documentos' => [
            ['admin.documentos-mantenimiento.index', 'admin.documentos-mantenimiento.*', 'bi-file-earmark-text', 'Mantenimiento'],
            ['admin.documentos-capacitacion.index', 'admin.documentos-capacitacion.*', 'bi-mortarboard', 'Capacitacion'],
            ['admin.ajustes.index', 'admin.ajustes.*', 'bi-receipt', 'Ajustes'],
        ],
        'Catalogos' => [
            ['admin.unidades.index', 'admin.unidades.*', 'bi-bus-front', 'Unidades'],
            ['admin.operadores.index', 'admin.operadores.*', 'bi-person-badge', 'Operadores'],
            ['admin.tecnologias.index', 'admin.tecnologias.*', 'bi-cpu', 'Tecnologias'],
            ['admin.inventario.index', 'admin.inventario.*', 'bi-box-seam', 'Inventario de sistemas'],
            ['admin.qr.index', 'admin.qr.*', 'bi-qr-code', 'Codigos QR'],
        ],
    ];
    if (auth()->user()->esAdmin()) {
        $menu['Administracion'] = [
            ['admin.usuarios-app.index', 'admin.usuarios-app.*', 'bi-people', 'Usuarios'],
            ['admin.importar.index', 'admin.importar.*', 'bi-upload', 'Importar datos'],
        ];
    }
?>

<?php $__env->startSection('body'); ?>
<aside class="sidebar" aria-label="Menu principal">
    <a href="<?php echo e(route('admin.dashboard')); ?>" class="brand">
        <span class="brand-mark">LUSA</span>
        <span class="brand-sub">Gestion<br>de sistemas</span>
    </a>
    <?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seccion => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="nav-section"><?php echo e($seccion); ?></div>
        <ul class="nav-lusa">
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ruta, $patron, $icono, $texto]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><a href="<?php echo e(route($ruta)); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['activo' => request()->routeIs($patron)]); ?>"><i class="bi <?php echo e($icono); ?>"></i><?php echo e($texto); ?></a></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <div class="p-3"></div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-menu" data-toggle-sidebar aria-label="Abrir menu"><i class="bi bi-list"></i></button>
            <span class="text-muted small d-none d-md-inline"><?php echo e(now()->translatedFormat('l d \\d\\e F, Y')); ?></span>
        </div>
        <div class="dropdown">
            <button class="btn btn-ghost d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="avatar"><?php echo e(strtoupper(mb_substr(auth()->user()->nombre_visible, 0, 2))); ?></span>
                <span class="text-start d-none d-sm-block lh-sm">
                    <span class="d-block fw-semibold"><?php echo e(auth()->user()->nombre_visible); ?></span>
                    <span class="d-block small text-muted"><?php echo e(auth()->user()->rol->etiqueta()); ?></span>
                </span>
                <i class="bi bi-chevron-down small text-muted"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li>
                    <form method="POST" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?>
                        <button class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesion</button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <main class="content">
        <?php if(session('success')): ?>
            <div class="alert alert-success d-flex gap-2 align-items-start"><i class="bi bi-check-circle"></i><div><?php echo e(session('success')); ?></div></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger d-flex gap-2 align-items-start"><i class="bi bi-exclamation-circle"></i><div><?php echo e(session('error')); ?></div></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1"><i class="bi bi-exclamation-circle me-1"></i>Revisa los siguientes puntos</div>
                <ul class="mb-0 ps-3"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\Downloads\Pojekts\lusa-gestion-web\resources\views/layouts/app.blade.php ENDPATH**/ ?>