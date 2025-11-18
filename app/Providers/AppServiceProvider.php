<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;

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
        // Force HTTPS in production (for Railway)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Sanctum configuration for SPA
        Sanctum::ignoreMigrations();
    }
}

