<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-10 md:p-20 text-center border-b border-gray-200 dark:border-gray-700">

                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white leading-tight">
                        Selamat Datang di FETNET
                    </h1>

                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                        Sistem Informasi Penjadwalan Otomatis FPTI Universitas Pendidikan Indonesia.
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                        Platform kolaboratif untuk merancang, mengatur, dan mengotomatiskan proses penjadwalan kompleks berbasis algoritma FET.
                    </p>
                    </p>

                    
                    <?php if(auth()->guard()->guest()): ?>
                        <div class="mt-8 flex justify-center gap-4">
                            <a href="<?php echo e(route('login')); ?>" class="inline-block rounded-lg bg-indigo-600 px-6 py-3 text-base font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Masuk
                            </a>
                            <?php if(Route::has('register')): ?>
                                <a href="<?php echo e(route('register')); ?>" class="inline-block rounded-lg bg-gray-200 dark:bg-gray-700 px-6 py-3 text-base font-medium text-gray-800 dark:text-gray-200 transition hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800">
                                    Daftar
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(auth()->guard()->check()): ?>
                        <div class="mt-8">
                            <a href="<?php echo e(auth()->user()->hasRole('fakultas') ? route('fakultas.dashboard') : (auth()->user()->hasRole('prodi') ? route('prodi.dashboard') : route('mahasiswa.dashboard'))); ?>"
                               class="inline-block rounded-lg bg-indigo-600 px-6 py-3 text-base font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Buka Dashboard
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /home/ashart20/FETNET/resources/views/welcome.blade.php ENDPATH**/ ?>