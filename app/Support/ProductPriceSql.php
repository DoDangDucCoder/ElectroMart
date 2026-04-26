<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class ProductPriceSql
{
    public static function canQueryEffectivePrice(string $table = 'products'): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, 'price');
    }

    public static function effectivePriceExpression(string $table = 'products'): string
    {
        if (! self::canQueryEffectivePrice($table)) {
            return '0';
        }

        $priceColumn = self::column($table, 'price');

        if (! Schema::hasColumn($table, 'sale_price')) {
            return $priceColumn;
        }

        $salePriceColumn = self::column($table, 'sale_price');

        return "CASE WHEN {$salePriceColumn} IS NOT NULL AND {$salePriceColumn} > 0 THEN {$salePriceColumn} ELSE {$priceColumn} END";
    }

    public static function normalizeFilterBinding(mixed $value): int|float|string|null
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return self::normalizeNumericBinding($value);
        }

        if (! is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);

        if ($trimmed === '' || ! is_numeric($trimmed)) {
            return $value;
        }

        if (preg_match('/^-?\d+$/', $trimmed) === 1) {
            return (int) $trimmed;
        }

        return self::normalizeNumericBinding((float) $trimmed);
    }

    private static function normalizeNumericBinding(int|float $value): int|float
    {
        if (is_int($value)) {
            return $value;
        }

        $rounded = round($value);

        if (abs($value - $rounded) < 0.0000001) {
            return (int) $rounded;
        }

        return $value;
    }

    private static function column(string $table, string $column): string
    {
        return $table === 'products' ? $column : "{$table}.{$column}";
    }
}
