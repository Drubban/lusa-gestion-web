<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Importación masiva de datos</h1>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if(session('errores') && count(session('errores')) > 0): ?>
        <div class="alert alert-warning">
            <strong>Errores encontrados:</strong>
            <ul><?php $__currentLoopData = session('errores'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($error); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Unidades -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white fw-bold">Importar Unidades (CSV)</div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('admin.importar.unidades')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Archivo CSV</label>
                            <input type="file" name="archivo" class="form-control" accept=".csv" required>
                            <div class="form-text">
                                <strong>Columnas requeridas:</strong> numero_economico, zona (reyes/apaxco/citrus).<br>
                                Opcionales: nombre_unidad, activo.<br>
                                <a href="#" id="descargarPlantillaUnidades">Descargar plantilla ejemplo</a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Importar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Operadores -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white fw-bold">Importar Operadores (CSV)</div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('admin.importar.operadores')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Archivo CSV</label>
                            <input type="file" name="archivo" class="form-control" accept=".csv" required>
                            <div class="form-text">
                                <strong>Columnas requeridas:</strong> clave_operador, nombre_completo, zona_nombre (reyes/apaxco/citrus).<br>
                                Opcionales: activo.<br>
                                <a href="#" id="descargarPlantillaOperadores" class="small">Descargar plantilla ejemplo</a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Importar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 🔥 TECNOLOGÍAS -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white fw-bold text-primary">
                    <i class="fas fa-microchip me-2"></i>Importar Tecnologías (CSV)
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('admin.importar.tecnologias')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Archivo CSV</label>
                            <input type="file" name="archivo" class="form-control" accept=".csv" required>
                            <div class="form-text">
                                <strong>Columnas requeridas:</strong> numero_economico, tipo (barras/telpo/gps/mdvr).<br>
                                <strong>Columnas opcionales según tipo:</strong>
                                <ul class="small mt-1 mb-0">
                                    <li><strong>Barras:</strong> id_barra, barras, telefono_barras, plan_barras</li>
                                    <li><strong>Telpo:</strong> imei_antes, v_apk, telpo, imei_telpo, telefono_telpo, plan_telpo, costo_plan</li>
                                    <li><strong>GPS:</strong> imei_gps, telefono_gps, plan_gps</li>
                                    <li><strong>MDVR:</strong> dvr, modelo, camaras, memoria</li>
                                </ul>
                                <a href="<?php echo e(route('admin.importar.plantilla.tecnologias')); ?>" class="small" target="_blank">
                                    <i class="fas fa-download me-1"></i>Descargar plantilla ejemplo
                                </a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-upload me-2"></i>Importar Tecnologías
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Información adicional sobre tipos de tecnología -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-info-circle me-2"></i>Tipos de Tecnología
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Campos requeridos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-primary">barras</span></td>
                                    <td>Equipo de barras / lectores</td>
                                    <td>id_barra, barras, telefono_barras, plan_barras</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">telpo</span></td>
                                    <td>Equipo Telpo (terminales de pago)</td>
                                    <td>imei_antes, v_apk, telpo, imei_telpo, telefono_telpo, plan_telpo, costo_plan</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">gps</span></td>
                                    <td>Equipo GPS (localización)</td>
                                    <td>imei_gps, telefono_gps, plan_gps</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">mdvr</span></td>
                                    <td>Equipo MDVR (grabación de video)</td>
                                    <td>dvr, modelo, camaras, memoria</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Generar archivos CSV de ejemplo
document.getElementById('descargarPlantillaUnidades').addEventListener('click', function(e) {
    e.preventDefault();
    const contenido = "numero_economico,nombre_unidad,zona,activo\nECO-001,Unidad Centro,reyes,1\nECO-002,Unidad Norte,apaxco,1\nECO-003,Unidad Sur,citrus,0";
    const blob = new Blob([contenido], {type: 'text/csv;charset=utf-8;'});
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.href = url;
    link.setAttribute('download', 'plantilla_unidades.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
});

document.getElementById('descargarPlantillaOperadores').addEventListener('click', function(e) {
    e.preventDefault();
    const contenido = "clave_operador,nombre_completo,zona_nombre,activo\n10001,Juan Pérez,reyes,1\n10002,María López,apaxco,1\n10003,Carlos Ruiz,citrus,0";
    const blob = new Blob([contenido], {type: 'text/csv;charset=utf-8;'});
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.href = url;
    link.setAttribute('download', 'plantilla_operadores.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/importacion/index.blade.php ENDPATH**/ ?>