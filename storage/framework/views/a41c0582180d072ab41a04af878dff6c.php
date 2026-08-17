<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Conformidad de Capacitación</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .container { width: 100%; margin: auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { width: 100%; border-collapse: collapse; }
        .info td { padding: 6px; border: 1px solid #000; vertical-align: top; }
        .info td.label { font-weight: bold; width: 30%; background: #f2f2f2; }
        .firma { margin-top: 30px; width: 100%; }
        .firma td { text-align: center; vertical-align: bottom; width: 50%; }
        .firma .linea { border-top: 1px solid #000; width: 80%; margin: 8px auto 0 auto; }
        .firma img { max-width: 150px; max-height: 50px; }
        footer { text-align: center; font-size: 10px; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Conformidad de Capacitación</h2>
    </div>

    <table class="info">
        <tr><td class="label">Unidad:</td><td><?php echo e($documento->asignacion->unidad->numero_economico); ?> - <?php echo e($documento->asignacion->unidad->nombre_unidad); ?></td></tr>
        <tr><td class="label">Zona:</td><td><?php echo e($documento->asignacion->unidad->zona->nombre ?? 'N/A'); ?></td></tr>
        <tr><td class="label">Operador:</td><td><?php echo e($documento->asignacion->operador->nombre_completo); ?></td></tr>
        <tr><td class="label">Clave operador:</td><td><?php echo e($documento->asignacion->operador->clave_operador); ?></td></tr>
        <tr><td class="label">Fecha / Hora:</td><td><?php echo e(\Carbon\Carbon::parse($documento->fecha)->format('d/m/Y')); ?> - <?php echo e($documento->hora); ?></td></tr>
    </table>

    <table class="firma">
        <tr>
            <td style="width:50%">
                <?php if($documento->firma_operador): ?>
                    <img src="data:image/png;base64,<?php echo e($documento->firma_operador); ?>" alt="Firma operador">
                <?php endif; ?>
                <div class="linea"></div>
                Firma del operador
            </td>
            <td style="width:50%">
                <?php if($documento->firma_ing): ?>
                    <img src="data:image/png;base64,<?php echo e($documento->firma_ing); ?>" alt="Firma Ing.">
                <?php endif; ?>
                <div class="linea"></div>
                Firma del Ing. a cargo
            </td>
        </tr>
    </table>

    <footer>Documento generado por sistema Lusa - <?php echo e(now()->format('d/m/Y H:i')); ?></footer>
</div>
</body>
</html><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/documentos/plantilla_capacitacion.blade.php ENDPATH**/ ?>