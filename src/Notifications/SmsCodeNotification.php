<?php

namespace Anfragen\TwoFactor\Notifications;

use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Crypt;

class SmsCodeNotification extends Notification
{
    /**
     * Get the notification's channels.
     */
    public function via(mixed $notifiable): array
    {
        return ['vonage'];
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     */
    public function toVonage($notifiable): VonageMessage
    {
        $name = config('app.name');

        $code = Crypt::decrypt($notifiable->two_factor_secret);

        return (new VonageMessage())
            ->clientReference((string)$notifiable->id)
            ->content("[{$name}] - Your code to activate two factor is: {$code}");
    }
}
