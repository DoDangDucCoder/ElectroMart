<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogDataAuditCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_audit_allows_external_product_images(): void
    {
        $brand = Brand::create([
            'name' => 'Audit Brand',
            'slug' => 'audit-brand',
            'is_active' => true,
        ]);

        $category = ProductCategory::create([
            'name' => 'Audit Category',
            'slug' => 'audit-category',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'External Image Product',
            'slug' => 'external-image-product',
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 1000000,
            'sale_price' => 900000,
            'stock_quantity' => 3,
            'thumbnail_path' => 'https://cdn.example.com/product.webp',
            'meta' => ['tags' => ['audit']],
            'is_active' => true,
        ]);

        $product->categories()->attach($category->id);
        $product->media()->create([
            'file_path' => 'https://cdn.example.com/product-gallery.webp',
            'disk' => 'public',
            'collection' => 'gallery',
            'display_order' => 1,
        ]);

        $this->artisan('catalog:audit-data --fail-on-issues')
            ->expectsOutputToContain('Catalog audit passed.')
            ->assertExitCode(0);
    }

    public function test_catalog_audit_can_fail_on_blocking_issues(): void
    {
        Product::create([
            'name' => 'Bad Price Product',
            'slug' => 'bad-price-product',
            'price' => 1000000,
            'sale_price' => 1000000,
            'stock_quantity' => 1,
            'is_active' => true,
        ]);

        $this->artisan('catalog:audit-data --fail-on-issues')
            ->expectsOutputToContain('Catalog audit found')
            ->assertExitCode(1);
    }
}
