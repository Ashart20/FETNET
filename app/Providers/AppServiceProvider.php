<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\Sidebar;
use App\Livewire\Header;
use App\Livewire\StatsCards;
use App\Livewire\ScheduleTable;
use App\Livewire\QuickActions;
use App\Livewire\Dashboard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FetWatchService::class, function () {
            return new FetWatchService();
        });
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('sidebar', Sidebar::class);
        Livewire::component('header', Header::class);
        Livewire::component('stats-cards', StatsCards::class);
        Livewire::component('schedule-table', ScheduleTable::class);
        Livewire::component('quick-actions', QuickActions::class);
        Livewire::component('dashboard', Dashboard::class);
        //
    }
}
