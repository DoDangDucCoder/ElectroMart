<?php

namespace App\Infrastructure\Persistence\Repositories\Checkout;

use App\Domain\Checkout\Repositories\ShippingFeeRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Shipping\ShippingFee;

class EloquentShippingFeeRepository implements ShippingFeeRepositoryInterface
{
    public function findVietnam2025Fee(?string $provinceCode, ?string $wardCode = null): ?ShippingFee
    {
        if (! $provinceCode) {
            return null;
        }

        // Thử match chính xác province + ward trước
        if ($wardCode) {
            $fee = ShippingFee::query()
                ->where('vietnam_province_code', $provinceCode)
                ->where('vietnam_ward_code', $wardCode)
                ->first();

            if ($fee) {
                return $fee;
            }
        }

        // Fallback: match theo province-only (ward rỗng hoặc null)
        return ShippingFee::query()
            ->where('vietnam_province_code', $provinceCode)
            ->where(function ($query) {
                $query->whereNull('vietnam_ward_code')
                      ->orWhere('vietnam_ward_code', '');
            })
            ->first();
    }

    public function findFee(?int $cityId, ?int $provinceId, ?int $wardId): ?ShippingFee
    {
        $query = ShippingFee::query();

        if ($wardId) {
            $query->where('ward_id', $wardId);
        }

        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        return $query->orderByDesc('ward_id')
            ->orderByDesc('province_id')
            ->orderByDesc('city_id')
            ->first();
    }
}
