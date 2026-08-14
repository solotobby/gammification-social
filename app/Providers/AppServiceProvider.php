<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');

            // Vite HMR marker must never ship to production (breaks @vite CSS/JS).
            $hot = public_path('hot');
            if (is_file($hot)) {
                @unlink($hot);
            }
        }
    }
}
