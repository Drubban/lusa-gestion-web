<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Detalle de Conformidad de Capacitación</h1>
        <div>
            <a href="<?php echo e(route('admin.documentos-capacitacion.exportar-pdf', $documento)); ?>" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="<?php echo e(route('admin.documentos-capacitacion.index')); ?>" class="btn btn-secondary rounded-pill px-4">Volver</a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white fw-semibold">Información del documento</div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border-bottom pb-1 mb-2"><strong>Unidad:</strong></div>
                            <p><?php echo e($documento->asignacion->unidad->numero_economico); ?> - <?php echo e($documento->asignacion->unidad->nombre_unidad ?? 'Sin nombre'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-1 mb-2"><strong>Zona:</strong></div>
                            <p><?php echo e($documento->asignacion->unidad->zona->nombre ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-1 mb-2"><strong>Operador:</strong></div>
                            <p><?php echo e($documento->asignacion->operador->nombre_completo); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-1 mb-2"><strong>Clave de operador:</strong></div>
                            <p><?php echo e($documento->asignacion->operador->clave_operador); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-1 mb-2"><strong>Fecha:</strong></div>
                            <p><?php echo e(\Carbon\Carbon::parse($documento->fecha)->format('d/m/Y')); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-1 mb-2"><strong>Hora:</strong></div>
                            <p><?php echo e($documento->hora); ?></p>
                        </div>
                        <div class="col-12">
                            <div class="border-bottom pb-1 mb-2"><strong>Vigente:</strong></div>
                            <p><?php echo $documento->vigente ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white fw-semibold">Firmas</div>
                <div class="card-body p-4">
                    <?php if($documento->firma_operador): ?>
                    <div class="mb-4">
                        <strong>Firma del operador:</strong>
                        <div class="border rounded p-2 mt-2 text-center bg-light">
                            <img src="data:image/png;base64,<?php echo e($documento->firma_operador); ?>" style="max-width: 100%; max-height: 120px;" alt="Firma operador">
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($documento->firma_ing): ?>
                    <div class="mb-4">
                        <strong>Firma del Ing. a cargo:</strong>
                        <div class="border rounded p-2 mt-2 text-center bg-light">
                            <img src="data:image/png;base64,<?php echo e($documento->firma_ing); ?>" style="max-width: 100%; max-height: 120px;" alt="Firma Ing.">
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Si quieres mostrar el contenido de la plantilla (por ejemplo, para vista previa) -->
    <div class="card shadow-sm border-0 rounded-4 mt-2">
        <div class="card-header bg-white fw-semibold">Vista previa del documento</div>
        <div class="card-body p-4 bg-light" style="font-size: 14px;">
            <?php echo $__env->make('admin.documentos.plantilla_capacitacion', ['documento' => $documento], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/documentos/capacitacion/show.blade.php ENDPATH**/ ?>