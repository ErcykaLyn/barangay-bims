<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\ResidentSidebarComposer;
use App\View\Composers\AdminSidebarComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the view composer for the resident sidebar
        View::composer('layouts.resident_sidebar', ResidentSidebarComposer::class);

        // ✅ Register the view composer for the admin sidebar (which is 'layouts.sidebar')
        View::composer('layouts.sidebar', AdminSidebarComposer::class);
    }
}