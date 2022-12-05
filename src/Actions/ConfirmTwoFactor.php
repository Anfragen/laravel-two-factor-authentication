<?php

namespace Anfragen\TwoFactor\Actions;

use Anfragen\TwoFactor\Support\TwoFactorAuthentication;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ConfirmTwoFactor
{
    /**
     * The two factor authentication provider.
     */
    protected TwoFactorAuthentication $provider;

    /**
     * Create a new action instance.
     */
    public function __construct(TwoFactorAuthentication $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Enable two factor authentication for the user.
     */
    public function handle(mixed $user, string $code): void
    {
        if (!$this->provider->verify($user, $code)) {
            throw ValidationException::withMessages([
                'code' => trans('anfragen::two-factor.invalid-code'),
            ]);
        }

        $user->forceFill(['two_factor_confirmed_at' => Carbon::now()])->save();

        session()->put('two_factor_auth', true);
    }
}
