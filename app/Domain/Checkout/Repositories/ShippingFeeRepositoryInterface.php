<?php

namespace App\Domain\Checkout\Repositories;

use App\Infrastructure\Persistence\Eloquent\Shipping\ShippingFee;

interface ShippingFeeRepositoryInterface
{
    public function findFee(?int $cityId, ?int $provinceId, ?int $wardId): ?ShippingFee;

    public function findVietnam2025Fee(?string $provinceCode, ?string $wardCode): ?ShippingFee;
}
