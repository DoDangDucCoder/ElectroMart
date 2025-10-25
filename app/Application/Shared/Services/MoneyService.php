<?php

namespace App\Application\Shared\Services;

use App\Domain\Shared\ValueObjects\Money;

class MoneyService
{
    public function createMoney(float $amount, string $currency = 'VND'): Money
    {
        return new Money($amount, $currency);
    }

    public function formatMoney(Money $money): string
    {
        return $money->format();
    }

    public function calculateDiscount(Money $originalPrice, Money $salePrice): Money
    {
        return $originalPrice->subtract($salePrice);
    }

    public function calculateDiscountPercentage(Money $originalPrice, Money $salePrice): int
    {
        if ($originalPrice->isZero()) {
            return 0;
        }

        $discount = $this->calculateDiscount($originalPrice, $salePrice);
        return (int) round(($discount->getAmount() / $originalPrice->getAmount()) * 100);
    }

    public function calculateTax(Money $amount, float $taxRate): Money
    {
        return $amount->multiply($taxRate / 100);
    }

    public function calculateShippingFee(Money $orderValue, array $shippingRules): Money
    {
        // Simple shipping calculation logic
        foreach ($shippingRules as $rule) {
            if ($orderValue->getAmount() >= $rule['min_amount']) {
                return $this->createMoney($rule['fee']);
            }
        }

        return $this->createMoney(0);
    }
}
