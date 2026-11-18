<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 28px 32px; }
    body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #1b2333; }
    .head { border-bottom: 3px solid #1d5fad; padding-bottom: 8px; margin-bottom: 14px; }
    .marca { font-size: 22px; font-weight: bold; font-style: italic; color: #1d5fad; }
    .franja { height: 4px; background: #e8a814; width: 90px; margin-top: 2px; }
    .titulo { font-size: 14px; font-weight: bold; margin: 0; }
    .folio { float: right; text-align: right; color: #4a5468; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    th, td { border: 1px solid #e3e7ee; padding: 6px 8px; text-align: left; vertical-align: top; }
    th { background: #eaf1fa; color: #164a87; width: 30%; font-weight: bold; }
    .firmas td { text-align: center; height: 110px; vertical-align: bottom; border: 0; }
    .firmas img { max-height: 70px; max-width: 160px; }
    .linea { border-top: 1px solid #1b2333; padding-top: 4px; font-size: 10px; }
    .pie { position: fixed; bottom: -10px; left: 0; right: 0; font-size: 9px; color: #7a8496; text-align: center; }
</style>
</head>
<body>
<div class="head">
    <div class="folio">Folio <strong><?php echo e($documento->folio); ?></strong><br><?php echo e($documento->fecha?->format('d/m/Y')); ?> <?php echo e(substr((string) $documento->hora, 0, 5)); ?></div>
    <div class="marca">LUSA</div><div class="franja"></div>
    <p class="titulo">Formato de servicio y analisis de equipos tecnologicos</p>
</div>
<table>
    <tr><th>Unidad</th><td><?php echo e($documento->unidad?->etiqueta); ?></td></tr>
    <tr><th>Zona</th><td><?php echo e(ucfirst($documento->unidad?->zona?->nombre ?? '-')); ?></td></tr>
    <tr><th>Operador</th><td><?php echo e($documento->operador ? $documento->operador->nombre_completo . ' (' . $documento->operador->clave_operador . ')' : '-'); ?></td></tr>
    <tr><th>Tecnologia reportada</th><td><?php echo e(collect($documento->tecnologias)->map(fn ($t) => \App\Models\DocumentoMantenimiento::TECNOLOGIAS[$t] ?? $t)->implode(', ')); ?></td></tr>
    <tr><th>Estado de camaras</th><td><?php echo e($documento->estado_camaras ?? '-'); ?></td></tr>
    <tr><th>Prueba de barras</th><td><?php echo e($documento->prueba_barras ?? '-'); ?></td></tr>
    <tr><th>Comentarios</th><td><?php echo e($documento->comentarios ?? '-'); ?></td></tr>
    <tr><th>Adeudos</th><td><?php echo e($documento->veces_adeudo); ?> <?php echo e($documento->observaciones_adeudo); ?></td></tr>
</table>
<table class="firmas"><tr>
    <?php $__currentLoopData = \App\Models\DocumentoMantenimiento::FIRMAS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo => $etiqueta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <td><?php if($f = $documento->firma($tipo)): ?><img src="<?php echo e($f->data_uri); ?>" alt=""><br><?php endif; ?><div class="linea"><?php echo e($etiqueta); ?></div></td>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tr></table>
<div class="pie">Documento generado por Lusa Gestion el <?php echo e(now()->format('d/m/Y H:i')); ?></div>
</body>
</html>
<?php /**PATH C:\Users\hulis\Downloads\Pojekts\lusa-gestion-web\resources\views/admin/documentos/plantilla_mantenimiento.blade.php ENDPATH**/ ?>