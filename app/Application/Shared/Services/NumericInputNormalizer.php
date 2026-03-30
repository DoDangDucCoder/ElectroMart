<?php

namespace App\Application\Shared\Services;

class NumericInputNormalizer
{
    public function digitsOnly(string|int|float|null $value): int
    {
        $normalized = preg_replace('/[^0-9]/', '', (string) $value);

        return (int) ($normalized ?: 0);
    }
}
