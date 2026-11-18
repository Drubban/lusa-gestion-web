<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['action', 'confirm' => 'Esta accion no se puede deshacer. Continuar?', 'label' => null, 'icon' => 'bi-trash3']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['action', 'confirm' => 'Esta accion no se puede deshacer. Continuar?', 'label' => null, 'icon' => 'bi-trash3']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<form method="POST" action="<?php echo e($action); ?>" class="d-inline" data-confirm="<?php echo e($confirm); ?>">
    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
    <button type="submit" <?php echo e($attributes->class($label ? 'btn btn-ghost-danger' : 'btn btn-ghost-danger btn-icon btn-sm')); ?> title="Eliminar">
        <i class="bi <?php echo e($icon); ?>"></i><?php if($label): ?><span class="ms-1"><?php echo e($label); ?></span><?php endif; ?>
    </button>
</form>
<?php /**PATH C:\Users\hulis\Downloads\Pojekts\lusa-gestion-web\resources\views/components/delete-button.blade.php ENDPATH**/ ?>