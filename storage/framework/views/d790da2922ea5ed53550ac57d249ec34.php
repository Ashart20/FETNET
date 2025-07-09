<div>
    <?php
        // We need this extra step to support models arrays. Ex: wire:model="emails.0"  , wire:model="emails.1"
        $uuid = $uuid . $modelName()
    ?>

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
                    <?php echo e($attributes->whereStartsWith('class')->class([
                            "select w-full",
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
<?php $component->withAttributes(['class' => 'pointer-events-none w-4 h-4 -ml-1 opacity-40']); ?>
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

                    
                    <select id="<?php echo e($uuid); ?>" <?php echo e($attributes->whereDoesntStartWith('class')); ?>>
                        <!--[if BLOCK]><![endif]--><?php if($placeholder): ?>
                            <option value="<?php echo e($placeholderValue); ?>"><?php echo e($placeholder); ?></option>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(data_get($option, $optionValue)); ?>" <?php if(data_get($option, 'disabled')): ?> disabled <?php endif; ?>><?php echo e(data_get($option, $optionLabel)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>

                    
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
</div><?php /**PATH /home/ashart20/FETNET/storage/framework/views/e9806bea217711ffa8e86976ad9781f7.blade.php ENDPATH**/ ?>