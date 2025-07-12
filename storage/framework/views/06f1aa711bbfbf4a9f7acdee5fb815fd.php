
<option value="<?php echo e($group->id); ?>" class="font-semibold">
    <?php echo e(str_repeat('--', $level ?? 0)); ?> <?php echo e($group->nama_kelompok); ?>

</option>


<!--[if BLOCK]><![endif]--><?php if($group->childrenRecursive->isNotEmpty()): ?>
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $group->childrenRecursive; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('livewire.prodi.partials.student-group-options', ['group' => $child, 'level' => ($level ?? 0) + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
<?php endif; ?><!--[if ENDBLOCK]><![endif]--><?php /**PATH /home/ashart20/FETNET/resources/views/livewire/prodi/partials/student-group-options.blade.php ENDPATH**/ ?>