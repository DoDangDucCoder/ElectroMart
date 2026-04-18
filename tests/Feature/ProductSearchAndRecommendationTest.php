<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductView;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Infrastructure\Persistence\Eloquent\Order\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductSearchAndRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;
    protected Brand $brand;
    protected ProductCategory $phones;
    protected ProductCategory $accessories;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->productService = $this->app->make(ProductService::class);

        $this->brand = Brand::create([
            'name' => 'Recommendation Brand',
            'slug' => 'recommendation-brand',
            'description' => 'Brand for search and recommendation tests',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        $this->phones = ProductCategory::create([
            'name' => 'Điện thoại',
            'slug' => 'dien-thoai',
            'description' => 'Danh mục điện thoại',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        $this->accessories = ProductCategory::create([
            'name' => 'Phụ kiện',
            'slug' => 'phu-kien',
            'description' => 'Danh mục phụ kiện',
            'is_active' => true,
            'display_order' => 1,
            'meta' => null,
        ]);
    }

    public function test_search_page_supports_combined_name_code_category_and_price_filters(): void
    {
        $matching = $this->createProduct('iPhone 17 Pro', $this->phones, [
            'sku' => 'IP17-256',
            'price' => 1000000,
        ]);

        $this->createProduct('iPhone 17 Case', $this->accessories, [
            'sku' => 'CASE-17',
            'price' => 150000,
        ]);

        $this->createProduct('Galaxy S30 Ultra', $this->phones, [
            'sku' => 'GS30-512',
            'price' => 1200000,
        ]);

        $this->get('/tim-kiem?keywords_submit=iPhone&product_code=IP17&category_id=' . $this->phones->id . '&price_min=900000&price_max=1100000')
            ->assertOk()
            ->assertSee('iPhone 17 Pro')
            ->assertSee('1 sản phẩm đang được hiển thị trong danh sách này.')
            ->assertSee('Tên: iPhone')
            ->assertSee('Mã: IP17')
            ->assertSee('Danh mục: Điện thoại');

        $this->assertDatabaseHas('product_search_queries', [
            'keyword' => 'iPhone',
            'product_code' => 'IP17',
            'category_id' => $this->phones->id,
            'source' => 'web',
        ]);
    }

    public function test_api_search_endpoint_supports_product_code_category_and_price_filters(): void
    {
        $matching = $this->createProduct('iPhone 17 Pro Max', $this->phones, [
            'sku' => 'IP17PM-512',
            'price' => 2000000,
        ]);

        $this->createProduct('Galaxy Watch', $this->accessories, [
            'sku' => 'GW-001',
            'price' => 700000,
        ]);

        $this->getJson('/api/v1/products/search?q=iPhone&sku=IP17PM&category_id=' . $this->phones->id . '&min_price=1500000&max_price=2500000')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.name', $matching->name);
    }

    public function test_product_detail_records_product_view_history_for_logged_in_customer(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct('Viewed Detail Product', $this->phones);

        $this->withSession(['customer_id' => $customer->id])
            ->get('/chi-tiet/' . $product->product_slug)
            ->assertOk();

        $this->assertDatabaseHas('product_views', [
            'product_id' => $product->id,
            'customer_id' => $customer->id,
        ]);

        $this->assertSame(1, $product->fresh()->views_count);
    }

    public function test_recommendation_page_prioritizes_products_related_to_order_history(): void
    {
        $customer = $this->createCustomer('buyer@example.com');
        $purchased = $this->createProduct('iPhone 17 Purchased', $this->phones);
        $candidate = $this->createProduct('iPhone 17 Candidate', $this->phones);
        $other = $this->createProduct('Chuột Gaming', $this->accessories);

        $order = Order::create([
            'code' => 'ORD-' . Str::upper(Str::random(8)),
            'customer_id' => $customer->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'currency' => 'VND',
            'subtotal' => $purchased->final_price,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => $purchased->final_price,
            'placed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $purchased->id,
            'product_name' => $purchased->name,
            'sku' => $purchased->sku,
            'original_price' => $purchased->price,
            'price' => $purchased->final_price,
            'sale_price' => $purchased->sale_price,
            'cost_price' => $purchased->cost_price,
            'sale_type' => $purchased->sale_type,
            'sale_value' => $purchased->sale_value,
            'sale_amount' => $purchased->sale_amount,
            'discount_amount' => $purchased->sale_amount,
            'quantity' => 1,
            'subtotal' => $purchased->final_price,
            'tax_amount' => 0,
        ]);

        ProductView::create([
            'product_id' => $other->id,
            'customer_id' => $customer->id,
            'ip_address' => '203.0.113.15',
            'viewed_at' => now(),
        ]);

        $this->withSession(['customer_id' => $customer->id])
            ->get('/goi-y-san-pham')
            ->assertOk()
            ->assertSee('shopw-recommendation-page', false)
            ->assertSee('shopw-recommendation-page-head', false)
            ->assertSee('shopw-recommendation-section--products', false)
            ->assertSee('iPhone 17 Candidate')
            ->assertSee('Ưu tiên gợi ý từ lịch sử đặt hàng');

        $this->assertDatabaseHas('product_recommendation_impressions', [
            'product_id' => $candidate->id,
            'customer_id' => $customer->id,
            'context' => 'recommendations_page',
        ]);
    }

    public function test_recommendation_page_uses_primary_category_when_category_pivot_rows_are_missing(): void
    {
        $customer = $this->createCustomer('primary-category@example.com');
        $otherBrand = Brand::create([
            'name' => 'Primary Category Brand',
            'slug' => 'primary-category-brand',
            'description' => 'Separate brand for primary category recommendation',
            'is_active' => true,
            'display_order' => 1,
            'meta' => null,
        ]);

        $purchased = $this->createProduct('Primary Purchased', $this->phones);
        $candidate = $this->productService->createProduct([
            'name' => 'Primary Category Candidate',
            'slug' => 'primary-category-candidate-' . Str::random(5),
            'brand_id' => $otherBrand->id,
            'primary_category_id' => $this->phones->id,
            'price' => 1700000,
            'cost_price' => 1100000,
            'stock_quantity' => 10,
            'description' => '<p>Candidate by primary category</p>',
            'content' => '<p>Candidate by primary category</p>',
            'category_ids' => [$this->phones->id],
            'tags' => 'recommendation,primary-category',
            'is_active' => true,
            'meta' => [],
        ]);

        $purchased->categories()->detach();
        $candidate->categories()->detach();

        $order = Order::create([
            'code' => 'ORD-' . Str::upper(Str::random(8)),
            'customer_id' => $customer->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'currency' => 'VND',
            'subtotal' => $purchased->final_price,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => $purchased->final_price,
            'placed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $purchased->id,
            'product_name' => $purchased->name,
            'sku' => $purchased->sku,
            'original_price' => $purchased->price,
            'price' => $purchased->final_price,
            'sale_price' => $purchased->sale_price,
            'cost_price' => $purchased->cost_price,
            'sale_type' => $purchased->sale_type,
            'sale_value' => $purchased->sale_value,
            'sale_amount' => $purchased->sale_amount,
            'discount_amount' => $purchased->sale_amount,
            'quantity' => 1,
            'subtotal' => $purchased->final_price,
            'tax_amount' => 0,
        ]);

        $this->withSession(['customer_id' => $customer->id])
            ->get('/goi-y-san-pham')
            ->assertOk()
            ->assertSee('Primary Category Candidate');
    }

    public function test_recommendation_page_falls_back_to_view_history_for_guest(): void
    {
        $viewed = $this->createProduct('Loa Bluetooth Đã Xem', $this->accessories);

        ProductView::create([
            'product_id' => $viewed->id,
            'customer_id' => null,
            'ip_address' => '198.51.100.10',
            'user_agent' => 'PHPUnit',
            'viewed_at' => now(),
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10'])
            ->get('/goi-y-san-pham')
            ->assertOk()
            ->assertSee('shopw-recommendation-page', false)
            ->assertSee('shopw-recommendation-source-strip', false)
            ->assertSee('Loa Bluetooth Đã Xem')
            ->assertSee('Bạn chưa có lịch sử mua hàng');

        $this->assertDatabaseHas('product_recommendation_impressions', [
            'product_id' => $viewed->id,
            'context' => 'recommendations_page',
        ]);
    }

    public function test_search_page_still_renders_when_search_tracking_table_is_missing(): void
    {
        $matching = $this->createProduct('iPhone 17 No Tracking', $this->phones, [
            'sku' => 'IP17-NT',
        ]);

        Schema::dropIfExists('product_search_queries');

        $this->get('/tim-kiem?keywords_submit=iPhone')
            ->assertOk()
            ->assertSee($matching->name);
    }

    public function test_recommendation_page_still_renders_when_impression_tracking_table_is_missing(): void
    {
        $featured = $this->createProduct('Trending Fallback Product', $this->phones, [
            'is_featured' => true,
        ]);

        Schema::dropIfExists('product_recommendation_impressions');

        $this->get('/goi-y-san-pham')
            ->assertOk()
            ->assertSee($featured->name);
    }

    public function test_partial_product_select_does_not_append_legacy_aliases_without_primary_key(): void
    {
        $product = $this->createProduct('Partial Select Product', $this->phones);

        $partial = Product::query()
            ->select('slug')
            ->whereKey($product->id)
            ->firstOrFail()
            ->toArray();

        $this->assertSame($product->slug, $partial['slug']);
        $this->assertArrayNotHasKey('product_id', $partial);
        $this->assertArrayNotHasKey('product_slug', $partial);
    }

    public function test_product_view_tracking_does_not_double_count_guest_then_customer_view_from_same_ip(): void
    {
        $customer = $this->createCustomer('dedupe@example.com');
        $product = $this->createProduct('Dedupe Viewed Product', $this->phones);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.90'])
            ->get('/chi-tiet/' . $product->product_slug)
            ->assertOk();

        $this->withSession(['customer_id' => $customer->id])
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.90'])
            ->get('/chi-tiet/' . $product->product_slug)
            ->assertOk();

        $this->assertSame(1, $product->fresh()->views_count);
        $this->assertSame(1, ProductView::query()->where('product_id', $product->id)->count());
    }

    protected function createCustomer(string $email = 'customer@example.com'): Customer
    {
        return Customer::create([
            'name' => 'Recommendation Customer',
            'email' => $email,
            'password' => bcrypt('secret123'),
            'phone' => '09' . random_int(10000000, 99999999),
            'is_active' => true,
        ]);
    }

    protected function createProduct(string $name, ProductCategory $category, array $overrides = []): Product
    {
        $sku = $overrides['sku'] ?? strtoupper(Str::slug($name, '-')) . '-' . Str::upper(Str::random(4));

        return $this->productService->createProduct(array_merge([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'brand_id' => $this->brand->id,
            'primary_category_id' => $category->id,
            'price' => 1500000,
            'cost_price' => 1000000,
            'stock_quantity' => 10,
            'description' => '<p>Mô tả ' . $name . '</p>',
            'content' => '<p>Nội dung ' . $name . '</p>',
            'category_ids' => [$category->id],
            'tags' => 'search,recommendation',
            'is_active' => true,
            'meta' => [],
        ], array_diff_key($overrides, ['sku' => true])) + [
            'sku' => $sku,
        ]);
    }
}
