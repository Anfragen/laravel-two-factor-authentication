<?php

namespace Anfragen\TwoFactor;

use Illuminate\Support\{ServiceProvider, Str};
use Illuminate\Support\Facades\Event;
use Anfragen\TwoFactor\Http\Middleware\{CheckTwoFactorConfirmed, CheckTwoFactorRequired};
use Anfragen\TwoFactor\Listeners\TwoFactorListener;
use Anfragen\TwoFactor\Provider\RegisterPermissions;
use Illuminate\Auth\Events\Login;
use Illuminate\Routing\Router;

class TwoFactorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'anfragen');

        $this->publishesFiles();

        $this->registerListeners();

        $this->registerMiddleware();

        $this->registerMacros();

        RegisterPermissions::register();
    }

    /**
     * Publishes files.
     */
    private function publishesFiles(): void
    {
        $migration = 'add_two_factor_columns_to_users_table';

        $this->publishes([
            __DIR__ . "/../database/migrations/{$migration}.php" => $this->returnMigrationName($migration),
        ], 'two-factor-migrations');

        $this->publishes([
            __DIR__ . '/../routes' => base_path('routes'),
        ], 'two-factor-routes');

        $this->publishes([
            __DIR__ . '/../lang' => lang_path('vendor/anfragen'),
        ], 'two-factor-lang');
    }

    /**
     * Register the package's listeners.
     */
    private function registerListeners(): void
    {
        Event::listen(Login::class, TwoFactorListener::class);
    }

    /**
     * Register the package's middleware.
     */
    private function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('two-factor.required', CheckTwoFactorRequired::class);
        $router->aliasMiddleware('two-factor.confirmed', CheckTwoFactorConfirmed::class);
    }

    /**
     * Register macros.
     */
    private function registerMacros(): void
    {
        Str::macro('onlyNumbers', function (?string $value): ?string {
            return preg_replace('/[^0-9]/', '', $value);
        });
    }

    /**
     * Return the migration name.
     */
    private function returnMigrationName(string $migration): string
    {
        $date = date('Y_m_d_His', time());

        return database_path("migrations/{$date}_{$migration}.php");
    }
}
