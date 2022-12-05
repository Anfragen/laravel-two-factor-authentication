<?php

namespace Anfragen\TwoFactor\Actions;

class DisableTwoFactor
{
    /**
     * Enable two factor authentication for the user.
     */
    public function handle(mixed $user): void
    {
        $user->forceFill([
            'two_factor_type'           => null,
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_created_at'     => null,
            'two_factor_confirmed_at'   => null,
            'two_factor_remember_at'    => null,
        ])->save();
    }
}
