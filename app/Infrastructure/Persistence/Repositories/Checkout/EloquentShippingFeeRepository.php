<?php

namespace App\Infrastructure\Persistence\Repositories\Checkout;

use App\Domain\Checkout\Repositories\ShippingFeeRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Shipping\ShippingFee;

class EloquentShippingFeeRepository implements ShippingFeeRepositoryInterface
{
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
