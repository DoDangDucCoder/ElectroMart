<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorefrontComparePageTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;
    protected ProductCategory $category;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productService = $this->app->make(ProductService::class);

        $this->category = ProductCategory::create([
            'name' => 'Compare Category',
            'slug' => 'compare-category',
            'description' => 'Category for compare tests',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        $this->brand = Brand::create([
            'name' => 'Compare Brand',
            'slug' => 'compare-brand',
            'description' => 'Brand for compare tests',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);
    }

    public function test_compare_page_renders_shared_empty_state_when_no_ids_are_selected(): void
    {
        $this->get('/so-sanh')
            ->assertOk()
            ->assertSee('g-compare')
            ->assertSee('shopw-compare-page', false)
            ->assertSee('shopw-compare-hero', false)
            ->assertSee('shopw-empty-state', false)
            ->assertSee('Chưa có sản phẩm để so sánh')
            ->assertSee('Về trang chủ');
    }

    public function test_compare_page_renders_products_when_ids_are_present(): void
    {
        $firstProduct = $this->createProduct('Gaming Compare One');
        $secondProduct = $this->createProduct('Gaming Compare Two');

        $this->get('/so-sanh?ids=' . $firstProduct->product_id . ',' . $secondProduct->product_id)
            ->assertOk()
            ->assertSee('compare-table')
            ->assertSee('shopw-compare-summary', false)
            ->assertSee('shopw-compare-table-shell', false)
            ->assertSee('shopw-compare-table-product-card', false)
            ->assertSee('Gaming Compare One')
            ->assertSee('Gaming Compare Two')
            ->assertDontSee('Chưa có sản phẩm để so sánh');
    }

    public function test_compare_page_sync_script_does_not_reload_when_current_ids_already_match_local_storage(): void
    {
        $this->get('/so-sanh')
            ->assertOk()
            ->assertSee('new URLSearchParams(window.location.search).get(\'ids\') || \'\'', false)
            ->assertSee('if (ids === currentIds)', false);
    }

    protected function createProduct(string $name): Product
    {
        return $this->productService->createProduct([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'brand_id' => $this->brand->id,
            'primary_category_id' => $this->category->id,
            'price' => 19500000,
            'cost_price' => 15200000,
            'stock_quantity' => 8,
            'description' => '<p>Mô tả ' . $name . '</p>',
            'content' => '<p>Nội dung ' . $name . '</p>',
            'category_ids' => [$this->category->id],
            'tags' => 'compare',
            'is_active' => true,
        ]);
    }
}

