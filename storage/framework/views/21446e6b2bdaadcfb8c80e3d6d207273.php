<div>
    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Manajemen User Prodi</h1>

        <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
            <div class="bg-green-100 dark:bg-green-800/50 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-4 my-4 rounded-md" role="alert">
                <p><?php echo e(session('message')); ?></p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <button wire:click="create()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">
            Tambah User Prodi
        </button>

        <!--[if BLOCK]><![endif]--><?php if($isModalOpen): ?>
            <?php echo $__env->make('livewire.fakultas.prodi-users-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prodi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr wire:key="<?php echo e($user->id); ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($users->firstItem() + $index); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($user->name); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($user->email); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($user->prodi->nama_prodi ?? 'N/A'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            
                            <button wire:click="edit(<?php echo e($user->id); ?>)" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-2 rounded text-xs">Edit</button>
                            
                            <button wire:click="delete(<?php echo e($user->id); ?>)" wire:confirm="Anda yakin ingin menghapus user ini?"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs ml-2">
                                Hapus
                            </button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Belum ada data user prodi.
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($users->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Volumes/EXCHANGE/WebDev/FetAS/resources/views/livewire/fakultas/manage-prodi-users.blade.php ENDPATH**/ ?>