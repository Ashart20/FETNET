<div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($studentGroupId ? 'Edit' : 'Tambah'); ?> Data</p>
            <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-300 text-3xl font-bold focus:outline-none">&times;</button>
        </div>

        <form wire:submit.prevent="store" class="pt-4 space-y-4">
            
            <!--[if BLOCK]><![endif]--><?php if($parentId): ?>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Menambahkan Sub-item di bawah: <span class="font-semibold"><?php echo e(\App\Models\StudentGroup::find($parentId)->nama_kelompok); ?></span>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <?php
                $labelClasses = 'block font-medium text-sm text-gray-700 dark:text-gray-300';
                $inputClasses = 'block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
            ?>

            <div>
                <label for="angkatan" class="<?php echo e($labelClasses); ?>">Angkatan:</label>
                <input type="text" id="angkatan" wire:model="angkatan" class="<?php echo e($inputClasses); ?>">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['angkatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div>
                <label for="nama_kelompok" class="<?php echo e($labelClasses); ?>">Nama (Tingkat/Kelompok/Sub):</label>
                <input type="text" id="nama_kelompok" wire:model="nama_kelompok" class="<?php echo e($inputClasses); ?>">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nama_kelompok'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div>
                <label for="kode_kelompok" class="<?php echo e($labelClasses); ?>">Kode (Opsional):</label>
                <input type="text" id="kode_kelompok" wire:model="kode_kelompok" class="<?php echo e($inputClasses); ?>">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['kode_kelompok'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div>
                <label for="jumlah_mahasiswa" class="<?php echo e($labelClasses); ?>">Jumlah Mahasiswa:</label>
                <input type="number" id="jumlah_mahasiswa" wire:model="jumlah_mahasiswa" class="<?php echo e($inputClasses); ?>">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['jumlah_mahasiswa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="flex justify-end pt-4 space-x-2">
                <button type="button" wire:click="closeModal()" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">Batal</button>
                <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150">
                    <?php echo e($studentGroupId ? 'Update' : 'Simpan'); ?>

                </button>
            </div>
        </form>
    </div>
</div><?php /**PATH /Volumes/EXCHANGE/WebDev/FetAS/resources/views/livewire/prodi/student-group-modal.blade.php ENDPATH**/ ?>