<?php

namespace Anfragen\TwoFactor\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Crypt;

class EmailCodeNotification extends Notification
{
    /**
     * Get the notification's channels.
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $name = config('app.name');

        $code = Crypt::decrypt($notifiable->two_factor_secret);

        return (new MailMessage())
            ->subject("[{$name}] - Your access code is {$code}")
            ->line('Your code to activate two factor is:')
            ->line($code)
            ->line('This code will expire in 5 minutes.');
    }
}
