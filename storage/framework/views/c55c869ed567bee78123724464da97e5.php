<div>
    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Master Ruangan</h1>

        <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
            <div class="bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 my-4 rounded-md" role="alert">
                <p><?php echo e(session('message')); ?></p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <button wire:click="create()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">
            Tambah Ruangan Baru
        </button>

        <!--[if BLOCK]><![endif]--><?php if($isModalOpen): ?>
            <?php echo $__env->make('livewire.fakultas.room-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Ruangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gedung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lantai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kapasitas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    
                    <tr wire:key="<?php echo e($room->id); ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($room->nama_ruangan); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($room->kode_ruangan); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($room->building->name ?? 'N/A'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($room->tipe); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($room->lantai); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200"><?php echo e($room->kapasitas); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="edit(<?php echo e($room->id); ?>)" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-2 rounded text-xs">Edit</button>
                            
                            <button wire:click="delete(<?php echo e($room->id); ?>)" wire:confirm="Anda yakin ingin menghapus ruangan ini?"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs ml-2">
                                Hapus
                            </button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Belum ada data ruangan.
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($rooms->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Volumes/EXCHANGE/WebDev/FetAS/resources/views/livewire/fakultas/manage-rooms.blade.php ENDPATH**/ ?>