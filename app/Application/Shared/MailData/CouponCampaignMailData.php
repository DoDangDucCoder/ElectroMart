<?php

namespace App\Application\Shared\MailData;

use App\Models\Coupon;

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
            couponCode: (string) ($coupon->coupon_code ?? 'N/A'),
            couponTime: (int) ($coupon->coupon_time ?? 0),
            couponCondition: (int) ($coupon->coupon_condition ?? 1),
            couponNumber: (int) ($coupon->coupon_number ?? 0),
            startCoupon: (string) ($coupon->coupon_date_start ?? 'Chưa cập nhật'),
            endCoupon: (string) ($coupon->coupon_date_end ?? 'Chưa cập nhật'),
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
