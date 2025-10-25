<?php

namespace App\Application\Checkout\Services;

use App\Domain\Checkout\Repositories\CouponRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use Illuminate\Support\Facades\Session;
use RuntimeException;

class CouponService
{
    protected const SESSION_KEY = 'coupon';

    public function __construct(
        private readonly CouponRepositoryInterface $coupons
    ) {
    }

    public function current(): ?array
    {
        return Session::get(self::SESSION_KEY);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function apply(string $code, float $subtotal, ?int $customerId = null): array
    {
        $coupon = $this->coupons->findActiveByCode($code);

        if (! $coupon) {
            throw new RuntimeException('Mã giảm giá không hợp lệ hoặc đã hết hạn.');
        }

        $this->assertUsageAvailable($coupon, $customerId);

        $discount = $this->calculateDiscount($coupon, $subtotal);

        $payload = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'discount_amount' => $discount,
            'max_discount' => (float) ($coupon->max_discount ?? 0),
        ];

        Session::put(self::SESSION_KEY, $payload);
        Session::save();

        return $payload;
    }

    public function resolveCouponFromSession(): ?Coupon
    {
        $data = $this->current();

        if (! $data) {
            return null;
        }

        if (isset($data['id'])) {
            return $this->coupons->findById((int) $data['id']);
        }

        if (! empty($data['code'])) {
            return $this->coupons->findActiveByCode($data['code']);
        }

        return null;
    }

    public function redeem(Coupon $coupon, ?int $customerId, ?int $orderId, float $discount): void
    {
        $this->coupons->recordRedemption($coupon, $customerId, $orderId, $discount);
        $this->coupons->incrementUsage($coupon);
    }

    protected function assertUsageAvailable(Coupon $coupon, ?int $customerId): void
    {
        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw new RuntimeException('Mã giảm giá đã đạt giới hạn sử dụng.');
        }

        if ($coupon->usage_per_customer !== null) {
            $customerUsage = $this->coupons->countRedemptionsForCustomer($coupon, $customerId);
            if ($customerUsage >= $coupon->usage_per_customer) {
                throw new RuntimeException('Bạn đã sử dụng mã giảm giá này tối đa số lần cho phép.');
            }
        }
    }

    protected function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0;
        }

        return match ($coupon->type) {
            'percent' => $this->calculatePercentDiscount($coupon, $subtotal),
            'fixed' => min((float) $coupon->value, $subtotal),
            'shipping' => (float) $coupon->value,
            default => 0,
        };
    }

    protected function calculatePercentDiscount(Coupon $coupon, float $subtotal): float
    {
        $discount = $subtotal * ((float) $coupon->value / 100);

        if ($coupon->max_discount !== null) {
            $discount = min($discount, (float) $coupon->max_discount);
        }

        return $discount;
    }
}
