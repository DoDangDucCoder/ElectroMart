<?php

namespace App\Application\Shared\MailData;

use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;

class CouponCampaignMailData
{
    public function __construct(
        public readonly string $couponCode,
        public readonly int $couponTime,
        public readonly int $couponCondition,
        public readonly int $couponNumber,
        public readonly string $startCoupon,
        public readonly string $endCoupon,
        public readonly string $audienceLabel = 'khách hàng',
        public readonly string $shopName = 'DangDuc',
        public readonly string $shopUrl = '',
        public readonly string $loginUrl = ''
    ) {}

    public static function fromCoupon(
        Coupon $coupon,
        string $audienceLabel,
        ?string $shopUrl = null,
        ?string $loginUrl = null,
        string $shopName = 'DangDuc'
    ): self {
        return new self(
            couponCode: (string) ($coupon->code ?? 'N/A'),
            couponTime: (int) ($coupon->usage_limit ?? 0),
            couponCondition: $coupon->type === 'percent' ? 1 : 2,
            couponNumber: (int) ($coupon->value ?? 0),
            startCoupon: $coupon->starts_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') ?? 'Chưa cập nhật',
            endCoupon: $coupon->expires_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') ?? 'Chưa cập nhật',
            audienceLabel: $audienceLabel,
            shopName: $shopName,
            shopUrl: $shopUrl ?? url('/'),
            loginUrl: $loginUrl ?? url('/dang-nhap'),
        );
    }

    public function discountLabel(): string
    {
        if ($this->couponCondition === 1) {
            return 'Giảm '.$this->couponNumber.'%';
        }

        return 'Giảm '.number_format($this->couponNumber, 0, ',', '.').'đ';
    }

    public function couponTimeLabel(): string
    {
        return number_format($this->couponTime).' mã';
    }

    public function activeWindowLabel(): string
    {
        return $this->startCoupon.' đến '.$this->endCoupon;
    }
}
