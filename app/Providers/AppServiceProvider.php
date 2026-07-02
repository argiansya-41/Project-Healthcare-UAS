<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        if (str_contains(request()->server('HTTP_X_FORWARDED_PROTO', ''), 'https') || 
            env('APP_ENV') === 'production' || 
            str_contains(request()->server('HTTP_HOST', ''), 'vercel.app')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
