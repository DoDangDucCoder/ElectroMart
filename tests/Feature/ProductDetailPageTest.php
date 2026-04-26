<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductDetailPageTest extends TestCase
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
            'name' => 'Detail Category',
            'slug' => 'detail-category',
            'description' => 'Category for product detail page tests',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        $this->brand = Brand::create([
            'name' => 'Detail Brand',
            'slug' => 'detail-brand',
            'description' => 'Brand for product detail page tests',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);
    }

    public function test_product_detail_page_renders_live_detail_hooks_and_increments_views(): void
    {
        $product = $this->createProduct('Detail Product A');
        $this->createProduct('Detail Product B');

        $this->get('/chi-tiet/' . $product->product_slug)
            ->assertOk()
            ->assertSee('g-product-detail')
            ->assertSee('id="imageGallery"', false)
            ->assertSee('shopw-pdp-gallery-head', false)
            ->assertSee('shopw-pdp-service-grid', false)
            ->assertSee('shopw-pdp-review-thread-shell', false)
            ->assertSee('shopw-pdp-review-form-shell', false)
            ->assertSee('Đội ngũ cửa hàng hỗ trợ chính sách bảo hành và hậu mãi theo từng nhóm hàng.')
            ->assertDontSee('Đội ngũ storefront hỗ trợ chính sách bảo hành và hậu mãi theo từng nhóm hàng.')
            ->assertSee('Hỗ trợ nhanh')
            ->assertDontSee('>Slug<', false)
            ->assertSee('class="product_id"', false)
            ->assertSee('class="comment_product_id"', false)
            ->assertSee('data-default-stock="8"', false)
            ->assertSee('shopw-pdp-purchase-status', false)
            ->assertDontSee('<form action="#">', false)
            ->assertSee('Sản phẩm liên quan');

        $this->assertSame(1, $product->fresh()->views_count);
    }

    public function test_product_detail_page_renders_sale_pricing_breakdown(): void
    {
        $product = $this->createProduct('Detail Sale Product', [
            'price' => 20000000,
            'sale_type' => 'percent',
            'sale_value' => 10,
        ]);

        $this->get('/chi-tiet/' . $product->product_slug)
            ->assertOk()
            ->assertSee('20.000.000đ')
            ->assertSee('18.000.000đ')
            ->assertSee('-10%')
            ->assertSee('Giá sau giảm = 20.000.000đ - 10% = 18.000.000đ');
    }

    public function test_product_detail_page_renders_products_with_array_meta_tags(): void
    {
        $product = $this->createProduct('Array Tag Product', [
            'tags' => ['smartphones', 'apple'],
        ]);

        $this->get('/chi-tiet/' . $product->product_slug)
            ->assertOk()
            ->assertSee('smartphones')
            ->assertSee('apple');
    }

    public function test_quickview_returns_fallback_image_payload(): void
    {
        $product = $this->createProduct('Quickview Product');

        $response = $this->postJson('/quickview', [
            'product_id' => $product->product_id,
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'product_name' => 'Quickview Product',
                'product_id' => $product->product_id,
            ]);

        $this->assertStringContainsString('uploads/product/no-image.svg', (string) $response->json('product_image'));
        $this->assertStringContainsString('Mua ngay', (string) $response->json('product_button'));
    }

    public function test_comment_and_rating_endpoints_persist_product_reviews(): void
    {
        $product = $this->createProduct('Review Product');

        $this->postJson('/send-comment', [
            'product_id' => $product->product_id,
            'comment_name' => 'New Customer',
            'comment_content' => 'Sản phẩm ổn, cần thêm thông tin.',
        ])->assertOk()->assertJson([
            'message' => 'Thành công.',
        ]);

        $ratingResponse = $this->post('/insert-rating', [
            'product_id' => $product->product_id,
            'index' => 4,
        ])->assertOk();

        $this->assertSame('Hoàn tất.', $ratingResponse->json());

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->product_id,
            'author_name' => 'New Customer',
            'content' => 'Sản phẩm ổn, cần thêm thông tin.',
            'rating' => 0,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->product_id,
            'rating' => 4,
            'status' => 'approved',
            'is_public' => 0,
        ]);

        $this->assertSame(2, ProductReview::query()->where('product_id', $product->product_id)->count());
    }

    public function test_product_detail_ajax_endpoints_validate_invalid_payloads(): void
    {
        $product = $this->createProduct('Validation Product');

        $this->postJson('/quickview', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['product_id']);

        $this->postJson('/send-comment', [
            'product_id' => $product->product_id,
            'comment_name' => '',
            'comment_content' => '',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['comment_name', 'comment_content']);

        $this->postJson('/insert-rating', [
            'product_id' => $product->product_id,
            'index' => 9,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['index']);

        $this->postJson('/load-comment', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_load_comment_returns_rendered_thread_partial(): void
    {
        $product = $this->createProduct('Comment Thread Product');

        $parent = ProductReview::create([
            'product_id' => $product->product_id,
            'author_name' => 'Approved Customer',
            'content' => 'Bình luận đã được duyệt.',
            'rating' => 0,
            'status' => 'approved',
            'is_public' => true,
            'approved_at' => now(),
        ]);

        ProductReview::create([
            'product_id' => $product->product_id,
            'parent_id' => $parent->id,
            'author_name' => 'Admin Support',
            'content' => 'Cảm ơn bạn đã để lại đánh giá.',
            'rating' => 0,
            'status' => 'approved',
            'is_public' => true,
            'approved_at' => now(),
        ]);

        $this->post('/load-comment', [
            'product_id' => $product->product_id,
        ])->assertOk()
            ->assertSee('Approved Customer')
            ->assertSee('Bình luận đã được duyệt.')
            ->assertSee('Admin Support')
            ->assertSee('Cảm ơn bạn đã để lại đánh giá.');
    }

    public function test_load_comment_renders_shared_empty_state_when_no_reviews_exist(): void
    {
        $product = $this->createProduct('Empty Review Product');

        $this->post('/load-comment', [
            'product_id' => $product->product_id,
        ])->assertOk()
            ->assertSee('shopw-empty-state', false)
            ->assertSee('Chưa có bình luận nào');
    }

    protected function createProduct(string $name, array $overrides = []): Product
    {
        return $this->productService->createProduct(array_merge([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'brand_id' => $this->brand->id,
            'primary_category_id' => $this->category->id,
            'price' => 15500000,
            'cost_price' => 12000000,
            'stock_quantity' => 8,
            'description' => '<p>Mô tả cho ' . $name . '</p>',
            'content' => '<p>Nội dung chi tiết cho ' . $name . '</p>',
            'category_ids' => [$this->category->id],
            'tags' => 'detail,frontend',
            'is_active' => true,
        ], $overrides));
    }
}

