<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Application\Checkout\Services\CartService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Đăng nhập để đặt hàng')
            ->assertDontSee('Tiến hành checkout')
            ->assertSee('Test Product')
            ->assertSee('Áp dụng mã');
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
            ->assertSee('delele-hover-cart');

        $this->get('/show_quick_cart')
            ->assertOk()
            ->assertSee('cart_quantity_input')
            ->assertSee('Giảm giá:')
            ->assertSee('Test Product');

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

