<?php extract((new \Illuminate\Support\Collection($attributes->getAttributes()))->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['class','xBind:class']));

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

foreach (array_filter((['class','xBind:class']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<?php if (isset($component)) { $__componentOriginald9303dabbda8ab722ffa8a71380c07e0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald9303dabbda8ab722ffa8a71380c07e0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'tallstack-ui::components.icon.heroicons.solid.x-mark','data' => ['class' => $class,'xBind:class' => $xBindClass]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('tallstack-ui::icon.heroicons.solid.x-mark'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($class),'x-bind:class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($xBindClass)]); ?>

<?php echo e($slot ?? ""); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald9303dabbda8ab722ffa8a71380c07e0)): ?>
<?php $attributes = $__attributesOriginald9303dabbda8ab722ffa8a71380c07e0; ?>
<?php unset($__attributesOriginald9303dabbda8ab722ffa8a71380c07e0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald9303dabbda8ab722ffa8a71380c07e0)): ?>
<?php $component = $__componentOriginald9303dabbda8ab722ffa8a71380c07e0; ?>
<?php unset($__componentOriginald9303dabbda8ab722ffa8a71380c07e0); ?>
<?php endif; ?><?php /**PATH /Volumes/EXCHANGE/WebDev/FetAS/storage/framework/views/19156b40e22511c7dffbc142c15ce727.blade.php ENDPATH**/ ?>