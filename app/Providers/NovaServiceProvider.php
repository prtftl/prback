<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

class NovaServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!class_exists(\Laravel\Nova\Nova::class)) {
            return;
        }

        $this->fortify();
        $this->routes();
        $this->gate();
    }

    /**
     * Register the configurations for Laravel Fortify.
     */
    protected function fortify(): void
    {
        if (!class_exists(\Laravel\Nova\Nova::class)) {
            return;
        }

        \Laravel\Nova\Nova::fortify()
            ->features([
                \Laravel\Fortify\Features::updatePasswords(),
                // Features::emailVerification(),
                // Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => true]),
            ])
            ->register();
    }

    /**
     * Register the Nova routes.
     */
    protected function routes(): void
    {
        if (!class_exists(\Laravel\Nova\Nova::class)) {
            return;
        }

        \Laravel\Nova\Nova::routes()
            ->withAuthenticationRoutes(default: true)
            ->withPasswordResetRoutes()
            ->withoutEmailVerificationRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     * 
     * IMPORTANT: If this gate returns false, Nova will NOT register routes at all,
     * which results in 404 instead of 403. This is why we need to be careful here.
     */
    protected function gate(): void
    {
        Gate::define('viewNova', function (?User $user = null) {
            // Allow access in local environment
            if (app()->environment('local')) {
                return true;
            }

            // If user is not authenticated, allow access to login page
            // This ensures Nova routes are registered even when not logged in
            if (!$user) {
                return true;
            }

            // Check if user's email is in allowed list
            $allowedEmails = array_filter([
                env('NOVA_USER_EMAIL'),
            ]);
            
            return in_array($user->email, $allowedEmails);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array<int, \Laravel\Nova\Dashboard>
     */
    protected function dashboards(): array
    {
        if (!class_exists(\Laravel\Nova\Nova::class)) {
            return [];
        }

        // Проверяем, что класс Main существует (может быть в Nova.disabled)
        $mainClass = \App\Nova\Dashboards\Main::class;
        if (!class_exists($mainClass)) {
            return [];
        }

        return [
            new $mainClass,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array<int, \Laravel\Nova\Tool>
     */
    public function tools(): array
    {
        return [];
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Не вызываем parent::register() если Nova не установлена
        if (!class_exists(\Laravel\Nova\Nova::class)) {
            return;
        }

        // Если нужно, можно вызвать parent::register() здесь
        //
    }
}
