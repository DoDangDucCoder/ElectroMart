<?php

namespace Tests\Unit;

use App\Application\Shared\Services\EagerLoadingService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use Tests\TestCase;

class EagerLoadingServiceTest extends TestCase
{
    public function test_it_applies_expected_relationships_for_product_queries(): void
    {
        $service = new EagerLoadingService();

        $query = $service->applyEagerLoading(Product::query(), 'admin');

        $this->assertSame(
            ['brand', 'primaryCategory', 'media', 'reviews', 'variants', 'categories'],
            array_keys($query->getEagerLoads())
        );
    }

    public function test_it_uses_valid_relationship_names_for_order_brand_and_category_queries(): void
    {
        $service = new EagerLoadingService();

        $orderQuery = $service->optimizeForAdminPanel(Order::query());
        $brandQuery = $service->optimizeForAdminPanel(Brand::query());
        $categoryQuery = $service->optimizeForAdminPanel(ProductCategory::query());

        $this->assertContains('payments', array_keys($orderQuery->getEagerLoads()));
        $this->assertArrayHasKey('products', $brandQuery->getEagerLoads());
        $this->assertArrayHasKey('products', $categoryQuery->getEagerLoads());
        $this->assertContains('products.primaryCategory', array_keys($brandQuery->getEagerLoads()));
        $this->assertContains('products.brand', array_keys($categoryQuery->getEagerLoads()));
    }
}
