<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Jadwal Perkuliahan</h1>

        
        <?php
            $selectClasses = 'w-full text-sm rounded-lg transition
                            bg-white border border-gray-300 text-gray-900 placeholder-gray-500
                            focus:outline-none focus:ring-2 focus:ring-blue-500
                            dark:bg-slate-800 dark:border-slate-700 dark:text-gray-300 dark:placeholder-gray-400';

            $labelClasses = 'block text-xs font-medium mb-1
                           text-gray-700
                           dark:text-gray-200';
        ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <div>
                <label for="filterHari" class="<?php echo e($labelClasses); ?>">Hari</label>
                <select wire:model.live="filterHari" id="filterHari" class="<?php echo e($selectClasses); ?>">
                    <option value="">Semua Hari</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $daftarHari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($item); ?>"><?php echo e($item); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>

            <div>
                <label for="filterDosen" class="<?php echo e($labelClasses); ?>">Dosen</label>
                <select wire:model.live="filterDosen" id="filterDosen" class="<?php echo e($selectClasses); ?>">
                    <option value="">Semua Dosen</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $daftarDosen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($item); ?>"><?php echo e($item); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>

            <div>
                <label for="filterMatkul" class="<?php echo e($labelClasses); ?>">Mata Kuliah</label>
                <select wire:model.live="filterMatkul" id="filterMatkul" class="<?php echo e($selectClasses); ?>">
                    <option value="">Semua Matkul</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $daftarMatkul; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($item); ?>"><?php echo e($item); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>

            <div>
                <label for="filterKelas" class="<?php echo e($labelClasses); ?>">Kelas</label>
                <select wire:model.live="filterKelas" id="filterKelas" class="<?php echo e($selectClasses); ?>">
                    <option value="">Semua Kelas</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $daftarKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($item); ?>"><?php echo e($item); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>

            <div class="flex items-end gap-x-8">
                <div class="flex-grow">
                    <label for="filterRuangan" class="<?php echo e($labelClasses); ?>">Ruangan</label>
                    <select wire:model.live="filterRuangan" id="filterRuangan" class="<?php echo e($selectClasses); ?>">
                        <option value="">Semua Ruangan</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $daftarRuangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($item); ?>"><?php echo e($item); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
                <div class="flex-shrink-0">
                    <button wire:click="resetFilters" title="Reset Semua Filter" class="<?php echo e($selectClasses); ?> h-full inline-flex items-center justify-center gap-x-2 px-4 hover:bg-slate-700">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0011.664 0l3.18-3.185m-4.992-2.686a3.75 3.75 0 01-5.304 0L9 15.121m-2.12-2.828a3.75 3.75 0 015.304 0L15 9.348" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-500 dark:text-gray-300 uppercase">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">Mata Kuliah</th>
                    <th scope="col" class="px-6 py-3 font-medium">SKS</th>
                    <th scope="col" class="px-6 py-3 font-medium">Dosen</th>
                    <th scope="col" class="px-6 py-3 font-medium">Kelas</th>
                    <th scope="col" class="px-6 py-3 font-medium">Hari</th>
                    <th scope="col" class="px-6 py-3 font-medium">Jam</th>
                    <th scope="col" class="px-6 py-3 font-medium">Ruangan</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $jadwal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr wire:key="<?php echo e($item->id); ?>" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white font-medium">
                            <?php echo e($item->activity->subject->nama_matkul ?? 'N/A'); ?>

                            <span class="block text-xs text-gray-500 dark:text-gray-400"><?php echo e($item->activity->subject->kode_matkul ?? ''); ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400"><?php echo e($item->activity->subject->sks ?? '-'); ?></td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">
                            <?php echo $item->activity->teachers->pluck('nama_dosen')->implode('<br>'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            
                            <!--[if BLOCK]><![endif]--><?php $__empty_2 = true; $__currentLoopData = $item->activity->studentGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                <?php if (isset($component)) { $__componentOriginal4f015fb6508e425790bdb8f79792e6ed = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f015fb6508e425790bdb8f79792e6ed = $attributes; } ?>
<?php $component = Mary\View\Components\Badge::resolve(['value' => $studentGroup->nama_kelompok] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Badge::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'badge-neutral mr-1 mb-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f015fb6508e425790bdb8f79792e6ed)): ?>
<?php $attributes = $__attributesOriginal4f015fb6508e425790bdb8f79792e6ed; ?>
<?php unset($__attributesOriginal4f015fb6508e425790bdb8f79792e6ed); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f015fb6508e425790bdb8f79792e6ed)): ?>
<?php $component = $__componentOriginal4f015fb6508e425790bdb8f79792e6ed; ?>
<?php unset($__componentOriginal4f015fb6508e425790bdb8f79792e6ed); ?>
<?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                <?php if (isset($component)) { $__componentOriginal4f015fb6508e425790bdb8f79792e6ed = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f015fb6508e425790bdb8f79792e6ed = $attributes; } ?>
<?php $component = Mary\View\Components\Badge::resolve(['value' => 'N/A (Kelompok tidak ditemukan)'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Badge::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'badge-error']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f015fb6508e425790bdb8f79792e6ed)): ?>
<?php $attributes = $__attributesOriginal4f015fb6508e425790bdb8f79792e6ed; ?>
<?php unset($__attributesOriginal4f015fb6508e425790bdb8f79792e6ed); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f015fb6508e425790bdb8f79792e6ed)): ?>
<?php $component = $__componentOriginal4f015fb6508e425790bdb8f79792e6ed; ?>
<?php unset($__componentOriginal4f015fb6508e425790bdb8f79792e6ed); ?>
<?php endif; ?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400"><?php echo e($item->day->name ?? '-'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400"><?php echo e(optional($item->timeSlot)->start_time ? \Carbon\Carbon::parse($item->timeSlot->start_time)->format('H:i') : '-'); ?> - <?php echo e(optional($item->timeSlot)->end_time ? \Carbon\Carbon::parse($item->timeSlot->end_time)->format('H:i') : '-'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white"><?php echo e($item->room->nama_ruangan ?? '-'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500 dark:text-gray-400">Tidak ada data jadwal ditemukan yang sesuai dengan filter.</td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
    </div>

    <!--[if BLOCK]><![endif]--><?php if($jadwal->hasPages()): ?>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <?php echo e($jadwal->links()); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH /home/ashart20/FETNET/resources/views/livewire/fet-schedule-viewer.blade.php ENDPATH**/ ?>