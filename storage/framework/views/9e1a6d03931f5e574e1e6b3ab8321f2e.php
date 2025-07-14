    <div
        <?php echo e($attributes->class(["bg-base-100 rounded-lg px-5 py-4  w-full", "lg:tooltip $tooltipPosition" => $tooltip])); ?>


        <?php if($tooltip): ?>
            data-tip="<?php echo e($tooltip); ?>"
        <?php endif; ?>
    >
        <div class="flex items-center gap-3">
            <!--[if BLOCK]><![endif]--><?php if($icon): ?>
                <div class="  <?php echo e($color); ?>">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => $icon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-9 h-9']); ?>
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
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div class="text-left rtl:text-right">
                <!--[if BLOCK]><![endif]--><?php if($title): ?>
                    <div class="text-xs text-base-content/50 whitespace-nowrap"><?php echo e($title); ?></div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <div class="font-black text-xl"><?php echo e($value ?? $slot); ?></div>

                <!--[if BLOCK]><![endif]--><?php if($description): ?>
                    <div class="stat-desc"><?php echo e($description); ?></div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div><?php /**PATH /Volumes/EXCHANGE/WebDev/FetAS/storage/framework/views/d82affd55519f07b3ed8baf0f86b1f96.blade.php ENDPATH**/ ?>