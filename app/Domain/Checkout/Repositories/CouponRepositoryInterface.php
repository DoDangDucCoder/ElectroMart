<?php

namespace App\Domain\Checkout\Repositories;

use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CouponRepositoryInterface
{
    public function findActiveByCode(string $code): ?Coupon;

    public function findByCode(string $code): ?Coupon;

    public function findById(int $id): ?Coupon;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function incrementUsage(Coupon $coupon): void;

    public function countRedemptionsForCustomer(Coupon $coupon, ?int $customerId): int;

    public function recordRedemption(Coupon $coupon, ?int $customerId, ?int $orderId, float $discount): void;
}
