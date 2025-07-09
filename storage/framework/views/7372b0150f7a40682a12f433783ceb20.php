<div>
    <div class="p-6 lg:p-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Dasbor Fakultas</h1>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="font-bold text-gray-900 dark:text-white">Total Prodi</h3>
                
                <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-2"><?php echo e($this->stats['totalProdi']); ?></p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="font-bold text-gray-900 dark:text-white">Total User Prodi</h3>
                <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-2"><?php echo e($this->stats['totalUserProdi']); ?></p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="font-bold text-gray-900 dark:text-white">Total Ruangan</h3>
                <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-2"><?php echo e($this->stats['totalRuangan']); ?></p>
            </div>
        </div>

        
        <div class="mt-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Pintasan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                <a href="<?php echo e(route('fakultas.prodi')); ?>" class="block p-6 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600">Manajemen Prodi</a>
                <a href="<?php echo e(route('fakultas.rooms')); ?>" class="block p-6 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600">Manajemen Ruangan</a>
                <a href="<?php echo e(route('fakultas.room-constraints')); ?>" class="block p-6 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600">Batasan Ruangan</a>
            </div>
        </div>
    </div>
</div><?php /**PATH /home/ashart20/FETNET/resources/views/livewire/fakultas/dashboard.blade.php ENDPATH**/ ?>