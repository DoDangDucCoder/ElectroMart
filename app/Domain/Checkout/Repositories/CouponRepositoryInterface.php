<?php

namespace App\Domain\Checkout\Repositories;

use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Marketing\CouponRedemption;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CouponRepositoryInterface
{
    public function findActiveByCode(string $code): ?Coupon;

    public function findByCode(string $code): ?Coupon;

    public function findById(int $id): ?Coupon;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function incrementUsage(Coupon $coupon): void;

    public function reserveUsage(Coupon $coupon, ?int $customerId, float $discount, string $sessionId, CarbonInterface $reservedUntil): CouponRedemption;

    public function findActiveReservation(int $redemptionId, string $sessionId): ?CouponRedemption;

    public function redeemReservation(CouponRedemption $redemption, ?int $customerId, ?int $orderId, float $discount): void;

    public function releaseReservation(CouponRedemption $redemption, string $reason): bool;

    public function releaseExpiredReservations(?CarbonInterface $now = null): int;

    public function countRedemptionsForCustomer(Coupon $coupon, ?int $customerId): int;

    public function recordRedemption(Coupon $coupon, ?int $customerId, ?int $orderId, float $discount): void;
}
