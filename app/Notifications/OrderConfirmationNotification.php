<?php

namespace App\Notifications;

use App\Domain\Order\Entities\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Xác nhận đơn hàng #' . $this->order->id)
            ->greeting('Xin chào ' . $this->order->customer->name . '!')
            ->line('Cảm ơn bạn đã đặt hàng tại cửa hàng của chúng tôi.')
            ->line('Mã đơn hàng: #' . $this->order->id)
            ->line('Tổng tiền: ' . number_format($this->order->total_amount) . ' VNĐ')
            ->action('Xem chi tiết đơn hàng', url('/orders/' . $this->order->id))
            ->line('Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
        ];
    }
}
