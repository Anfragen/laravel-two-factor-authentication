<?php

namespace Anfragen\TwoFactor\Actions;

use Anfragen\TwoFactor\Support\RecoveryCode;

class GenerateRecoveryCodes
{
    /**
     * Generate new recovery codes for the user.
     */
    public function handle(mixed $user): void
    {
        $codes = RecoveryCode::generateMany();

        $user->forceFill(['two_factor_recovery_codes' => $codes])->save();
    }
}
