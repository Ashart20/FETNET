    <?php foreach ((['noJoin' => null]) as $__key => $__value) {
    $__consumeVariable = is_string($__key) ? $__key : $__value;
    $$__consumeVariable = is_string($__key) ? $__env->getConsumableComponentData($__key, $__value) : $__env->getConsumableComponentData($__value);
} ?>

    <div
        <?php echo e($attributes->class([
                'collapse border-[length:var(--border)] border-base-content/10',
                'join-item' => !$noJoin,
                'collapse-arrow' => !$collapsePlusMinus && !$noIcon,
                'collapse-plus' => $collapsePlusMinus && !$noIcon
            ])); ?>


        wire:key="collapse-<?php echo e($uuid); ?>"
    >
            <!-- Detects if it is inside an accordion.  -->
            <!--[if BLOCK]><![endif]--><?php if(isset($noJoin)): ?>
                <input id="radio-<?php echo e($uuid); ?>" type="radio" value="<?php echo e($name); ?>" x-model="model" />
            <?php else: ?>
                <input id="checkbox-<?php echo e($uuid); ?>" <?php echo e($attributes->wire('model')); ?> type="checkbox" />
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div
                <?php echo e($heading->attributes->merge(["class" => "collapse-title font-semibold"])); ?>


                <?php if(isset($noJoin)): ?>
                    :class="model == '<?php echo e($name); ?>' && 'z-10'"
                    @click="if (model == '<?php echo e($name); ?>') model = null"
                <?php endif; ?>
            >
                <?php echo e($heading); ?>

            </div>
            <div <?php echo e($content->attributes->merge(["class" => "collapse-content text-sm"])); ?> wire:key="content-<?php echo e($uuid); ?>">
                <!--[if BLOCK]><![endif]--><?php if($separator): ?>
                    <hr class="mb-3 border-t-[length:var(--border)] border-base-content/10" />
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <?php echo e($content); ?>

            </div>
    </div><?php /**PATH /home/ashart20/FETNET/storage/framework/views/27093d0140db33cf16023882e1517837.blade.php ENDPATH**/ ?>