<?php

namespace Anfragen\TwoFactor\Actions;

use Anfragen\TwoFactor\Enum\TwoFactorType;
use Anfragen\TwoFactor\Support\TwoFactorAuthentication;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class SendLoginCode
{
    /**
     * The auth user instance.
     */
    protected mixed $user;

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
     * Resend the login code.
     */
    public function handle(mixed $user): void
    {
        $this->user = $user;

        if ($this->user->two_factor_type === TwoFactorType::SMS->value) {
            $this->enableSmsType();
        }

        if ($this->user->two_factor_type === TwoFactorType::EMAIL->value) {
            $this->enableEmailType();
        }
    }

    /**
     * Update the two-factor code that will be sent to the user.
     */
    private function updateUserCode(): void
    {
        $this->user->forceFill([
            'two_factor_secret'     => Crypt::encrypt($this->provider->generateCode()),
            'two_factor_created_at' => Carbon::now(),
        ])->save();
    }

    /**
     * Update the code and send the SMS notification to the user.
     */
    protected function enableSmsType(): void
    {
        $this->updateUserCode();

        $this->user->sendSmsCodeNotification();
    }

    /**
     * Update the code and send the Email notification to the user.
     */
    protected function enableEmailType(): void
    {
        $this->updateUserCode();

        $this->user->sendEmailCodeNotification();
    }
}
