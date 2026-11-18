<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Volver']));

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

foreach (array_filter((['title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Volver']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="page-head">
    <div>
        <?php if($back): ?>
            <div class="breadcrumb-lite"><a href="<?php echo e($back); ?>"><i class="bi bi-arrow-left me-1"></i><?php echo e($backLabel); ?></a></div>
        <?php endif; ?>
        <h1><?php echo e($title); ?></h1>
        <?php if($subtitle): ?><p><?php echo e($subtitle); ?></p><?php endif; ?>
    </div>
    <?php if(isset($actions)): ?>
        <div class="d-flex flex-wrap gap-2"><?php echo e($actions); ?></div>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\hulis\Downloads\Pojekts\lusa-gestion-web\resources\views/components/page-header.blade.php ENDPATH**/ ?>