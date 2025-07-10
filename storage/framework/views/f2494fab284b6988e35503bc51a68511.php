<div>
    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Struktur Kelompok Mahasiswa</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Kelola struktur Tingkat (Year), Kelompok (Group), dan Sub-Kelompok.
        </p>

        <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 dark:bg-green-900/50 dark:border-green-800 dark:text-green-200 p-4 my-4 rounded-md" role="alert">
                <p><?php echo e(session('message')); ?></p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <button wire:click="create(null)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-4">
            Tambah Tingkat (Year)
        </button>

        <!--[if BLOCK]><![endif]--><?php if($isModalOpen): ?>
            <?php echo $__env->make('livewire.prodi.student-group-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <div class="border dark:border-gray-700 rounded-lg p-4">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                
                <?php echo $__env->make('livewire.prodi.partials.student-group-item', ['group' => $group, 'level' => 0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-center text-gray-500 dark:text-gray-400">Belum ada data. Silakan tambahkan tingkat baru.</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div><?php /**PATH /home/ashart20/FETNET/resources/views/livewire/prodi/manage-student-groups.blade.php ENDPATH**/ ?>