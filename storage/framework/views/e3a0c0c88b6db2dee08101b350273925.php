<?php
    $personalize = $classes();
    $flash = session()->pull('tallstackui:banner');
    $wire = $flash ? true : $wire;
?>

<?php if($show): ?>
    <div x-data="tallstackui_banner(<?php echo \Illuminate\Support\Js::from($flash)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($animated)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($wire)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($text ??= $slot->toHtml())->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($enter)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($leave)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($close)->toHtml() ?>)"
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            $personalize['wire'] => $wire,
            $personalize['wrapper'],
            $personalize['sizes.' . $size],
            $colors['background'] ?? $color['background'] => !$wire
        ]); ?>"
        <?php if($wire): ?>
         x-bind:class="{
            'bg-green-600' : type === 'success',
            'bg-red-600' : type === 'error',
            'bg-yellow-600' : type === 'warning',
            'bg-blue-600' : type === 'info'
         }" <?php endif; ?>
        x-show="show && text !== ''"
        x-cloak
        <?php if($wire): ?> x-on:tallstackui:banner.window="add($event)" <?php endif; ?>
        <?php if($animated || $close || $wire): ?>
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-y-10"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="-translate-y-10"
        <?php endif; ?>>
        <?php if($left): ?>
            <span <?php if(!is_string($left)): ?> <?php echo e($left->attributes->class([$personalize['slot.left'], $colors['text'] ?? '' => !$wire])); ?> x-bind:class="{
                    'text-green-50' : type === 'success',
                    'text-red-50' : type === 'error',
                    'text-yellow-50' : type === 'warning',
                    'text-blue-50' : type === 'info'
                }" <?php endif; ?>>
                <?php echo $left; ?>

            </span>
        <?php endif; ?>
        <?php if($wire): ?>
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([$personalize['text'], 'flex justify-center items-center gap-2']); ?>">
                <div x-show="type === 'success'">
                    <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => TallStackUi::prefix('icon')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => TallStackUi::icon('check-circle'),'outline' => true,'internal' => true,'class' => ''.e($personalize['icon']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                </div>
                <div x-show="type === 'error'">
                    <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => TallStackUi::prefix('icon')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => TallStackUi::icon('x-circle'),'outline' => true,'internal' => true,'class' => ''.e($personalize['icon']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                </div>
                <div x-show="type === 'info'">
                    <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => TallStackUi::prefix('icon')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => TallStackUi::icon('information-circle'),'outline' => true,'internal' => true,'class' => ''.e($personalize['icon']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                </div>
                <div x-show="type === 'warning'">
                    <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => TallStackUi::prefix('icon')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => TallStackUi::icon('exclamation-circle'),'outline' => true,'internal' => true,'class' => ''.e($personalize['icon']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                </div>
                <span class="text-white" x-html="text"></span>
            </div>
        <?php else: ?>
            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([$personalize['text'], $colors['text'] ?? $color['text']]); ?>">
                <?php echo $text ??= $slot->toHtml(); ?>

            </span>
        <?php endif; ?>
        <button type="button" x-on:click="show = false" x-show="close" dusk="tallstackui_banner_close">
            <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => TallStackUi::prefix('icon')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => TallStackUi::icon('x-mark'),'internal' => true,'class' => \Illuminate\Support\Arr::toCssClasses([$personalize['close'], $colors['text'] ?? '' => !$wire]),'x-bind:class' => '{
                                    \'text-green-50\': type === \'success\',
                                    \'text-red-50\': type === \'error\',
                                    \'text-yellow-50\': type === \'warning\',
                                    \'text-blue-50\': type === \'info\'
                                 }']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
        </button>
    </div>
<?php endif; ?>
<?php /**PATH /Volumes/EXCHANGE/WebDev/FetAS/vendor/tallstackui/tallstackui/src/resources/views/components/banner.blade.php ENDPATH**/ ?>