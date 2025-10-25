<?php

namespace App\Notifications;

use App\Domain\Customer\Entities\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Customer $customer
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Chào mừng bạn đến với cửa hàng của chúng tôi!')
            ->greeting('Xin chào ' . $this->customer->name . '!')
            ->line('Cảm ơn bạn đã đăng ký tài khoản tại cửa hàng của chúng tôi.')
            ->line('Bây giờ bạn có thể:')
            ->line('• Mua sắm các sản phẩm yêu thích')
            ->line('• Theo dõi đơn hàng của bạn')
            ->line('• Nhận các ưu đãi đặc biệt')
            ->action('Bắt đầu mua sắm', url('/'))
            ->line('Chúc bạn có trải nghiệm mua sắm tuyệt vời!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'message' => 'Chào mừng bạn đến với cửa hàng!',
        ];
    }
}
