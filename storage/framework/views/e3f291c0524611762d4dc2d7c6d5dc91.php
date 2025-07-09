    <details
        x-data="{open: false}"
        @click.outside="open = false"
        :open="open"
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'dropdown',
            'dropdown-end' => ($noXAnchor && $right),
            'dropdown-top' => ($noXAnchor && $top),
            'dropdown-bottom' => $noXAnchor,
        ]); ?>"
    >
        <!-- CUSTOM TRIGGER -->
        <?php if($trigger): ?>
            <summary x-ref="button" @click.prevent="open = !open" <?php echo e($trigger->attributes->class(['list-none'])); ?>>
                <?php echo e($trigger); ?>

            </summary>
        <?php else: ?>
            <!-- DEFAULT TRIGGER -->
            <summary x-ref="button" @click.prevent="open = !open" <?php echo e($attributes->class(["btn"])); ?>>
                <?php echo e($label); ?>

                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => $icon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
            </summary>
        <?php endif; ?>

        <ul
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'p-2','shadow','menu','z-[1]','border-[length:var(--border)]','border-base-content/10','bg-base-100', 'rounded-box','w-auto','min-w-max',
                'dropdown-content' => $noXAnchor,
            ]); ?>"
            @click="open = false"
            <?php if(!$noXAnchor): ?>
                x-anchor.<?php echo e($right ? 'bottom-end' : 'bottom-start'); ?>="$refs.button"
            <?php endif; ?>
        >
            <div wire:key="dropdown-slot-<?php echo e($uuid); ?>">
                <?php echo e($slot); ?>

            </div>
        </ul>
    </details><?php /**PATH /home/ashart20/FETNET/storage/framework/views/0fca3e59cf7ee803ccf0ab8a2a8d80ab.blade.php ENDPATH**/ ?>