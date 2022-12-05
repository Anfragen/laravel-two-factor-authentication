<?php

namespace Anfragen\TwoFactor\Actions;

use Anfragen\TwoFactor\Enum\TwoFactorType;
use Anfragen\TwoFactor\Support\{RecoveryCode, TwoFactorAuthentication};
use Illuminate\Support\Facades\Crypt;

class EnableTwoFactor
{
    /**
     * The auth user instance.
     */
    protected mixed $user;

    /**
     * The two factor type.
     */
    protected string $type;

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
    public function handle(mixed $user, string $type): void
    {
        $this->user = $user;

        $this->type = $type;

        if ($this->type === TwoFactorType::APP->value) {
            $this->enableAppType();
        }

        if (in_array($this->type, [TwoFactorType::SMS->value, TwoFactorType::EMAIL->value])) {
            $this->enableSmsAndEmailType();
        }
    }

    /**
     * Enable the APP type.
     */
    protected function enableAppType(): void
    {
        $codes = RecoveryCode::generateMany();

        $this->user->forceFill([
            'two_factor_type'           => $this->type,
            'two_factor_secret'         => Crypt::encrypt($this->provider->generateSecretKey()),
            'two_factor_recovery_codes' => $codes,
            'two_factor_created_at'     => null,
            'two_factor_confirmed_at'   => null,
            'two_factor_remember_at'    => null,
        ])->save();
    }

    /**
     * Enable the SMS or Email type.
     */
    protected function enableSmsAndEmailType(): void
    {
        $this->user->forceFill([
            'two_factor_type'         => $this->type,
            'two_factor_created_at'   => null,
            'two_factor_confirmed_at' => null,
            'two_factor_remember_at'  => null,
        ])->save();

        app(SendLoginCode::class)->handle($this->user->refresh());
    }
}
