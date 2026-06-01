<?php

namespace App\Application\Catalog\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductVariant;

class ProductPricingService
{
    public function normalizeSaleType(?string $saleType): ?string
    {
        $normalized = $saleType ? strtolower(trim($saleType)) : null;

        return in_array($normalized, ['percent', 'fixed'], true) ? $normalized : null;
    }

    public function calculateSaleAmount(float $price, ?string $saleType, float|int|string|null $saleValue): float
    {
        $saleType = $this->normalizeSaleType($saleType);
        $price = max(0, (float) $price);
        $saleValue = $this->normalizeNumber($saleValue);

        if (! $saleType || $saleValue === null || $saleValue <= 0 || $price <= 0) {
            return 0.0;
        }

        if ($saleType === 'percent') {
            $boundedPercent = min(100, max(0, $saleValue));

            return round(($price * $boundedPercent) / 100, 2);
        }

        return round(min($saleValue, $price), 2);
    }

    public function calculateFinalPrice(float $price, ?string $saleType, float|int|string|null $saleValue): float
    {
        $price = max(0, (float) $price);
        $saleAmount = $this->calculateSaleAmount($price, $saleType, $saleValue);

        return round(max(0, $price - $saleAmount), 2);
    }

    public function calculateProfit(float $finalPrice, float|int|string|null $costPrice): float
    {
        $cost = max(0, (float) ($this->normalizeNumber($costPrice) ?? 0));

        return round($finalPrice - $cost, 2);
    }

    public function derivePricingPayload(
        float $price,
        ?string $saleType,
        float|int|string|null $saleValue,
        float|int|string|null $manualSalePrice = null
    ): array {
        $price = max(0, (float) $price);
        $saleType = $this->normalizeSaleType($saleType);
        $saleValue = $this->normalizeNumber($saleValue);

        if ($saleType && $saleValue !== null && $saleValue > 0) {
            $saleAmount = $this->calculateSaleAmount($price, $saleType, $saleValue);
            $finalPrice = $this->calculateFinalPrice($price, $saleType, $saleValue);

            return [
                'sale_type' => $saleType,
                'sale_value' => $saleValue,
                'sale_amount' => $saleAmount,
                'sale_price' => $saleAmount > 0 ? $finalPrice : 0.0,
                'final_price' => $finalPrice,
            ];
        }

        $manualSalePrice = $this->normalizeNumber($manualSalePrice);

        if ($manualSalePrice !== null && $manualSalePrice > 0 && $manualSalePrice < $price) {
            $saleAmount = round($price - $manualSalePrice, 2);

            return [
                'sale_type' => 'fixed',
                'sale_value' => $saleAmount,
                'sale_amount' => $saleAmount,
                'sale_price' => round($manualSalePrice, 2),
                'final_price' => round($manualSalePrice, 2),
            ];
        }

        return [
            'sale_type' => null,
            'sale_value' => 0.0,
            'sale_amount' => 0.0,
            'sale_price' => $price,
            'final_price' => $price,
        ];
    }

    public function buildSaleExplanation(float $price, ?string $saleType, float|int|string|null $saleValue): ?string
    {
        $saleType = $this->normalizeSaleType($saleType);
        $saleValue = $this->normalizeNumber($saleValue);

        if (! $saleType || $saleValue === null || $saleValue <= 0) {
            return null;
        }

        $finalPrice = $this->calculateFinalPrice($price, $saleType, $saleValue);

        if ($saleType === 'percent') {
            $formattedPercent = rtrim(rtrim(number_format($saleValue, 2, '.', ''), '0'), '.');

            return sprintf(
                'Giá sau giảm = %s - %s%% = %s',
                $this->formatMoney($price),
                $formattedPercent,
                $this->formatMoney($finalPrice)
            );
        }

        return sprintf(
            'Giá sau giảm = %s - %s = %s',
            $this->formatMoney($price),
            $this->formatMoney($saleValue),
            $this->formatMoney($finalPrice)
        );
    }

    public function buildOrderItemSnapshot(Product|ProductVariant $sellable, int $quantity = 1): array
    {
        $price = (float) ($sellable->price ?? 0);
        $saleType = $this->normalizeSaleType($sellable->sale_type ?? null);
        $saleValue = $this->normalizeNumber($sellable->sale_value ?? null);
        $salePrice = (float) ($sellable->sale_price ?? 0);

        $pricing = $this->derivePricingPayload($price, $saleType, $saleValue, $salePrice);
        $unitPrice = (float) $pricing['final_price'];
        $unitSaleAmount = (float) $pricing['sale_amount'];
        $quantity = max(1, $quantity);

        return [
            'original_price' => $price,
            'price' => $unitPrice,
            'sale_price' => $pricing['sale_price'],
            'cost_price' => $this->normalizeNumber($sellable->cost_price ?? null) ?? 0.0,
            'sale_type' => $pricing['sale_type'],
            'sale_value' => $pricing['sale_value'],
            'sale_amount' => $unitSaleAmount,
            'discount_amount' => round($unitSaleAmount * $quantity, 2),
            'subtotal' => round($unitPrice * $quantity, 2),
            'quantity' => $quantity,
        ];
    }

    protected function normalizeNumber(float|int|string|null $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            if ($trimmed === '') {
                return null;
            }

            $normalized = preg_replace('/\s+/', '', $trimmed);

            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $normalized) === 1) {
                $normalized = str_replace('.', '', $normalized);
            } elseif (preg_match('/^\d{1,3}(,\d{3})+$/', $normalized) === 1) {
                $normalized = str_replace(',', '', $normalized);
            } else {
                $normalized = str_replace(',', '.', $normalized);

                if (substr_count($normalized, '.') > 1) {
                    $normalized = str_replace('.', '', $normalized);
                }
            }

            $normalized = preg_replace('/[^0-9.]/', '', $normalized);

            if ($normalized === '') {
                return null;
            }

            return (float) $normalized;
        }

        return (float) $value;
    }

    protected function formatMoney(float $amount): string
    {
        return number_format($amount, 0, ',', '.') . 'đ';
    }
}
