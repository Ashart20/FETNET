<div>
    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Data Dosen</h1>

        <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
            <div class="bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 dark:border-green-600 text-green-700 dark:text-green-300 p-4 my-4 rounded-md" role="alert">
                <p><?php echo e(session('message')); ?></p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <button wire:click="create()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">
            Tambah Data Dosen Baru
        </button>

        
        <!--[if BLOCK]><![endif]--><?php if($isModalOpen): ?>
            <?php echo $__env->make('livewire.prodi.teacher-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama dosen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kode dosen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($teachers->firstItem() + $index); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($teacher->nama_dosen); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($teacher->kode_dosen); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="edit(<?php echo e($teacher->id); ?>)" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-2 rounded text-xs">Edit</button>
                            <button wire:click="delete(<?php echo e($teacher->id); ?>)" wire:confirm="Anda yakin ingin menghapus data ini?"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs ml-2">
                                Hapus
                            </button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Belum ada data dosen. Silakan tambahkan data baru.
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($teachers->links()); ?>

        </div>
    </div>
</div><?php /**PATH /home/ashart20/FETNET/resources/views/livewire/prodi/manage-teachers.blade.php ENDPATH**/ ?>