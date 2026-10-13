<?php

namespace App\Application\Checkout\Services;

use App\Domain\Checkout\Repositories\ShippingFeeRepositoryInterface;
use Illuminate\Support\Facades\Session;

class ShippingService
{
    public const SESSION_KEY = 'fee';
    public const DEFAULT_FEE = 30000;

    public function __construct(
        private readonly ShippingFeeRepositoryInterface $shippingFees
    ) {
    }

    public function calculateFee(?int $cityId, ?int $provinceId, ?int $wardId, float $default = self::DEFAULT_FEE): float
    {
        $fee = $this->shippingFees->findFee($cityId, $provinceId, $wardId);

        return (float) ($fee?->amount ?? $default);
    }

    public function calculateVietnam2025Fee(?string $provinceCode, ?string $wardCode, float $default = self::DEFAULT_FEE): float
    {
        return $this->resolveVietnam2025Fee($provinceCode, $wardCode, $default)['fee'];
    }

    public function resolveVietnam2025Fee(?string $provinceCode, ?string $wardCode, float $default = self::DEFAULT_FEE): array
    {
        $fee = $this->shippingFees->findVietnam2025Fee($provinceCode, $wardCode);

        if (! $fee) {
            return [
                'fee' => (float) $default,
                'scope' => 'system_default',
                'label' => 'Phí mặc định hệ thống',
            ];
        }

        $feeProvinceCode = trim((string) $fee->vietnam_province_code);
        $feeWardCode = trim((string) $fee->vietnam_ward_code);
        $scope = 'default';
        $label = 'Mặc định toàn quốc';

        if ($feeProvinceCode !== '' && $feeWardCode !== '') {
            $scope = 'ward';
            $label = 'Theo xã/phường/khu vực';
        } elseif ($feeProvinceCode !== '') {
            $scope = 'province';
            $label = 'Theo tỉnh/thành phố';
        }

        return [
            'fee' => (float) $fee->amount,
            'scope' => $scope,
            'label' => $label,
        ];
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
