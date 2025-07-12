<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <!-- Styles -->
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    </head>
    <body class="font-sans antialiased">
        <?php if (isset($component)) { $__componentOriginaldd1b43da7e6d065b15d7e149fac55bb9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldd1b43da7e6d065b15d7e149fac55bb9 = $attributes; } ?>
<?php $component = TallStackUi\View\Components\Banner::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\TallStackUi\View\Components\Banner::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldd1b43da7e6d065b15d7e149fac55bb9)): ?>
<?php $attributes = $__attributesOriginaldd1b43da7e6d065b15d7e149fac55bb9; ?>
<?php unset($__attributesOriginaldd1b43da7e6d065b15d7e149fac55bb9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldd1b43da7e6d065b15d7e149fac55bb9)): ?>
<?php $component = $__componentOriginaldd1b43da7e6d065b15d7e149fac55bb9; ?>
<?php unset($__componentOriginaldd1b43da7e6d065b15d7e149fac55bb9); ?>
<?php endif; ?>

        <div class="min-h-screen bg-gray-100">

            <!-- Page Heading -->
            <?php if(isset($header)): ?>
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <?php echo e($header); ?>

                    </div>
                </header>
            <?php endif; ?>

            <!-- Page Content -->
            <main>
                <?php echo e($slot); ?>

            </main>
        </div>

        <?php echo $__env->yieldPushContent('modals'); ?>
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    </body>
</html>
<?php /**PATH /Volumes/EXCHANGE/WebDev/FetAS/resources/views/components/layouts/app.blade.php ENDPATH**/ ?>