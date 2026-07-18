<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
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
            ->subject(__('messages.order_status_subject', ['status' => $this->order->status]))
            ->greeting(__('messages.hello', ['name' => $notifiable->name]))
            ->line(__('messages.order_status_email', [
                'id' => $this->order->id,
                'status' => __("messages.status_{$this->order->status}"),
            ]))
            ->action(__('messages.view_order'), route('orders.show', $this->order));
    }
}
