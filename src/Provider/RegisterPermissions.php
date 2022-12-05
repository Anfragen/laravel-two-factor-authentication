<?php

namespace Anfragen\TwoFactor\Provider;

use Anfragen\TwoFactor\Enum\TwoFactorType;
use Illuminate\Support\Facades\Gate;

class RegisterPermissions
{
    /**
     * Register the permissions.
     */
    public static function register(): void
    {
        Gate::define('two-factor-enable', function ($user) {
            return is_null($user->two_factor_confirmed_at);
        });

        Gate::define('two-factor-confirm', function ($user) {
            return !is_null($user->two_factor_type)
                && !is_null($user->two_factor_secret)
                && is_null($user->two_factor_confirmed_at);
        });

        Gate::define('two-factor-disable', function ($user) {
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
}
