    <div
        x-data="{
                tabs: [],
                selected:
                    <?php if($selected): ?>
                        '<?php echo e($selected); ?>'
                    <?php else: ?>
                        <?php if ((object) ($attributes->wire('model')) instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')->value()); ?>')<?php echo e($attributes->wire('model')->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')); ?>')<?php endif; ?>
                    <?php endif; ?>
                 ,
                 init() {
                     // Fix weird issue when navigating back
                     document.addEventListener('livewire:navigating', () => {
                         document.querySelectorAll('.tab').forEach(el =>  el.remove());
                     });
                 }
        }"
        class="<?php echo e($tabsClass); ?>"
        x-class="font-semibold pb-1 border-b-[length:var(--border)] border-b-base-content/50 border-b-base-content/10 flex overflow-x-auto scrollbar-hide relative w-full"
    >
        <!-- TAB LABELS -->
        <div class="<?php echo e($labelDivClass); ?>">
            <template x-for="tab in tabs">
                <a
                    role="tab"
                    x-html="tab.label"
                     @click="tab.disabled ? null: selected = tab.name"
                    :class="{ '<?php echo e($activeClass); ?> tab-active': selected === tab.name, 'hidden': tab.hidden }"
                    class="tab <?php echo e($labelClass); ?>"></a>
            </template>
        </div>

        <!-- TAB CONTENT -->
        <div role="tablist" <?php echo e($attributes->except(['wire:model', 'wire:model.live'])->class(["block"])); ?>>
            <?php echo e($slot); ?>

        </div>
    </div><?php /**PATH /home/ashart20/FETNET/storage/framework/views/38641c23be426f9db4d05bef468ca769.blade.php ENDPATH**/ ?>