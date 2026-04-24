<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryPageTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;
    protected ProductCategory $category;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->productService = $this->app->make(ProductService::class);
        $this->category = ProductCategory::create([
            'name' => 'Gaming Laptop',
            'slug' => 'gaming-laptop',
            'description' => 'Danh muc test cho storefront category page',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        $this->brand = Brand::create([
            'name' => 'Storefront Brand',
            'slug' => 'storefront-brand',
            'description' => 'Brand test',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);
    }

    public function test_category_page_renders_live_listing_controls_on_fresh_db(): void
    {
        $this->createProduct('Gaming Laptop Alpha', 21000000);
        $this->createProduct('Gaming Laptop Beta', 24000000);

        $this->get('/danh-muc/' . $this->category->slug_category_product)
            ->assertOk()
            ->assertSee('g-category')
            ->assertSee('shopw-category-hero-note', false)
            ->assertSee('shopw-category-service-card', false)
            ->assertSee('kho sản phẩm hiện có')
            ->assertDontSee('kho storefront hiện tại')
            ->assertSee('id="sort"', false)
            ->assertSee('id="slider-range"', false)
            ->assertSee('Gaming Laptop Alpha')
            ->assertSee('Gaming Laptop Beta');
    }

    public function test_category_page_applies_sort_and_price_filter(): void
    {
        $low = $this->createProduct('Gaming Laptop Low', 12000000);
        $mid = $this->createProduct('Gaming Laptop Mid', 18000000);
        $high = $this->createProduct('Gaming Laptop High', 26000000);

        $response = $this->get('/danh-muc/' . $this->category->slug_category_product . '?' . http_build_query([
            'sort_by' => 'giam_dan',
            'start_price' => 15000000,
            'end_price' => 30000000,
        ]));

        $response->assertOk()
            ->assertDontSee($low->product_name)
            ->assertSeeInOrder([$high->product_name, $mid->product_name])
            ->assertSee('Sort: Giá giảm dần')
            ->assertSee('Giá: 15.000.000 VND - 30.000.000 VND');
    }

    public function test_category_page_applies_single_sided_min_price_filter(): void
    {
        $low = $this->createProduct('Gaming Laptop Budget', 12000000);
        $mid = $this->createProduct('Gaming Laptop Mid Range', 18000000);
        $high = $this->createProduct('Gaming Laptop Premium', 26000000);

        $this->get('/danh-muc/' . $this->category->slug_category_product . '?' . http_build_query([
            'start_price' => 15000000,
        ]))
            ->assertOk()
            ->assertDontSee($low->product_name)
            ->assertSee($mid->product_name)
            ->assertSee($high->product_name);
    }

    public function test_category_page_filters_using_discounted_final_price(): void
    {
        $discounted = $this->createProduct('Gaming Laptop Flash Deal', 22000000, [
            'sale_type' => 'fixed',
            'sale_value' => 7000000,
        ]);
        $regular = $this->createProduct('Gaming Laptop Regular Price', 16000000);

        $this->get('/danh-muc/' . $this->category->slug_category_product . '?' . http_build_query([
            'start_price' => 14000000,
            'end_price' => 15000000,
        ]))
            ->assertOk()
            ->assertSee($discounted->product_name)
            ->assertDontSee($regular->product_name);
    }

    public function test_category_page_sorts_by_discounted_final_price(): void
    {
        $lowestFinalPrice = $this->createProduct('Gaming Laptop Deep Discount', 30000000, [
            'sale_type' => 'fixed',
            'sale_value' => 15000000,
        ]);
        $midFinalPrice = $this->createProduct('Gaming Laptop Mid Price', 17000000);
        $highestFinalPrice = $this->createProduct('Gaming Laptop High Price', 21000000);

        $this->get('/danh-muc/' . $this->category->slug_category_product . '?sort_by=tang_dan')
            ->assertOk()
            ->assertSeeInOrder([
                $lowestFinalPrice->product_name,
                $midFinalPrice->product_name,
                $highestFinalPrice->product_name,
            ]);
    }

    public function test_category_page_renders_sale_badge_and_breakdown_on_product_cards(): void
    {
        $product = $this->createProduct('Gaming Laptop Sale', 20000000, [
            'sale_type' => 'fixed',
            'sale_value' => 2000000,
        ]);

        $this->get('/danh-muc/' . $this->category->slug_category_product)
            ->assertOk()
            ->assertSee($product->product_name)
            ->assertSee('20.000.000 VND')
            ->assertSee('18.000.000 VND')
            ->assertSee('-2.000.000đ', false)
            ->assertSee('Giá sau giảm = 20.000.000đ - 2.000.000đ = 18.000.000đ');
    }

    public function test_category_page_still_renders_when_sale_price_column_is_missing(): void
    {
        $product = $this->createProduct('Gaming Laptop Schema Fallback', 22000000, [
            'sale_type' => 'fixed',
            'sale_value' => 7000000,
        ]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sale_price');
        });

        $this->get('/danh-muc/' . $this->category->slug_category_product . '?sort_by=tang_dan')
            ->assertOk()
            ->assertSee($product->product_name);
    }

    protected function createProduct(string $name, float $price, array $overrides = []): Product
    {
        return $this->productService->createProduct(array_merge([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'brand_id' => $this->brand->id,
            'primary_category_id' => $this->category->id,
            'price' => $price,
            'cost_price' => max(1, $price - 2000000),
            'stock_quantity' => 12,
            'description' => '<p>Mô tả ' . $name . '</p>',
            'content' => '<p>Nội dung ' . $name . '</p>',
            'category_ids' => [$this->category->id],
            'tags' => 'category,listing',
            'is_active' => true,
        ], $overrides));
    }
}

