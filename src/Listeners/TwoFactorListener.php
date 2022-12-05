<?php

namespace Anfragen\TwoFactor\Listeners;

use Anfragen\TwoFactor\Actions\SendLoginCode;
use Illuminate\Auth\Events\Login;

class TwoFactorListener
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        app(SendLoginCode::class)->handle($event->user);
    }
}
