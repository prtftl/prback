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
     */
    protected function gate(): void
    {
        Gate::define('viewNova', function (User $user) {
            $allowedEmails = array_filter([
                env('NOVA_USER_EMAIL'),
            ]);
            
            return in_array($user->email, $allowedEmails) || app()->environment('local');
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
