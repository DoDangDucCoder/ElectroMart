<?php

namespace App\Domain\Checkout\Repositories;

use App\Infrastructure\Persistence\Eloquent\Shipping\ShippingFee;

interface ShippingFeeRepositoryInterface
{
    public function findFee(?int $cityId, ?int $provinceId, ?int $wardId): ?ShippingFee;
}
