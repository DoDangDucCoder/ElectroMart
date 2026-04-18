<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Application\Checkout\Services\CartService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartFlowTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;
    protected CartService $cartService;
    protected int $productId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->productService = $this->app->make(ProductService::class);
        $this->cartService = $this->app->make(CartService::class);

        $this->productId = $this->createProduct();
    }

    public function test_user_can_add_item_to_cart_via_ajax(): void
    {
        $response = $this->postJson('/add-cart-ajax', [
            'id_product' => $this->productId,
            'id_attribute' => null,
            'cart_product_qty' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Đã thêm sản phẩm vào giỏ hàng.']);

        $items = $this->cartService->items();
        $this->assertCount(1, $items);
        $this->assertSame(2, $items->first()['product_qty']);
    }

    public function test_user_cannot_add_more_items_than_available_stock_via_ajax(): void
    {
        $this->postJson('/add-cart-ajax', [
            'id_product' => $this->productId,
            'id_attribute' => null,
            'cart_product_qty' => 11,
        ])->assertStatus(422)
            ->assertJson([
                'error' => 'Test Product chỉ còn 10 sản phẩm trong kho.',
            ]);

        $this->assertTrue($this->cartService->items()->isEmpty());
    }

    public function test_user_receives_clear_error_when_attribute_table_is_missing(): void
    {
        Schema::dropIfExists('tbl_product_attribute');

        $this->postJson('/add-cart-ajax', [
            'id_product' => $this->productId,
            'id_attribute' => 1,
            'cart_product_qty' => 1,
        ])->assertStatus(422)
            ->assertJson([
                'error' => 'Tùy chọn sản phẩm hiện không khả dụng.',
            ]);

        $this->assertTrue($this->cartService->items()->isEmpty());
    }

    public function test_user_can_render_live_cart_page(): void
    {
        $this->cartService->addItem($this->productId, 2);

        $this->get('/gio-hang')
            ->assertOk()
            ->assertSee('g-cart')
            ->assertSee('shopw-cart-flow', false)
            ->assertSee('shopw-cart-support-card', false)
            ->assertSee('Giỏ hàng của bạn')
            ->assertSee('Tóm tắt đơn hàng')
            ->assertSee('Đăng nhập để thanh toán')
            ->assertSee('redirect=checkout', false)
            ->assertSee('redirect_to=', false)
            ->assertDontSee('Tiến hành checkout')
            ->assertSee('Test Product')
            ->assertSee('Áp dụng mã');
    }

    public function test_logged_in_customer_sees_checkout_cta_on_cart_page(): void
    {
        $customer = Customer::create([
            'name' => 'Cart Customer',
            'email' => 'cart-customer@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0988000000',
            'is_active' => true,
        ]);

        $this->cartService->addItem($this->productId, 1);

        $this->withSession([
            'customer_id' => $customer->id,
        ])->get('/gio-hang')
            ->assertOk()
            ->assertSee('Tiến hành thanh toán')
            ->assertSee('/checkout', false)
            ->assertDontSee('Đăng nhập để thanh toán');
    }

    public function test_user_can_update_cart_quantities(): void
    {
        $this->cartService->addItem($this->productId, 1);
        $item = $this->cartService->items()->first();

        $response = $this->post('/update-cart', [
            'cart_qty' => [
                $item['session_id'] => 3,
            ],
        ]);

        $response->assertRedirect();
        $this->assertSame(3, $this->cartService->items()->first()['product_qty']);
    }

    public function test_quick_cart_update_rejects_quantity_above_available_stock(): void
    {
        $this->cartService->addItem($this->productId, 1);
        $item = $this->cartService->items()->first();

        $this->postJson('/update-quick-cart', [
            'session_id' => $item['session_id'],
            'quantity' => 11,
        ])->assertStatus(422)
            ->assertJson([
                'message' => 'Test Product chỉ còn 10 sản phẩm trong kho.',
            ]);

        $this->assertSame(1, $this->cartService->items()->first()['product_qty']);
    }

    public function test_update_cart_quantity_redirects_back_with_error_when_quantity_exceeds_stock(): void
    {
        $this->cartService->addItem($this->productId, 1);
        $item = $this->cartService->items()->first();

        $this->from('/gio-hang')
            ->post('/update-cart-quantity', [
                'rowId_cart' => $item['session_id'],
                'cart_quantity' => 11,
            ])
            ->assertRedirect('/gio-hang')
            ->assertSessionHas('error', 'Test Product chỉ còn 10 sản phẩm trong kho.');

        $this->assertSame(1, $this->cartService->items()->first()['product_qty']);
    }

    public function test_user_can_apply_coupon_to_cart(): void
    {
        $this->cartService->addItem($this->productId, 1);

        $coupon = Coupon::create([
            'code' => 'SALE10',
            'name' => 'Giảm 10%',
            'type' => 'percent',
            'value' => 10,
            'usage_limit' => null,
            'usage_per_customer' => null,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->from('/gio-hang')->post('/check-coupon', [
            'coupon' => $coupon->code,
        ]);

        $response->assertRedirect('/gio-hang');
        $response->assertSessionHas('message');

        $couponData = session('coupon');
        $this->assertNotEmpty($couponData);
        $this->assertSame($coupon->code, $couponData['code']);
    }

    public function test_cart_page_clears_stale_coupon_and_shipping_sessions_when_cart_is_empty(): void
    {
        Session::put('coupon', [
            'id' => 999,
            'code' => 'STALE',
            'discount_amount' => 10000,
        ]);
        Session::put('fee', 25000);

        $this->get('/gio-hang')->assertOk();

        $this->assertNull(session('coupon'));
        $this->assertNull(session('fee'));
    }

    public function test_user_can_clear_cart(): void
    {
        $this->cartService->addItem($this->productId, 1);

        $response = $this->get('/del-all-product');
        $response->assertRedirect();

        $this->assertTrue($this->cartService->items()->isEmpty());
    }

    public function test_delete_product_route_returns_json_for_ajax_requests(): void
    {
        $this->cartService->addItem($this->productId, 1);
        $item = $this->cartService->items()->first();

        $this->getJson('/del-product/' . $item['session_id'])
            ->assertOk()
            ->assertJson([
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            ]);

        $this->assertTrue($this->cartService->items()->isEmpty());
    }

    public function test_user_can_render_cart_ajax_fragments_from_blade_partials(): void
    {
        $this->cartService->addItem($this->productId, 2);

        $this->get('/show-cart-menu')
            ->assertOk()
            ->assertSee('class="badges"', false)
            ->assertSee('2');

        $this->get('/show-cart')
            ->assertOk()
            ->assertSee('class="badges"', false);

        $this->get('/hover-cart')
            ->assertOk()
            ->assertSee('hover-cart')
            ->assertSee('Test Product')
            ->assertSee('data-hover-cart-remove="true"', false)
            ->assertSee('data-session-id=', false)
            ->assertDontSee('href="#"', false);

        $this->get('/show_quick_cart')
            ->assertOk()
            ->assertSee('cart_quantity_input')
            ->assertSee('cart_qty_update', false)
            ->assertSee('data-session_id=', false)
            ->assertSee('Giảm giá:')
            ->assertSee('Test Product')
            ->assertSee('data-quick-cart-remove="true"', false)
            ->assertDontSee('<form>', false);

        $this->get('/cart-session')
            ->assertOk()
            ->assertSee('class="cart_id"', false)
            ->assertSee((string) $this->productId);
    }

    public function test_cart_requests_validate_payloads_consistently(): void
    {
        $this->postJson('/add-cart-ajax', [
            'id_product' => null,
            'cart_product_qty' => 0,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['id_product', 'cart_product_qty']);

        $this->getJson('/remove-item?id=invalid')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['id']);

        $this->postJson('/update-quick-cart', [
            'session_id' => '',
            'quantity' => 0,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['session_id', 'quantity']);

        $this->from('/gio-hang')
            ->post('/save-cart', [
                'productid_hidden' => '',
                'qty' => 0,
            ])
            ->assertRedirect('/gio-hang')
            ->assertSessionHasErrors(['productid_hidden', 'qty']);

        $this->from('/gio-hang')
            ->post('/check-coupon', [
                'coupon' => '',
            ])
            ->assertRedirect('/gio-hang')
            ->assertSessionHasErrors(['coupon']);
    }

    protected function createProduct(): int
    {
        $brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'Brand description',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        $category = ProductCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Category description',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        $product = $this->productService->createProduct([
            'name' => 'Test Product',
            'slug' => 'test-product-' . Str::random(6),
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 150000,
            'cost_price' => 120000,
            'stock_quantity' => 10,
            'description' => 'Test description',
            'content' => 'Test content',
            'category_ids' => [$category->id],
            'tags' => 'tag1,tag2',
            'is_active' => true,
        ]);

        return $product->product_id;
    }
}

