<div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($activityId ? 'Edit Aktivitas' : 'Tambah Aktivitas Baru'); ?></p>
            <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white text-2xl font-bold focus:outline-none">&times;</button>
        </div>

        <form wire:submit.prevent="store" class="pt-4 space-y-4">
            
            <div class="mb-4">
                <label for="teacher_ids" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Pilih Dosen (Bisa lebih dari satu):</label>
                <div wire:ignore>
                    <select id="select-teachers" wire:model="teacher_ids" multiple
                            placeholder="Ketik untuk mencari dosen..." autocomplete="off"
                            class="tom-select-class"> 
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($teacher->id); ?>"><?php echo e($teacher->nama_dosen); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['teacher_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <div>
                <label for="subject_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Mata Kuliah:</label>
                <select id="subject_id" wire:model.defer="subject_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Mata Kuliah --</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($subject->id); ?>"><?php echo e($subject->nama_matkul); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['subject_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <div>
                <label for="student_group_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Kelompok Mahasiswa:</label>
                <select id="student_group_id" wire:model.defer="student_group_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Kelompok --</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $studentGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($group->id); ?>"><?php echo e($group->nama_kelompok); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['student_group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <div>
                <label for="activity_tag_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Pilih Tag (Opsional):</label>
                <select id="activity_tag_id" wire:model.defer="activity_tag_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Tanpa Tag --</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $activityTags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tag->id); ?>"><?php echo e($tag->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['activity_tag_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>



            <div class="flex justify-end pt-4 space-x-2">
                <button type="button" wire:click="closeModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold text-xs uppercase rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">Batal</button>
                <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white font-semibold text-xs uppercase rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <span wire:loading.remove><?php echo e($activityId  ? 'Update' : 'Simpan'); ?></span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div><?php /**PATH /home/ashart20/FETNET/resources/views/livewire/prodi/activity-modal.blade.php ENDPATH**/ ?>