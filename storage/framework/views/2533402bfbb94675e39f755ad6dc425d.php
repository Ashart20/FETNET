<div id="<?php echo e($anchor); ?>" <?php echo e($attributes->class(["mb-10", "mary-header-anchor" => $withAnchor])); ?>>
    <div class="flex flex-wrap gap-5 justify-between items-center">
        <div>
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["flex", "items-center", "$size font-extrabold", is_string($title) ? '' : $title?->attributes->get('class') ]); ?>" >
                <!--[if BLOCK]><![endif]--><?php if($withAnchor): ?>
                    <a href="#<?php echo e($anchor); ?>">
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!--[if BLOCK]><![endif]--><?php if($icon): ?>
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => ''.e($icon).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => ''.e($iconClasses).'']); ?>
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
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(["ml-2" => $icon]); ?>"><?php echo e($title); ?></span>

                <!--[if BLOCK]><![endif]--><?php if($withAnchor): ?>
                    </a>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!--[if BLOCK]><![endif]--><?php if($subtitle): ?>
                <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["text-base-content/50 text-sm mt-1", is_string($subtitle) ? '' : $subtitle?->attributes->get('class') ]); ?>" >
                    <?php echo e($subtitle); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <!--[if BLOCK]><![endif]--><?php if($middle): ?>
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["flex items-center justify-center gap-3 grow order-last sm:order-none", is_string($middle) ? '' : $middle?->attributes->get('class')]); ?>">
                <div class="w-full lg:w-auto">
                    <?php echo e($middle); ?>

                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["flex items-center gap-3", is_string($actions) ? '' : $actions?->attributes->get('class') ]); ?>" >
            <?php echo e($actions); ?>

        </div>
    </div>

    <!--[if BLOCK]><![endif]--><?php if($separator): ?>
        <hr class="border-t-[length:var(--border)] border-base-content/10 mt-3" />

        <!--[if BLOCK]><![endif]--><?php if($progressIndicator): ?>
            <div class="h-0.5 -mt-4 mb-4">
                <progress
                    class="progress <?php echo e($progressIndicatorClass); ?> w-full h-[var(--border)]"
                    wire:loading

                    <?php if($progressTarget()): ?>
                        wire:target="<?php echo e($progressTarget()); ?>"
                     <?php endif; ?>></progress>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH /Volumes/EXCHANGE/WebDev/NewFet/storage/framework/views/2bd16bd1b798763a115fc3c5ed14d1ca.blade.php ENDPATH**/ ?>