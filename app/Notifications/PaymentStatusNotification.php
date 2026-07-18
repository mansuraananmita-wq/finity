<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public bool $success) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->success
                ? __('messages.payment_success_subject')
                : __('messages.payment_failed_subject'))
            ->greeting(__('messages.hello', ['name' => $notifiable->name]));

        if ($this->success) {
            return $message
                ->line(__('messages.payment_success_email', ['id' => $this->order->id]))
                ->action(__('messages.view_order'), route('orders.show', $this->order));
        }

        return $message
            ->line(__('messages.payment_failed_email', ['id' => $this->order->id]))
            ->action(__('messages.retry_payment'), route('orders.show', $this->order));
    }
}
