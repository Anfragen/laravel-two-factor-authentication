<?php

namespace Anfragen\TwoFactor\Traits;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use Anfragen\TwoFactor\Support\{RecoveryCode, TwoFactorAuthentication};
use Anfragen\TwoFactor\Notifications\{EmailCodeNotification, SmsCodeNotification};
use BaconQrCode\Renderer\RendererStyle\{Fill, RendererStyle};
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

trait TwoFactorAuthenticatable
{
    /**
     * Send the sms code notification.
     */
    public function sendSmsCodeNotification(): void
    {
        $this->notify(new SmsCodeNotification());
    }

    /**
     * Send the email code notification.
     */
    public function sendEmailCodeNotification(): void
    {
        $this->notify(new EmailCodeNotification());
    }

    /**
     * Determine if two-factor authentication has been enabled.
     */
    public function hasEnabledTwoFactorAuthentication(): bool
    {
        return !is_null($this->two_factor_type)
            && !is_null($this->two_factor_secret)
            && !is_null($this->two_factor_confirmed_at);
    }

    /**
     * Get the user's two factor authentication recovery codes.
     */
    public function recoveryCodes(): array
    {
        $codes = Crypt::decrypt($this->two_factor_recovery_codes);

        return json_decode($codes, true);
    }

    /**
     * Replace the given recovery code with a new one in the user's stored codes.
     */
    public function replaceRecoveryCode(string $code): void
    {
        $codes = Crypt::decrypt($this->two_factor_recovery_codes);

        $newCodes = Str::replace($code, RecoveryCode::generate(), $codes);

        $this->forceFill(['two_factor_recovery_codes' => Crypt::encrypt($newCodes)])->save();
    }

    /**
     * Get the QR code SVG of the user's two factor authentication QR code URL.
     */
    public function twoFactorQrCodeSvg(): string
    {
        $colors = Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72));

        $image = new ImageRenderer(new RendererStyle(192, 0, null, null, $colors), new SvgImageBackEnd());

        $svg = (new Writer($image))->writeString($this->twoFactorQrCodeUrl());

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * Get the two factor authentication QR code URL.
     */
    public function twoFactorQrCodeUrl(): string
    {
        $secret = Crypt::decrypt($this->two_factor_secret);

        return app(TwoFactorAuthentication::class)->qrCodeUrl(config('app.name'), $this->email, $secret);
    }
}
