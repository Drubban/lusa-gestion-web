<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label', 'value', 'icon' => 'bi-circle', 'tone' => 'primary', 'href' => null, 'hint' => null]));

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

foreach (array_filter((['label', 'value', 'icon' => 'bi-circle', 'tone' => 'primary', 'href' => null, 'hint' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php $tag = $href ? 'a' : 'div'; ?>
<<?php echo e($tag); ?> <?php if($href): ?> href="<?php echo e($href); ?>" <?php endif; ?> <?php echo e($attributes->class('card stat text-reset')); ?>>
    <span class="stat-icon tone-<?php echo e($tone); ?>"><i class="bi <?php echo e($icon); ?>"></i></span>
    <span>
        <span class="stat-value d-block"><?php echo e($value); ?></span>
        <span class="stat-label"><?php echo e($label); ?></span>
        <?php if($hint): ?><span class="d-block small text-muted"><?php echo e($hint); ?></span><?php endif; ?>
    </span>
</<?php echo e($tag); ?>>
<?php /**PATH C:\Users\hulis\Downloads\Pojekts\lusa-gestion-web\resources\views/components/stat.blade.php ENDPATH**/ ?>