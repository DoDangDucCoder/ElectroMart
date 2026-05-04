<?php

namespace App\Notifications;

use App\Domain\Order\Entities\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        private readonly Order $order
    ) {
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [30, 120, 300];
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

    public function failed(Throwable $exception): void
    {
        Log::error('Order confirmation notification failed permanently.', [
            'order_id' => $this->order->id ?? null,
            'customer_id' => $this->order->customer->id ?? null,
            'exception' => $exception->getMessage(),
        ]);
    }
}
