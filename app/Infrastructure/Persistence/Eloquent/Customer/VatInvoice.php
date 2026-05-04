<?php

namespace App\Infrastructure\Persistence\Eloquent\Customer;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VatInvoice extends BaseModel
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_REJECTED = 'rejected';

    protected $table = 'vat_invoices';

    protected $casts = [
        'value' => 'decimal:2',
        'issued_at' => 'datetime',
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

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_ISSUED => 'Đã xuất',
            self::STATUS_PROCESSING => 'Đang xử lý',
            self::STATUS_REJECTED => 'Cần bổ sung',
            default => 'Chờ xử lý',
        };
    }
}
