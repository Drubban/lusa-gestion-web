<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['column', 'label']));

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

foreach (array_filter((['column', 'label']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $activa = request('sort') === $column;
    $dir = $activa && request('direction') === 'asc' ? 'desc' : 'asc';
?>
<a href="<?php echo e(request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir, 'page' => null])); ?>">
    <?php echo e($label); ?> <?php if($activa): ?><i class="bi bi-arrow-<?php echo e(request('direction') === 'asc' ? 'up' : 'down'); ?>"></i><?php endif; ?>
</a>
<?php /**PATH C:\Users\hulis\Downloads\Pojekts\lusa-gestion-web\resources\views/components/sort-link.blade.php ENDPATH**/ ?>