    <div
        <?php echo e($attributes
                ->merge(['wire:key' => $uuid ])
                ->class(['card bg-base-100 rounded-lg p-5', 'shadow-xs' => $shadow])); ?>

    >
        <!--[if BLOCK]><![endif]--><?php if($figure): ?>
            <figure <?php echo e($figure->attributes->class(["mb-5 -m-5"])); ?>>
                <?php echo e($figure); ?>

            </figure>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($title || $subtitle): ?>
            <div class="pb-5">
                <div class="flex gap-3 justify-between items-center w-full">
                    <div class="grow-1">
                        <!--[if BLOCK]><![endif]--><?php if($title): ?>
                            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["text-xl font-bold", is_string($title) ? '' : $title?->attributes->get('class') ]); ?>" >
                                <?php echo e($title); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if($subtitle): ?>
                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["text-base-content/50 text-sm mt-1", is_string($subtitle) ? '' : $subtitle?->attributes->get('class') ]); ?>" >
                                <?php echo e($subtitle); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($menu): ?>
                        <div <?php echo e($menu->attributes->class(["flex items-center gap-2"])); ?>> <?php echo e($menu); ?> </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <!--[if BLOCK]><![endif]--><?php if($separator): ?>
                    <hr class="mt-3 border-t-[length:var(--border)] border-base-content/10" />

                    <!--[if BLOCK]><![endif]--><?php if($progressIndicator): ?>
                        <div class="h-0.5 -mt-4 mb-4">
                            <progress
                                class="progress progress-primary w-full h-0.5"
                                wire:loading

                                <?php if($progressTarget()): ?>
                                    wire:target="<?php echo e($progressTarget()); ?>"
                                 <?php endif; ?>></progress>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div class="grow-1">
            <?php echo e($slot); ?>

        </div>

        <!--[if BLOCK]><![endif]--><?php if($actions): ?>
            <!--[if BLOCK]><![endif]--><?php if($separator): ?>
                <hr class="mt-5 border-t-[length:var(--border)] border-base-content/10" />
            <?php else: ?>
                <div></div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["flex w-full items-end justify-end gap-3 pt-5", is_string($actions) ? '' : $actions?->attributes->get('class') ]); ?>">
                <?php echo e($actions); ?>

            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div><?php /**PATH /home/ashart20/FETNET/storage/framework/views/a2bc44e75eaee3305e07558a1421c1c2.blade.php ENDPATH**/ ?>