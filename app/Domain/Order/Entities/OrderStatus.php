<?php

namespace App\Domain\Order\Entities;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function isActive(): bool
    {
        return ! in_array($this, [self::CANCELLED, self::REFUNDED], true);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::PENDING, self::CONFIRMED], true);
    }

    public function canBeRefunded(): bool
    {
        return in_array($this, [self::DELIVERED], true);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Chờ xử lý',
            self::CONFIRMED => 'Đã xác nhận',
            self::PROCESSING => 'Đang xử lý',
            self::SHIPPED => 'Đã gửi hàng',
            self::DELIVERED => 'Đã giao hàng',
            self::CANCELLED => 'Đã hủy',
            self::REFUNDED => 'Đã hoàn tiền',
        };
    }
}
