<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('messages.order_confirmation_subject', ['id' => $this->order->id]))
            ->greeting(__('messages.hello', ['name' => $notifiable->name]))
            ->line(__('messages.order_placed_email', ['id' => $this->order->id]))
            ->line(__('messages.order_total', ['amount' => number_format((float) $this->order->total_amount, 2)]))
            ->action(__('messages.view_order'), route('orders.show', $this->order));
    }
}
