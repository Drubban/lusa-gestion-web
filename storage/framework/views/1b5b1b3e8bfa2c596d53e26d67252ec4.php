<!DOCTYPE html>
<html lang="es">

<style>
    /* Estilos para la paginación */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        flex-wrap: wrap;
        margin: 0;
        padding: 0;
    }

    .pagination .page-item {
        list-style: none;
    }

    .pagination .page-link {
        padding: 8px 12px;
        font-size: 14px;
        border-radius: 8px;
        color: #0d6efd;
        background-color: #fff;
        border: 1px solid #dee2e6;
        text-decoration: none;
        transition: all 0.2s;
    }

    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .pagination .active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    .pagination .disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }

    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        padding: 8px 16px;
    }

    .nav .nav-link {
        padding: 10px 15px;
        border-radius: 5px;
        transition: all 0.3s;
    }

    .nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .nav .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Admin Lusa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-dark sidebar" style="min-height: 100vh;">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.dashboard')); ?>">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>

                        <!-- Unidades -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.unidades.index')); ?>">
                                <i class="fas fa-bus"></i> Unidades
                            </a>
                        </li>

                        <!-- Operadores -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.operadores.index')); ?>">
                                <i class="fas fa-users"></i> Operadores
                            </a>
                        </li>

                        <!-- Documentos -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.documentos-mantenimiento.index')); ?>">
                                <i class="fas fa-clipboard-list"></i> Mantenimiento
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.mantenimiento.dashboard')); ?>">
                                <i class="fas fa-calendar-check"></i> Tablero de Mantenimiento
                            </a>
                        </li>
                       
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.documentos-capacitacion.index')); ?>">
                                <i class="fas fa-graduation-cap"></i> Capacitación
                            </a>
                        </li>

                        <!-- Movimientos -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.movimientos.index')); ?>">
                                <i class="fas fa-exchange-alt"></i> Entradas/Salidas
                            </a>
                        </li>

                        <!-- Usuarios App -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.usuarios-app.index')); ?>">
                                <i class="fas fa-mobile-alt"></i> Usuarios App
                            </a>
                        </li>

                        <!-- QR -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.qr.exportar')); ?>">
                                <i class="fas fa-qrcode"></i> Exportar QR
                            </a>
                        </li>

                        <!-- Importar -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.importar.index')); ?>">
                                <i class="fas fa-upload"></i> Importar datos
                            </a>
                        </li>

                        <!-- Inventario -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.inventario.index')); ?>">
                                <i class="fas fa-boxes"></i> Inventario
                            </a>
                        </li>

                        <!-- Ajustes -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.ajustes.index')); ?>">
                                <i class="fas fa-table"></i> Ajustes
                            </a>
                        </li>

                        <!-- 🔥 TECNOLOGÍAS - ENLACES INDIVIDUALES -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.tecnologias.index', ['tipo' => 'barras'])); ?>">
                                <i class="fas fa-barcode"></i> Tecnología Barras
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.tecnologias.index', ['tipo' => 'telpo'])); ?>">
                                <i class="fas fa-mobile-alt"></i> Tecnología Telpo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.tecnologias.index', ['tipo' => 'gps'])); ?>">
                                <i class="fas fa-satellite"></i> Tecnología GPS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo e(route('admin.tecnologias.index', ['tipo' => 'mdvr'])); ?>">
                                <i class="fas fa-video"></i> Tecnología MDVR
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="col-md-10 ms-sm-auto px-md-4">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
</body>

</html><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>