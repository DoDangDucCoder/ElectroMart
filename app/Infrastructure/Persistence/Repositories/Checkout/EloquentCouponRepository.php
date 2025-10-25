<?php

namespace App\Infrastructure\Persistence\Repositories\Checkout;

use App\Domain\Checkout\Repositories\CouponRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Marketing\CouponRedemption;
use Carbon\Carbon;

class EloquentCouponRepository implements CouponRepositoryInterface
{
    public function findActiveByCode(string $code): ?Coupon
    {
        $now = Carbon::now();

        return Coupon::where('code', $code)
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->first();
    }

    public function findById(int $id): ?Coupon
    {
        return Coupon::find($id);
    }

    public function incrementUsage(Coupon $coupon): void
    {
        $coupon->increment('used_count');
    }

    public function countRedemptionsForCustomer(Coupon $coupon, ?int $customerId): int
    {
        if (! $customerId) {
            return 0;
        }

        return CouponRedemption::where('coupon_id', $coupon->id)
            ->where('customer_id', $customerId)
            ->count();
    }

    public function recordRedemption(Coupon $coupon, ?int $customerId, ?int $orderId, float $discount): void
    {
        CouponRedemption::create([
            'coupon_id' => $coupon->id,
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'discount_amount' => $discount,
        ]);
    }
}
