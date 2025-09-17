<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            // Route config for V1 version of API
            Route::prefix('api/v1')
                ->middleware('api')
                ->namespace($this->namespace. '\V1')
                ->group(base_path('routes/api_v1.php'));

            // Route config for V2 version of API
            Route::prefix('v2')
                ->middleware('api')
                ->namespace($this->namespace. '\V2')
                ->group(base_path('routes/api_v2.php'));

            Route::prefix('v3')
                ->middleware('api')
                ->namespace($this->namespace. '\V3')
                ->group(base_path('routes/api_v3.php'));

            Route::prefix('admin/v1')
                ->middleware('api')
                ->namespace($this->namespace. '\Web')
                ->group(base_path('routes/admin_v1.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
