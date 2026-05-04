<?php

namespace App\Infrastructure\Persistence\Eloquent\Customer;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends BaseModel
{
    use SoftDeletes;

    public const TYPE_CARD = 'card';
    public const TYPE_WALLET = 'wallet';
    public const TYPE_BANK = 'bank_account';
    public const TYPE_COD = 'cod';

    protected $table = 'customer_payment_methods';

    protected $casts = [
        'meta' => 'array',
        'is_default' => 'boolean',
        'is_verified' => 'boolean',
        'expiry_month' => 'integer',
        'expiry_year' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function maskedNumber(): string
    {
        $tail = trim((string) $this->account_identifier);

        return match ($this->type) {
            self::TYPE_CARD => '•••• ' . substr($tail, -4),
            self::TYPE_BANK => '•••• ' . substr($tail, -4),
            self::TYPE_WALLET => $tail,
            default => $tail,
        };
    }

    public function expiryLabel(): ?string
    {
        if (! $this->expiry_month || ! $this->expiry_year) {
            return null;
        }

        return sprintf('%02d/%s', $this->expiry_month, substr((string) $this->expiry_year, -2));
    }

    public function providerLabel(): string
    {
        return match (strtolower((string) $this->provider)) {
            'visa' => 'Visa',
            'mastercard' => 'Mastercard',
            'jcb' => 'JCB',
            'amex' => 'American Express',
            'momo' => 'Ví MoMo',
            'zalopay' => 'Ví ZaloPay',
            'vnpay' => 'VNPay',
            'shopeepay' => 'Ví ShopeePay',
            'vietcombank' => 'Vietcombank',
            'techcombank' => 'Techcombank',
            'bidv' => 'BIDV',
            'vietinbank' => 'VietinBank',
            'mbbank' => 'MB Bank',
            'cod' => 'Thanh toán khi nhận hàng (COD)',
            default => ucfirst($this->provider),
        };
    }
}
