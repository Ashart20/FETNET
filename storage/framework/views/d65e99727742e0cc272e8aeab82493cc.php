<div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($roomId ? 'Edit Ruangan' : 'Tambah Ruangan Baru'); ?></p>
            <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white text-2xl font-bold focus:outline-none">&times;</button>
        </div>

        <form wire:submit.prevent="store" class="pt-4">
            <div class="space-y-4">
                
                <?php
                    $inputClasses = "shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500";
                    $labelClasses = "block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2";
                ?>

                <div>
                    <label for="nama_ruangan" class="<?php echo e($labelClasses); ?>">Nama Ruangan:</label>
                    <input type="text" id="nama_ruangan" wire:model.defer="nama_ruangan" class="<?php echo e($inputClasses); ?>">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nama_ruangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div>
                    <label for="kode_ruangan" class="<?php echo e($labelClasses); ?>">Kode Ruangan:</label>
                    <input type="text" id="kode_ruangan" wire:model.defer="kode_ruangan" class="<?php echo e($inputClasses); ?>">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['kode_ruangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div>
                    <label for="building_id" class="<?php echo e($labelClasses); ?>">Gedung:</label>
                    <select id="building_id" wire:model.defer="building_id" class="<?php echo e($inputClasses); ?>">
                        <option value="">-- Pilih Gedung --</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $buildings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $building): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($building->id); ?>"><?php echo e($building->name); ?> (<?php echo e($building->code); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['building_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <div class="mt-3 p-3 border rounded-md dark:border-gray-600">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Tidak menemukan gedung? Tambah baru:</p>

                    <!--[if BLOCK]><![endif]--><?php if(session()->has('building-message')): ?>
                        <div class="text-green-600 dark:text-green-400 text-xs mb-2"><?php echo e(session('building-message')); ?></div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <div class="flex items-center space-x-2">
                        <?php
                            $newBuildingInputClasses = "w-full text-xs border-gray-300 rounded-md py-2 px-3 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500";
                        ?>
                        <input type="text" wire:model.defer="newBuildingName" placeholder="Nama Gedung" class="w-1/2 <?php echo e($newBuildingInputClasses); ?>">
                        <input type="text" wire:model.defer="newBuildingCode" placeholder="Kode" class="w-1/3 <?php echo e($newBuildingInputClasses); ?>">
                        <button type="button" wire:click="addNewBuilding" wire:loading.attr="disabled" wire:target="addNewBuilding" class="w-auto bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 rounded text-xs disabled:opacity-50 transition">
                            + Tambah
                        </button>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newBuildingName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newBuildingCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div>
                    <label for="tipe" class="<?php echo e($labelClasses); ?>">Tipe Ruangan:</label>
                    <select id="tipe" wire:model.defer="tipe" class="<?php echo e($inputClasses); ?>">
                        <option value="KELAS_TEORI">Kelas Teori</option>
                        <option value="LABORATORIUM">Laboratorium</option>
                        <option value="AUDITORIUM">Auditorium</option>
                    </select>
                </div>
                <div>
                    <label for="lantai" class="<?php echo e($labelClasses); ?>">Lantai:</label>
                    <input type="text" id="lantai" wire:model.defer="lantai" class="<?php echo e($inputClasses); ?>">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['lantai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div>
                    <label for="kapasitas" class="<?php echo e($labelClasses); ?>">Kapasitas:</label>
                    <input type="number" id="kapasitas" wire:model.defer="kapasitas" class="<?php echo e($inputClasses); ?>">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['kapasitas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>

            <div class="flex justify-end pt-6 space-x-2">
                <button type="button" wire:click="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">Batal</button>
                <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white font-semibold text-xs uppercase rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <span wire:loading.remove><?php echo e($roomId ? 'Update' : 'Simpan'); ?></span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div><?php /**PATH /Volumes/EXCHANGE/WebDev/FetAS/resources/views/livewire/fakultas/room-modal.blade.php ENDPATH**/ ?>