    <div
        wire:key="<?php echo e($uuid); ?>"
        <?php echo e($attributes->whereDoesntStartWith('class')); ?>

        <?php echo e($attributes->class(['alert rounded-md', 'shadow-md' => $shadow])); ?>

        x-data="{ show: true }" x-show="show"
    >
        <?php if($icon): ?>
            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => $icon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'self-center']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
        <?php endif; ?>

        <?php if($title): ?>
            <div>
                <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["font-bold" => $description]); ?>"><?php echo e($title); ?></div>
                <div class="text-xs"><?php echo e($description); ?></div>
            </div>
        <?php else: ?>
            <span><?php echo e($slot); ?></span>
        <?php endif; ?>

        <div class="flex items-center gap-3">
            <?php echo e($actions); ?>

        </div>

        <?php if($dismissible): ?>
            <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-x-mark'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'show = false','class' => 'btn-xs btn-circle btn-ghost static self-start end-0']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
        <?php endif; ?>
<<<<<<< HEAD:storage/framework/views/945ecfc1f43e95e2a953252fa1776876.php
    </div><?php /**PATH /home/ashart20/FETNET/storage/framework/views/d59c58b0719840e6368af143f15091b8.blade.php ENDPATH**/ ?>
=======
    </div><?php /**PATH /Volumes/EXCHANGE/WebDev/NewFet/storage/framework/views/d59c58b0719840e6368af143f15091b8.blade.php ENDPATH**/ ?>
>>>>>>> 37932502ce1771fd5d973efa3d1d4af0ca2dafb8:storage/framework/views/09fceb36033e09c9236065a515ff2dec.php
