<?php

namespace App\Application\Checkout\Services;

use App\Domain\Checkout\Repositories\ShippingFeeRepositoryInterface;
use Illuminate\Support\Facades\Session;

class ShippingService
{
    public const SESSION_KEY = 'fee';

    public function __construct(
        private readonly ShippingFeeRepositoryInterface $shippingFees
    ) {
    }

    public function calculateFee(?int $cityId, ?int $provinceId, ?int $wardId, float $default = 25000): float
    {
        $fee = $this->shippingFees->findFee($cityId, $provinceId, $wardId);

        return (float) ($fee?->amount ?? $default);
    }

    public function calculateVietnam2025Fee(?string $provinceCode, ?string $wardCode, float $default = 25000): float
    {
        $fee = $this->shippingFees->findVietnam2025Fee($provinceCode, $wardCode);

        return (float) ($fee?->amount ?? $default);
    }

    public function storeFee(float $amount): void
    {
        Session::put(self::SESSION_KEY, $amount);
        Session::save();
    }

    public function getFee(): float
    {
        return (float) Session::get(self::SESSION_KEY, 0);
    }

    public function clearFee(): void
    {
        Session::forget(self::SESSION_KEY);
    }
}
