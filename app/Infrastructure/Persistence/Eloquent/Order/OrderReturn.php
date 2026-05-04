<?php

namespace App\Infrastructure\Persistence\Eloquent\Order;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderReturn extends BaseModel
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REFUNDED = 'refunded';

    protected $table = 'order_returns';

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'quantity' => 'integer',
        'refunded_at' => 'datetime',
        'meta' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Đã chấp thuận',
            self::STATUS_REJECTED => 'Từ chối',
            self::STATUS_REFUNDED => 'Đã hoàn tiền',
            default => 'Chờ xử lý',
        };
    }

    public function statusClass(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_REFUNDED => 'blue',
            default => 'amber',
        };
    }
}
