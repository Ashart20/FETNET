    <div x-data="{ focused: false, selection: <?php if ((object) ($attributes->wire('model')) instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')->value()); ?>')<?php echo e($attributes->wire('model')->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')); ?>')<?php endif; ?> }">
        <div
            @click.outside = "clear()"
            @keyup.esc = "clear()"

            x-data="{
                options: <?php echo e(json_encode($options)); ?>,
                isSingle: <?php echo e(json_encode($single)); ?>,
                isSearchable: <?php echo e(json_encode($searchable)); ?>,
                isReadonly: <?php echo e(json_encode($isReadonly())); ?>,
                isDisabled: <?php echo e(json_encode($isDisabled())); ?>,
                isRequired: <?php echo e(json_encode($isRequired())); ?>,
                minChars: <?php echo e($minChars); ?>,

                init() {
                    // Fix weird issue when navigating back
                    document.addEventListener('livewire:navigating', () => {
                        let elements = document.querySelectorAll('.mary-choices-element');
                        elements.forEach(el =>  el.remove());
                    });
                },
                get selectedOptions() {
                    return this.isSingle
                        ? this.options.filter(i => i.<?php echo e($optionValue); ?> == this.selection)
                        : this.selection.map(i => this.options.filter(o => o.<?php echo e($optionValue); ?> == i)[0])
                },
                get noResults() {
                    if (!this.isSearchable || this.$refs.searchInput.value == '') {
                        return false
                    }

                    return this.isSingle
                            ? (this.selection && this.options.length  == 1) || (!this.selection && this.options.length == 0)
                            : this.options.length <= this.selection.length
                },
                get isAllSelected() {
                    return this.options.length == this.selection.length
                },
                get isSelectionEmpty() {
                    return this.isSingle
                        ? this.selection == null || this.selection == ''
                        : this.selection.length == 0
                },
                selectAll() {
                    this.selection = this.options.map(i => i.<?php echo e($optionValue); ?>)
                    this.dispatchChangeEvent({ value: this.selection })
                },
                clear() {
                    this.focused = false;
                    this.$refs.searchInput.value = ''
                },
                reset() {
                    this.clear();
                    this.isSingle
                        ? this.selection = null
                        : this.selection = []

                    this.dispatchChangeEvent({ value: this.selection})
                },
                focus() {
                    if (this.isReadonly || this.isDisabled) {
                        return
                    }

                    this.focused = true
                    this.$refs.searchInput.focus()
                },
                resize() {
                    $refs.searchInput.style.width = ($refs.searchInput.value.length + 1) * 0.55 + 'rem'
                },
                isActive(id) {
                    return this.isSingle
                        ? this.selection == id
                        : this.selection.includes(id)
                },
                toggle(id, keepOpen = false) {
                    if (this.isReadonly || this.isDisabled) {
                        return
                    }

                    if (this.isSingle) {
                        this.selection = id
                        this.focused = false
                    } else {
                        this.selection.includes(id)
                            ? this.selection = this.selection.filter(i => i != id)
                            : this.selection.push(id)
                    }

                    this.dispatchChangeEvent({ value: this.selection })

                    this.$refs.searchInput.value = ''

                    if (!keepOpen) {
                        this.$refs.searchInput.focus()
                    }

                },
                search(value, event) {
                    if (value.length < this.minChars) {
                        return
                    }

                    // Prevent search for this keys
                    if (event && ['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Shift', 'CapsLock', 'Tab',
                                  'Control', 'Alt', 'Home', 'End', 'PageUp', 'PageDown'].includes(event.key)) {
                        return;
                    }

                    // Call search function from parent component
                    // `search(value)` or `search(value, extra1, extra2 ...)`
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').<?php echo e(str_contains($searchFunction, '(')
                              ? preg_replace('/\((.*?)\)/', '(value, $1)', $searchFunction)
                              : $searchFunction . '(value)'); ?>.then(()=> this.resize())
                },
                dispatchChangeEvent(detail) {
                    this.$refs.searchInput.dispatchEvent(new CustomEvent('change-selection', { bubbles: true, detail }))
                }
            }"

            @keydown.up="$focus.previous()"
            @keydown.down="$focus.next()"
        >
            <fieldset class="fieldset py-0">
                
                <!--[if BLOCK]><![endif]--><?php if($label && !$inline): ?>
                    <legend class="fieldset-legend mb-0.5">
                        <?php echo e($label); ?>


                        <!--[if BLOCK]><![endif]--><?php if($attributes->get('required')): ?>
                            <span class="text-error">*</span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </legend>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <label class="<?php echo \Illuminate\Support\Arr::toCssClasses(["floating-label" => $label && $inline]); ?>">
                    
                    <!--[if BLOCK]><![endif]--><?php if($label && $inline): ?>
                        <span class="font-semibold"><?php echo e($label); ?></span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["w-full", "join" => $prepend || $append]); ?>">
                        
                        <!--[if BLOCK]><![endif]--><?php if($prepend): ?>
                            <?php echo e($prepend); ?>

                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <label
                            x-ref="container"

                            <?php if($isDisabled()): ?>
                                disabled
                            <?php endif; ?>

                            <?php if(!$isDisabled() && !$isReadonly()): ?>
                                @click="focus()"
                            <?php endif; ?>

                            <?php echo e($attributes->whereStartsWith('class')->class([
                                    "select w-full min-h-fit pl-2.5",
                                    "join-item" => $prepend || $append,
                                    "border-dashed" => $attributes->has("readonly") && $attributes->get("readonly") == true,
                                    "!select-error" => $errorFieldName() && $errors->has($errorFieldName()) && !$omitError
                                ])); ?>

                        >
                            
                            <!--[if BLOCK]><![endif]--><?php if($prefix): ?>
                                <span class="label"><?php echo e($prefix); ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            
                            <!--[if BLOCK]><![endif]--><?php if($icon): ?>
                                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => $icon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'pointer-events-none w-4 h-4 opacity-40']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <div class="w-full py-0.5 min-h-3 content-center text-wrap">

                                
                                <span wire:key="selected-options-<?php echo e($uuid); ?>">
                                    <!--[if BLOCK]><![endif]--><?php if($compact): ?>
                                        <div class="badge badge-soft">
                                            <span class="font-black" x-text="selectedOptions.length"></span> <?php echo e($compactText); ?>

                                        </div>
                                    <?php else: ?>
                                        <template x-for="(option, index) in selectedOptions" :key="index">
                                            <span class="mary-choices-element cursor-pointer badge badge-soft m-0.5 !inline-block !h-auto">
                                                
                                                <!--[if BLOCK]><![endif]--><?php if($selection): ?>
                                                    <span x-html="document.getElementById('selection-<?php echo e($uuid . '-\' + option.'. $optionValue); ?>).innerHTML"></span>
                                                <?php else: ?>
                                                    <span x-text="option?.<?php echo e($optionLabel); ?>"></span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <!--[if BLOCK]><![endif]--><?php if(!$isDisabled() && !$isReadonly()): ?>
                                                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-x-mark'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'toggle(option.'.e($optionValue).')','x-show' => '!isReadonly && !isDisabled && !isSingle','class' => 'w-4 h-4 hover:text-error']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </span>
                                        </template>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </span>

                                
                                <span :class="(focused || !isSelectionEmpty) && 'hidden'" class="text-base-content/40">
                                    <?php echo e($attributes->get('placeholder')); ?>

                                </span>

                                
                                <input
                                    x-ref="searchInput"
                                    @input="focus(); resize();"
                                    @keydown.arrow-down.prevent="focus()"
                                    :required="isRequired && isSelectionEmpty"
                                    class="w-1 !inline-block outline-hidden"

                                    <?php echo e($attributes->whereStartsWith('@')); ?>


                                    <?php if($isReadonly() || $isDisabled() || ! $searchable): ?>
                                        readonly
                                    <?php else: ?>
                                        @focus="focus()"
                                    <?php endif; ?>

                                    <?php if($isDisabled()): ?>
                                        disabled
                                     <?php endif; ?>

                                    <?php if($searchable): ?>
                                        @keydown.debounce.<?php echo e($debounce); ?>="search($el.value, $event)"
                                    <?php endif; ?>
                                 />
                            </div>

                            
                            <!--[if BLOCK]><![endif]--><?php if($clearable && !$isReadonly() && !$isDisabled()): ?>
                                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-x-mark'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'reset()','x-show' => '!isSelectionEmpty','class' => 'cursor-pointer w-4 h-4 opacity-40']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            
                            <!--[if BLOCK]><![endif]--><?php if($iconRight): ?>
                                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => $iconRight] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'pointer-events-none w-4 h-4 opacity-40']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            
                            <!--[if BLOCK]><![endif]--><?php if($suffix): ?>
                                <span class="label"><?php echo e($suffix); ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </label>

                        
                        <!--[if BLOCK]><![endif]--><?php if($append): ?>
                            <?php echo e($append); ?>

                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </label>

                
                <!--[if BLOCK]><![endif]--><?php if(!$omitError && $errors->has($errorFieldName())): ?>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $errors->get($errorFieldName()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = Arr::wrap($message); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="<?php echo e($errorClass); ?>" x-class="text-error"><?php echo e($line); ?></div>
                            <?php if($firstErrorOnly) break; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php if($firstErrorOnly) break; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                
                <!--[if BLOCK]><![endif]--><?php if($hint): ?>
                    <div class="<?php echo e($hintClass); ?>" x-classes="fieldset-label"><?php echo e($hint); ?></div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </fieldset>

            
            <div x-cloak x-show="focused" class="relative" wire:key="options-list-main-<?php echo e($uuid); ?>">
                <div
                    wire:key="options-list-<?php echo e($uuid); ?>"
                    class="<?php echo e($height); ?> w-full absolute z-10 shadow-xl bg-base-100 border border-base-content/10 rounded-lg cursor-pointer overflow-y-auto"
                    x-anchor.bottom-start="$refs.container"
                >

                    
                    <progress wire:loading wire:target="<?php echo e(preg_replace('/\((.*?)\)/', '', $searchFunction)); ?>" class="progress absolute top-0 h-0.5"></progress>

                   
                   <!--[if BLOCK]><![endif]--><?php if($allowAll): ?>
                       <div
                            wire:key="allow-all-<?php echo e(rand()); ?>"
                            class="font-bold   border border-s-4 border-s-base-content/10 border-base-200 hover:bg-base-200"
                       >
                            <div x-show="!isAllSelected" @click="selectAll()" class="p-3 underline decoration-wavy decoration-info"><?php echo e($allowAllText); ?></div>
                            <div x-show="isAllSelected" @click="reset()" class="p-3 underline decoration-wavy decoration-error"><?php echo e($removeAllText); ?></div>
                       </div>
                   <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    
                    <div
                        x-show="noResults"
                        wire:key="no-results-<?php echo e(rand()); ?>"
                        class="p-3 decoration-wavy decoration-warning underline font-bold border border-s-4 border-s-warning border-b-base-200"
                    >
                        <?php echo e($noResultText); ?>

                    </div>

                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div
                            wire:key="option-<?php echo e(data_get($option, $optionValue)); ?>"
                            @click="toggle(<?php echo e($getOptionValue($option)); ?>, true)"
                            @keydown.enter="toggle(<?php echo e($getOptionValue($option)); ?>, true)"
                            :class="isActive(<?php echo e($getOptionValue($option)); ?>) && 'border-s-4 border-s-base-content'"
                            class="border-s-4 border-base-content/10 focus:bg-base-200 focus:outline-none"
                            tabindex="0"
                        >
                            
                            <!--[if BLOCK]><![endif]--><?php if($item): ?>
                                <?php echo e($item($option)); ?>

                            <?php else: ?>
                                <?php if (isset($component)) { $__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418 = $attributes; } ?>
<?php $component = Mary\View\Components\ListItem::resolve(['item' => $option,'value' => $optionLabel,'subValue' => $optionSubLabel,'avatar' => $optionAvatar] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-list-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\ListItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418)): ?>
<?php $attributes = $__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418; ?>
<?php unset($__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418)): ?>
<?php $component = $__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418; ?>
<?php unset($__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418); ?>
<?php endif; ?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            
                            <!--[if BLOCK]><![endif]--><?php if($selection): ?>
                                <span id="selection-<?php echo e($uuid); ?>-<?php echo e(data_get($option, $optionValue)); ?>" class="hidden">
                                    <?php echo e($selection($option)); ?>

                                </span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div><?php /**PATH /home/ashart20/FETNET/storage/framework/views/e6e56820e1f9aa7e1189bdd851b514d6.blade.php ENDPATH**/ ?>