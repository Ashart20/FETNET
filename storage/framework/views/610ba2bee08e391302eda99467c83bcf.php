
<?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['title' => ''.e($activityId ? 'Edit' : 'Tambah').' Aktivitas','separator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'modal-lg','wire:model' => 'activityModal','wire:close' => 'closeModal']); ?>

    <div class="space-y-4">
        <?php if (isset($component)) { $__componentOriginald64144c2287634503c73cd4803d6e578 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald64144c2287634503c73cd4803d6e578 = $attributes; } ?>
<?php $component = Mary\View\Components\Select::resolve(['label' => 'Pilih Mata Kuliah','options' => $subjects,'optionValue' => 'id','optionLabel' => 'name','placeholder' => '-- Pilih Mata Kuliah --'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'subject_id','searchable' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald64144c2287634503c73cd4803d6e578)): ?>
<?php $attributes = $__attributesOriginald64144c2287634503c73cd4803d6e578; ?>
<?php unset($__attributesOriginald64144c2287634503c73cd4803d6e578); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald64144c2287634503c73cd4803d6e578)): ?>
<?php $component = $__componentOriginald64144c2287634503c73cd4803d6e578; ?>
<?php unset($__componentOriginald64144c2287634503c73cd4803d6e578); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207 = $attributes; } ?>
<?php $component = Mary\View\Components\Choices::resolve(['label' => 'Pilih Kelompok Mahasiswa','options' => $allStudentGroups,'optionLabel' => 'nama_kelompok','searchable' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-choices'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Choices::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'selectedStudentGroupIds','placeholder' => '-- Pilih Kelompok --','multiple' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207)): ?>
<?php $attributes = $__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207; ?>
<?php unset($__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207)): ?>
<?php $component = $__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207; ?>
<?php unset($__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207 = $attributes; } ?>
<?php $component = Mary\View\Components\Choices::resolve(['label' => 'Pilih Dosen','options' => $teachers,'optionLabel' => 'nama_dosen','searchable' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-choices'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Choices::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'teacher_ids','placeholder' => '-- Pilih Dosen --','multiple' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207)): ?>
<?php $attributes = $__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207; ?>
<?php unset($__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207)): ?>
<?php $component = $__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207; ?>
<?php unset($__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginald64144c2287634503c73cd4803d6e578 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald64144c2287634503c73cd4803d6e578 = $attributes; } ?>
<?php $component = Mary\View\Components\Select::resolve(['label' => 'Tag Aktivitas (Wajib)','options' => $activityTags,'optionValue' => 'id','optionLabel' => 'name','placeholder' => '-- Pilih Tag --'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'activity_tag_id']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald64144c2287634503c73cd4803d6e578)): ?>
<?php $attributes = $__attributesOriginald64144c2287634503c73cd4803d6e578; ?>
<?php unset($__attributesOriginald64144c2287634503c73cd4803d6e578); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald64144c2287634503c73cd4803d6e578)): ?>
<?php $component = $__componentOriginald64144c2287634503c73cd4803d6e578; ?>
<?php unset($__componentOriginald64144c2287634503c73cd4803d6e578); ?>
<?php endif; ?>

    </div>

     <?php $__env->slot('actions', null, []); ?> 
        <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Batal'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'closeModal()']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => ''.e($activityId ? 'Update' : 'Simpan').'','spinner' => 'store'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'store','class' => 'btn-primary']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
     <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $attributes = $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $component = $__componentOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?><?php /**PATH /home/ashart20/FETNET/resources/views/livewire/prodi/activity-form.blade.php ENDPATH**/ ?>