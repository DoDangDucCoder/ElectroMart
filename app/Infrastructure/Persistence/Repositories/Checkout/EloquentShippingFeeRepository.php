<?php

namespace App\Infrastructure\Persistence\Repositories\Checkout;

use App\Domain\Checkout\Repositories\ShippingFeeRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Shipping\ShippingFee;

class EloquentShippingFeeRepository implements ShippingFeeRepositoryInterface
{
    public function findVietnam2025Fee(?string $provinceCode, ?string $wardCode = null): ?ShippingFee
    {
        // Thử match chính xác province + ward trước
        if ($provinceCode && $wardCode) {
            $fee = ShippingFee::query()
                ->where('vietnam_province_code', $provinceCode)
                ->where('vietnam_ward_code', $wardCode)
                ->first();

            if ($fee) {
                return $fee;
            }
        }

        // Fallback: match theo province-only (ward rỗng hoặc null)
        if ($provinceCode) {
            $fee = ShippingFee::query()
                ->where('vietnam_province_code', $provinceCode)
                ->where(function ($query) {
                    $query->whereNull('vietnam_ward_code')
                        ->orWhere('vietnam_ward_code', '');
                })
                ->first();

            if ($fee) {
                return $fee;
            }
        }

        // Fallback cuối: phí mặc định toàn quốc.
        return ShippingFee::query()
            ->where(function ($query) {
                $query->whereNull('vietnam_province_code')
                    ->orWhere('vietnam_province_code', '');
            })
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

        $fee = $query->orderByDesc('ward_id')
            ->orderByDesc('province_id')
            ->orderByDesc('city_id')
            ->first();

        if ($fee) {
            return $fee;
        }

        return ShippingFee::query()
            ->whereNull('city_id')
            ->whereNull('province_id')
            ->whereNull('ward_id')
            ->first();
    }
}
