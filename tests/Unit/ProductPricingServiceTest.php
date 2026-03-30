<?php

namespace Tests\Unit;

use App\Application\Catalog\Services\ProductPricingService;
use Tests\TestCase;

class ProductPricingServiceTest extends TestCase
{
    protected ProductPricingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ProductPricingService::class);
    }

    public function test_it_calculates_percent_sale_correctly(): void
    {
        $payload = $this->service->derivePricingPayload(2000000, 'percent', 10);

        $this->assertSame(200000.0, (float) $payload['sale_amount']);
        $this->assertSame(1800000.0, (float) $payload['final_price']);
        $this->assertSame(1800000.0, (float) $payload['sale_price']);
    }

    public function test_it_calculates_fixed_sale_correctly(): void
    {
        $payload = $this->service->derivePricingPayload(2000000, 'fixed', '200000');

        $this->assertSame(200000.0, (float) $payload['sale_amount']);
        $this->assertSame(1800000.0, (float) $payload['final_price']);
    }

    public function test_it_builds_sale_explanation_for_fixed_sale(): void
    {
        $explanation = $this->service->buildSaleExplanation(2000000, 'fixed', 200000);

        $this->assertSame('Giá sau giảm = 2.000.000đ - 200.000đ = 1.800.000đ', $explanation);
    }
}
