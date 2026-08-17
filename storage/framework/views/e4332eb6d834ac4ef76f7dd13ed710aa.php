

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
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
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
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white fw-bold">Importar Operadores (CSV)</div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('admin.importar.operadores')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Archivo CSV</label>
                            <input type="file" name="archivo" class="form-control" accept=".csv" required>
                            <div class="form-text">
                                <strong>Columnas requeridas:</strong> clave_operador, nombre_completo, zona_nombre (reyes/apaxco/citrus), activo (opcional).<br>
                                <a href="#" id="descargarPlantillaOperadores" class="small">Descargar plantilla ejemplo</a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Importar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Generar archivos CSV de ejemplo al hacer clic en los enlaces
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