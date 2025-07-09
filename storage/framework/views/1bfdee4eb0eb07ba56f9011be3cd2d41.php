    <div>
        <label
            for="<?php echo e($uuid); ?>"
            x-data="{
                theme: $persist(window.matchMedia('(prefers-color-scheme: dark)').matches ? '<?php echo e($darkTheme); ?>' : '<?php echo e($lightTheme); ?>').as('mary-theme'),
                class: $persist(window.matchMedia('(prefers-color-scheme: dark)').matches ? '<?php echo e($darkClass); ?>' : '<?php echo e($lightClass); ?>').as('mary-class'),
                init() {
                    if (this.theme == '<?php echo e($darkTheme); ?>') {
                        this.$refs.sun.classList.add('swap-off');
                        this.$refs.sun.classList.remove('swap-on');
                        this.$refs.moon.classList.add('swap-on');
                        this.$refs.moon.classList.remove('swap-off');
                    }
                    this.setToggle()
                },
                setToggle() {
                    document.documentElement.setAttribute('data-theme', this.theme)
                    document.documentElement.setAttribute('class', this.class)
                    this.$dispatch('theme-changed', this.theme)
                    this.$dispatch('theme-changed-class', this.class)
                },
                toggle() {
                    this.theme = this.theme == '<?php echo e($lightTheme); ?>' ? '<?php echo e($darkTheme); ?>' : '<?php echo e($lightTheme); ?>'
                    this.class = this.theme == '<?php echo e($lightTheme); ?>' ? '<?php echo e($lightClass); ?>' : '<?php echo e($darkClass); ?>'
                    this.setToggle()
                }
            }"
            @mary-toggle-theme.window="toggle()"
            <?php echo e($attributes->class("swap swap-rotate")); ?>

        >
            <input id="<?php echo e($uuid); ?>" type="checkbox" class="theme-controller opacity-0" @click="toggle()" :value="theme" />
            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-sun'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-ref' => 'sun','class' => 'swap-on']); ?>
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
            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-moon'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-ref' => 'moon','class' => 'swap-off']); ?>
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
        </label>
    </div>
    <script>
        document.documentElement.setAttribute("data-theme", localStorage.getItem("mary-theme")?.replaceAll("\"", ""))
        document.documentElement.setAttribute("class", localStorage.getItem("mary-class")?.replaceAll("\"", ""))
    </script><?php /**PATH /home/ashart20/FETNET/storage/framework/views/78e64ddcccf18737c331cd44c54737fe.blade.php ENDPATH**/ ?>