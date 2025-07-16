<form
    <?php echo e($attributes->whereDoesntStartWith('class')); ?>

    <?php echo e($attributes->class(['grid grid-flow-row auto-rows-min gap-3'])); ?>

>

    <?php echo e($slot); ?>


    <!--[if BLOCK]><![endif]--><?php if($actions): ?>
        <!--[if BLOCK]><![endif]--><?php if(!$noSeparator): ?>
            <hr class="border-t-[length:var(--border)] border-base-content/10 my-3" />
        <?php else: ?>
            <div></div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div <?php echo e($actions->attributes->class(["flex justify-end gap-3"])); ?>>
            <?php echo e($actions); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</form><?php /**PATH /Volumes/EXCHANGE/WebDev/FetAS/storage/framework/views/5f9101bfaecd25e75865ba2e396e350a.blade.php ENDPATH**/ ?>