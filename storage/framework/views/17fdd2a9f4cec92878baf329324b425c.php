<div class="py-2 <?php echo e($level > 0 ? 'ml-'.($level * 4) : ''); ?>">
    <div class="flex items-center justify-between p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700/50">
        
        <div class="flex-1">
            <span class="font-semibold text-gray-800 dark:text-gray-200"><?php echo e($group->nama_kelompok); ?></span>
            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">(Angkatan: <?php echo e($group->angkatan); ?>)</span>
        </div>

        
        <div class="flex items-center space-x-2">
            
            <!--[if BLOCK]><![endif]--><?php if($level < 2): ?>
                <button wire:click="create(<?php echo e($group->id); ?>)" class="text-green-500 hover:text-green-700 text-xs font-bold">Tambah Sub</button>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <button wire:click="edit(<?php echo e($group->id); ?>)" class="text-yellow-500 hover:text-yellow-700 text-xs font-bold">Edit</button>
            <button wire:click="delete(<?php echo e($group->id); ?>)" wire:confirm="Yakin ingin menghapus '<?php echo e($group->nama_kelompok); ?>' beserta semua sub-kelompoknya?" class="text-red-500 hover:text-red-700 text-xs font-bold">Hapus</button>
        </div>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($group->childrenRecursive->isNotEmpty()): ?>
        <div class="border-l-2 dark:border-gray-600">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $group->childrenRecursive; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('livewire.prodi.partials.student-group-item', ['group' => $child, 'level' => $level + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH /home/ashart20/FETNET/resources/views/livewire/prodi/partials/student-group-item.blade.php ENDPATH**/ ?>