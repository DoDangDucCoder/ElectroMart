<?php

namespace Tests\Unit;

use App\Support\ProductPriceSql;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductPriceSqlTest extends TestCase
{
    public function test_effective_price_expression_prefers_sale_price_when_column_exists(): void
    {
        Schema::shouldReceive('hasTable')
            ->once()
            ->with('products')
            ->andReturn(true);

        Schema::shouldReceive('hasColumn')
            ->once()
            ->with('products', 'price')
            ->andReturn(true);

        Schema::shouldReceive('hasColumn')
            ->once()
            ->with('products', 'sale_price')
            ->andReturn(true);

        $expression = ProductPriceSql::effectivePriceExpression();

        $this->assertSame(
            'CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END',
            $expression
        );
        $this->assertStringNotContainsString('AS REAL', $expression);
    }

    public function test_effective_price_expression_falls_back_to_price_when_sale_price_column_is_missing(): void
    {
        Schema::shouldReceive('hasTable')
            ->once()
            ->with('products')
            ->andReturn(true);

        Schema::shouldReceive('hasColumn')
            ->once()
            ->with('products', 'price')
            ->andReturn(true);

        Schema::shouldReceive('hasColumn')
            ->once()
            ->with('products', 'sale_price')
            ->andReturn(false);

        $this->assertSame('price', ProductPriceSql::effectivePriceExpression());
    }

    public function test_effective_price_expression_falls_back_to_zero_when_price_column_is_missing(): void
    {
        Schema::shouldReceive('hasTable')
            ->once()
            ->with('products')
            ->andReturn(true);

        Schema::shouldReceive('hasColumn')
            ->once()
            ->with('products', 'price')
            ->andReturn(false);

        $this->assertSame('0', ProductPriceSql::effectivePriceExpression());
    }

    public function test_normalize_filter_binding_keeps_integral_values_as_ints(): void
    {
        $this->assertSame(15000000, ProductPriceSql::normalizeFilterBinding(15000000));
        $this->assertSame(15000000, ProductPriceSql::normalizeFilterBinding(15000000.0));
        $this->assertSame(15000000, ProductPriceSql::normalizeFilterBinding('15000000'));
        $this->assertSame(15000000, ProductPriceSql::normalizeFilterBinding('15000000.00'));
    }

    public function test_normalize_filter_binding_preserves_decimal_values_when_needed(): void
    {
        $this->assertSame(15000000.5, ProductPriceSql::normalizeFilterBinding(15000000.5));
        $this->assertSame(15000000.5, ProductPriceSql::normalizeFilterBinding('15000000.5'));
    }
}
