<?php

namespace Anfragen\TwoFactor;

use Anfragen\TwoFactor\Enum\TwoFactorType;
use Illuminate\Support\{ServiceProvider, Str};
use Illuminate\Support\Facades\{Event, Gate};
use Anfragen\TwoFactor\Http\Middleware\{CheckTwoFactorConfirmed, CheckTwoFactorRequired};
use Anfragen\TwoFactor\Listeners\TwoFactorListener;
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

        $this->registerGates();

        $this->registerMacros();
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
     * Configure the package's gates.
     */
    private function registerGates(): void
    {
        Gate::define('two-factor-disabled', function ($user) {
            return is_null($user->two_factor_confirmed_at);
        });

        Gate::define('two-factor-enabled', function ($user) {
            return !is_null($user->two_factor_type)
                && !is_null($user->two_factor_secret)
                && is_null($user->two_factor_confirmed_at);
        });

        Gate::define('two-factor-confirmed', function ($user) {
            return !is_null($user->two_factor_type)
                && !is_null($user->two_factor_secret)
                && !is_null($user->two_factor_confirmed_at);
        });

        Gate::define('two-factor-app', function ($user) {
            return $user->two_factor_type === TwoFactorType::APP->value
                && !is_null($user->two_factor_secret);
        });

        Gate::define('two-factor-sms-or-email', function ($user) {
            return $user->two_factor_type !== TwoFactorType::APP->value
                && !is_null($user->two_factor_secret);
        });
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
