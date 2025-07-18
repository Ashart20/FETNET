<div class="mary-table-pagination">
    <div <?php echo e($attributes->class(["mb-4 border-t-[length:var(--border)] border-t-base-content/5"])); ?>></div>
    <div class="justify-between md:flex md:flex-row w-auto md:w-full items-center overflow-y-auto pl-2 pr-2 relative">
        <!--[if BLOCK]><![endif]--><?php if($isShowable()): ?>
        <div class="flex flex-row justify-center md:justify-start mb-2 md:mb-0 py-1">
            <select id="<?php echo e($uuid); ?>" <?php if(!empty($modelName())): ?> wire:model.live="<?php echo e($modelName()); ?>" <?php endif; ?>
                    class="select select-sm flex sm:text-sm sm:leading-6 w-auto md:mr-5">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $perPageValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($option); ?>" <?php if($rows->perPage() === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </select>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <div class="w-full">
        <!--[if BLOCK]><![endif]--><?php if($rows instanceof LengthAwarePaginator): ?>
            <?php echo e($rows->onEachSide(1)->links(data: ['scrollTo' => false])); ?>

        <?php else: ?>
            <?php echo e($rows->links(data: ['scrollTo' => false])); ?>

        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div><?php /**PATH /home/ashart20/FETNET/storage/framework/views/6929bb401fcbeeab106552884f99a016.blade.php ENDPATH**/ ?>