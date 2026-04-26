<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImportDemoCatalogCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_catalog_records_from_dummyjson(): void
    {
        $brand = Brand::create([
            'name' => 'Apple',
            'slug' => 'apple',
            'code' => 'APPLE',
            'description' => 'Existing Apple description',
            'is_active' => true,
            'display_order' => 1,
            'meta' => null,
        ]);

        $this->fakeProducts([$this->dummyProduct()]);

        $this->artisan('demo:import-catalog --source=dummyjson --limit=0 --categories=smartphones')
            ->expectsOutputToContain('Demo catalog import completed.')
            ->assertExitCode(0);

        $brand->refresh();
        $this->assertSame('Existing Apple description', $brand->description);

        $category = ProductCategory::query()->where('slug', 'dien-thoai-thong-minh')->first();
        $this->assertNotNull($category);
        $this->assertSame('Điện thoại thông minh', $category->name);
        $this->assertSame('dummyjson', $category->meta['source']);
        $this->assertSame('smartphones', $category->meta['source_category']);

        $product = Product::query()->where('sku', 'DJSON-101-ABC123')->first();
        $this->assertNotNull($product);
        $this->assertSame('iPhone Demo', $product->name);
        $this->assertSame('dummyjson-101-iphone-demo', $product->slug);
        $this->assertSame($brand->id, $product->brand_id);
        $this->assertSame($category->id, $product->primary_category_id);
        $this->assertSame(25000000.0, (float) $product->price);
        $this->assertSame('percent', $product->sale_type);
        $this->assertSame(10.0, (float) $product->sale_value);
        $this->assertSame(22500000.0, (float) $product->sale_price);
        $this->assertSame('https://cdn.example.com/iphone-demo-thumb.png', $product->thumbnail_path);
        $this->assertSame('dummyjson', $product->meta['source']);
        $this->assertSame(101, $product->meta['source_id']);
        $this->assertSame(['smartphones', 'apple'], $product->meta['tags']);

        $this->assertDatabaseHas('product_category_product', [
            'product_id' => $product->id,
            'product_category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('product_media', [
            'product_id' => $product->id,
            'file_path' => 'https://cdn.example.com/iphone-demo-thumb.png',
            'collection' => 'thumbnail',
        ]);

        $this->assertDatabaseHas('product_media', [
            'product_id' => $product->id,
            'file_path' => 'https://cdn.example.com/iphone-demo-gallery.png',
            'collection' => 'gallery',
        ]);
    }

    public function test_import_is_idempotent_and_updates_existing_demo_products(): void
    {
        $updatedProduct = $this->dummyProduct([
            'title' => 'iPhone Demo Updated',
            'stock' => 44,
        ]);

        Http::fake([
            'https://dummyjson.com/products*' => Http::sequence()
                ->push($this->productsPayload([$this->dummyProduct()]))
                ->push($this->productsPayload([$updatedProduct])),
        ]);

        $this->artisan('demo:import-catalog --categories=smartphones')
            ->assertExitCode(0);

        $this->artisan('demo:import-catalog --categories=smartphones')
            ->assertExitCode(0);

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('product_media', 2);

        $product = Product::query()->where('sku', 'DJSON-101-ABC123')->firstOrFail();
        $this->assertSame('iPhone Demo Updated', $product->name);
        $this->assertSame('dummyjson-101-iphone-demo-updated', $product->slug);
        $this->assertSame(44, $product->stock_quantity);
    }

    public function test_import_does_not_overwrite_real_products_with_similar_slugs(): void
    {
        $brand = Brand::create([
            'name' => 'Real Brand',
            'slug' => 'real-brand',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $category = ProductCategory::create([
            'name' => 'Real Category',
            'slug' => 'real-category',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $realProduct = Product::create([
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'name' => 'Real Store Product',
            'slug' => 'dummyjson-101-iphone-demo-copy',
            'sku' => 'REAL-IPHONE',
            'unit' => 'Chiếc',
            'price' => 30000000,
            'sale_price' => null,
            'cost_price' => 20000000,
            'stock_quantity' => 5,
            'stock_alert_threshold' => 1,
            'is_active' => true,
            'is_featured' => false,
            'display_order' => 1,
            'description' => 'Real product should stay unchanged',
            'content' => 'Real content',
            'views_count' => 0,
            'meta' => ['source' => 'real-store'],
        ]);

        $this->fakeProducts([$this->dummyProduct()]);

        $this->artisan('demo:import-catalog --categories=smartphones')
            ->assertExitCode(0);

        $this->assertDatabaseCount('products', 2);
        $realProduct->refresh();
        $this->assertSame('Real Store Product', $realProduct->name);
        $this->assertSame('REAL-IPHONE', $realProduct->sku);
        $this->assertSame('real-store', $realProduct->meta['source']);
    }

    public function test_dry_run_does_not_write_catalog_data(): void
    {
        $this->fakeProducts([$this->dummyProduct()]);

        $this->artisan('demo:import-catalog --categories=smartphones --dry-run')
            ->expectsOutputToContain('Dry run complete. No data was written.')
            ->assertExitCode(0);

        $this->assertDatabaseCount('brands', 0);
        $this->assertDatabaseCount('product_categories', 0);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('product_media', 0);
    }

    public function test_api_failure_returns_error_without_writing_catalog_data(): void
    {
        Http::fake([
            'https://dummyjson.com/products*' => Http::response(['message' => 'Server error'], 500),
        ]);

        $this->artisan('demo:import-catalog --categories=smartphones')
            ->expectsOutputToContain('Unable to fetch demo catalog data')
            ->assertExitCode(1);

        $this->assertDatabaseCount('brands', 0);
        $this->assertDatabaseCount('product_categories', 0);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('product_media', 0);
    }

    public function test_import_falls_back_when_demo_thumbnail_is_invalid(): void
    {
        $this->fakeProducts([
            $this->dummyProduct([
                'thumbnail' => 'not-a-url',
                'images' => ['not-a-url', 'also-not-a-url'],
            ]),
        ]);

        $this->artisan('demo:import-catalog --categories=smartphones')
            ->assertExitCode(0);

        $product = Product::query()->where('sku', 'DJSON-101-ABC123')->firstOrFail();

        $this->assertSame('uploads/product/no-image.svg', $product->thumbnail_path);
        $this->assertDatabaseCount('product_media', 0);
    }

    private function fakeProducts(array $products): void
    {
        Http::fake([
            'https://dummyjson.com/products*' => Http::response($this->productsPayload($products)),
        ]);
    }

    private function productsPayload(array $products): array
    {
        return [
            'products' => $products,
            'total' => count($products),
            'skip' => 0,
            'limit' => 0,
        ];
    }

    private function dummyProduct(array $overrides = []): array
    {
        return array_replace_recursive([
            'id' => 101,
            'title' => 'iPhone Demo',
            'description' => 'A realistic sample smartphone for storefront testing.',
            'category' => 'smartphones',
            'price' => 1000,
            'discountPercentage' => 10,
            'rating' => 4.8,
            'stock' => 12,
            'tags' => ['smartphones', 'apple'],
            'brand' => 'Apple',
            'sku' => 'ABC123',
            'weight' => 2,
            'dimensions' => [
                'width' => 10,
                'height' => 20,
                'depth' => 1,
            ],
            'warrantyInformation' => '1 year warranty',
            'shippingInformation' => 'Ships in 1 week',
            'availabilityStatus' => 'In Stock',
            'returnPolicy' => '30 days return policy',
            'minimumOrderQuantity' => 1,
            'meta' => [
                'barcode' => '1234567890123',
            ],
            'thumbnail' => 'https://cdn.example.com/iphone-demo-thumb.png',
            'images' => [
                'https://cdn.example.com/iphone-demo-thumb.png',
                'https://cdn.example.com/iphone-demo-gallery.png',
            ],
        ], $overrides);
    }
}
