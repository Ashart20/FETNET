

    <!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FETNET - Sistem Penjadwalan</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body class="min-h-screen font-sans antialiased bg-base-200">


<div class="navbar bg-base-100 shadow-md sticky top-0 z-30 px-4">
    <div class="navbar-start">
        
        <a href="/" wire:navigate class="btn btn-ghost text-xl">
            <img src="<?php echo e(asset('logo-fetnet-modern.png')); ?>" alt="Logo Fetnet" class="h-8 w-auto">
            <span class="ml-2 hidden sm:inline">FETNET</span>
        </a>
    </div>
    <div class="navbar-center hidden lg:flex">
        
        <ul class="menu menu-horizontal px-1">
            <?php if(auth()->guard()->check()): ?>
                
                <li>
                    <?php
                        $dashboardRouteName = '';
                        if (auth()->user()->hasRole('fakultas')) {
                            $dashboardRouteName = 'fakultas.dashboard';
                        } elseif (auth()->user()->hasRole('prodi')) {
                            $dashboardRouteName = 'prodi.dashboard';
                        } elseif (auth()->user()->hasRole('mahasiswa')) {
                            $dashboardRouteName = 'mahasiswa.dashboard';
                        }
                    ?>

                    <?php if($dashboardRouteName): ?>
                        <a href="<?php echo e(route($dashboardRouteName)); ?>" <?php if(request()->routeIs($dashboardRouteName)): ?> class="active" <?php endif; ?>>
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-home'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?> Dashboard
                        </a>
                    <?php else: ?>
                        
                        <a href="/dashboard" class="btn btn-ghost">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-home'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?> Dashboard (Default)
                        </a>
                    <?php endif; ?>
                </li>

                
                <?php if (\Illuminate\Support\Facades\Blade::check('role', 'fakultas')): ?>
                <li>
                    <details>
                        <summary>
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-academic-cap'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?> Prodi & User
                        </summary>
                        <ul class="p-2 bg-base-100 rounded-t-none z-20">
                            <li><a href="<?php echo e(route('fakultas.prodi')); ?>" <?php if(request()->routeIs('fakultas.prodi')): ?> class="active" <?php endif; ?>>Manajemen Prodi</a></li>
                        </ul>
                    </details>
                </li>
                <li>
                    <details>
                        <summary>
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-building-office'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?> Ruangan & Batasan
                        </summary>
                        <ul class="p-2 bg-base-100 rounded-t-none z-20">
                            <li><a href="<?php echo e(route('fakultas.rooms')); ?>" <?php if(request()->routeIs('fakultas.rooms')): ?> class="active" <?php endif; ?>>Manajemen Ruangan</a></li>
                            <li><a href="<?php echo e(route('fakultas.room-constraints')); ?>" <?php if(request()->routeIs('fakultas.room-constraints')): ?> class="active" <?php endif; ?>>Batasan Waktu Ruangan</a></li>
                        </ul>
                    </details>
                </li>
                <?php endif; ?>

                
                <?php if (\Illuminate\Support\Facades\Blade::check('role', 'prodi')): ?>
                <li>
                    <details>
                        <summary>
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-archive-box'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?> Manajemen Data
                        </summary>
                        <ul class="p-2 bg-base-100 rounded-t-none z-20">
                            <li><a href="<?php echo e(route('prodi.teachers')); ?>" <?php if(request()->routeIs('prodi.teachers')): ?> class="active" <?php endif; ?>>Dosen</a></li>
                            <li><a href="<?php echo e(route('prodi.subjects')); ?>" <?php if(request()->routeIs('prodi.subjects')): ?> class="active" <?php endif; ?>>Mata Kuliah</a></li>
                            <li><a href="<?php echo e(route('prodi.student-groups')); ?>" <?php if(request()->routeIs('prodi.student-groups')): ?> class="active" <?php endif; ?>>Kelompok Mahasiswa</a></li>
                            <li><a href="<?php echo e(route('prodi.activities')); ?>" <?php if(request()->routeIs('prodi.activities')): ?> class="active" <?php endif; ?>>Aktivitas</a></li>
                        </ul>
                    </details>
                </li>
                <li>
                    <details>
                        <summary>
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-clock'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?> Manajemen Batasan
                        </summary>
                        <ul class="p-2 bg-base-100 rounded-t-none z-20">
                            <li><a href="<?php echo e(route('prodi.teacher-constraints')); ?>" <?php if(request()->routeIs('prodi.teacher-constraints')): ?> class="active" <?php endif; ?>>Batasan Dosen</a></li>
                            <li><a href="<?php echo e(route('prodi.student-group-constraints')); ?>" <?php if(request()->routeIs('prodi.student-group-constraints')): ?> class="active" <?php endif; ?>>Batasan Mahasiswa</a></li>
                        </ul>
                    </details>
                </li>
                <?php endif; ?>

                
                <li><a href="<?php echo e(route('hasil.fet')); ?>" <?php if(request()->routeIs('hasil.fet')): ?> class="active" <?php endif; ?>>
                        <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-calendar-days'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?> Jadwal Utama
                    </a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="navbar-end">
        
        <?php if (isset($component)) { $__componentOriginal96fdff35e0ddfd31ddf96c499b591184 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal96fdff35e0ddfd31ddf96c499b591184 = $attributes; } ?>
<?php $component = Mary\View\Components\ThemeToggle::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-theme-toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\ThemeToggle::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn btn-ghost btn-circle']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal96fdff35e0ddfd31ddf96c499b591184)): ?>
<?php $attributes = $__attributesOriginal96fdff35e0ddfd31ddf96c499b591184; ?>
<?php unset($__attributesOriginal96fdff35e0ddfd31ddf96c499b591184); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal96fdff35e0ddfd31ddf96c499b591184)): ?>
<?php $component = $__componentOriginal96fdff35e0ddfd31ddf96c499b591184; ?>
<?php unset($__componentOriginal96fdff35e0ddfd31ddf96c499b591184); ?>
<?php endif; ?>

        <?php if(auth()->guard()->check()): ?>
            
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost">
                    
                    <span><?php echo e(Auth::user()->name); ?></span>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[20] p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a href="<?php echo e(route('profile.edit')); ?>">Profil</a></li>
                    <li>
                        <form method="POST" action="<?php echo e(route('logout')); ?>" >
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full text-left">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>


<main class="p-4 sm:p-6 lg:p-8">
    <?php echo e($slot); ?>

</main>


<?php if (isset($component)) { $__componentOriginal2aca76be1376419dfd37220f36011753 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aca76be1376419dfd37220f36011753 = $attributes; } ?>
<?php $component = Mary\View\Components\Toast::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Toast::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2aca76be1376419dfd37220f36011753)): ?>
<?php $attributes = $__attributesOriginal2aca76be1376419dfd37220f36011753; ?>
<?php unset($__attributesOriginal2aca76be1376419dfd37220f36011753); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2aca76be1376419dfd37220f36011753)): ?>
<?php $component = $__componentOriginal2aca76be1376419dfd37220f36011753; ?>
<?php unset($__componentOriginal2aca76be1376419dfd37220f36011753); ?>
<?php endif; ?>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH /home/ashart20/FETNET/resources/views/layouts/app.blade.php ENDPATH**/ ?>