<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

/**
 * TODO: Implement SMS gateway integration when provider credentials are available (Part 3 checklist).
 */
class SmsNotificationChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        // $message = $notification->toSms($notifiable);
        // Dispatch to SMS provider API here.
    }
}
