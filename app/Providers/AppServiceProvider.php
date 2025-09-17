<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // No need to add URL forcing here, keep register clean
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force HTTPS only in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        
        Paginator::useBootstrap();
    }
}
