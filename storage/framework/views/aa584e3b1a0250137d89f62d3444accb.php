<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'label' => null, 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false]));

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

foreach (array_filter((['name', 'label' => null, 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php $id = $attributes->get('id', str_replace(['[', ']', '.'], '_', $name)); $clave = str_replace(['[', ']'], ['.', ''], $name); ?>
<div class="<?php echo e($attributes->get('wrapper', 'mb-3')); ?>">
    <?php if($label): ?><label class="form-label" for="<?php echo e($id); ?>"><?php echo e($label); ?><?php if($required): ?> <span class="text-danger">*</span><?php endif; ?></label><?php endif; ?>
    <input id="<?php echo e($id); ?>" name="<?php echo e($name); ?>" type="<?php echo e($type); ?>" value="<?php echo e($type === 'password' ? '' : old($clave, $value)); ?>"
        <?php if($required): ?> required <?php endif; ?>
        <?php echo e($attributes->except(['wrapper', 'id'])->class(['form-control', 'is-invalid' => $errors->has($clave)])); ?>>
    <?php $__errorArgs = [$clave];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    <?php if($hint): ?><div class="form-text"><?php echo e($hint); ?></div><?php endif; ?>
</div>
<?php /**PATH C:\Users\hulis\Downloads\Pojekts\lusa-gestion-web\resources\views/components/form/input.blade.php ENDPATH**/ ?>