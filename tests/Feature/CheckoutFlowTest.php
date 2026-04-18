<?php

namespace Tests\Feature;

use App\Application\Checkout\Services\CartService;
use App\Application\Checkout\Services\CouponService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Shipping\ShippingFee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;
    protected CouponService $couponService;
    protected int $productId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->cartService = $this->app->make(CartService::class);
        $this->couponService = $this->app->make(CouponService::class);

        $this->productId = $this->createProduct();
    }

    public function test_user_can_calculate_shipping_and_confirm_order(): void
    {
        $customer = Customer::create([
            'name' => 'Checkout Tester',
            'email' => 'tester@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0999999999',
            'is_active' => true,
        ]);

        Session::put('customer_id', $customer->id);

        $this->cartService->addItem($this->productId, 2);
        $this->createLocations();

        $this->get('/checkout')
            ->assertOk()
            ->assertSee('g-checkout')
            ->assertSee('shopw-checkout-flow', false)
            ->assertSee('shopw-checkout-assurance-grid', false)
            ->assertSee('shopw-checkout-support-card', false)
            ->assertSee('shipping_email')
            ->assertSee('calculate_delivery')
            ->assertSee('send_order')
            ->assertSee('Tóm tắt đơn hàng')
            ->assertSee('data-checkout-fee-display="hero"', false)
            ->assertSee('data-checkout-grand-total-display="summary"', false)
            ->assertSee('checkout_delivery_notice', false)
            ->assertSee('checkout_base_total', false);

        [$city, $province, $ward] = Location::query()->orderBy('id')->get()->values()->all();

        ShippingFee::create([
            'city_id' => $city->id,
            'province_id' => $province->id,
            'ward_id' => $ward->id,
            'amount' => 15000,
        ]);

        $this->post('/calculate-fee', [
            'city_id' => $city->id,
            'province_id' => $province->id,
            'ward_id' => $ward->id,
        ])->assertStatus(200)
            ->assertJson(['fee' => 15000.0]);

        $coupon = Coupon::create([
            'code' => 'SALE10',
            'name' => 'Sale 10%',
            'type' => 'percent',
            'value' => 10,
            'usage_limit' => null,
            'usage_per_customer' => null,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $subtotal = $this->cartService->subtotal();
        $this->couponService->apply($coupon->code, $subtotal, $customer->id);

        $response = $this->postJson('/confirm-order', [
            'shipping_email' => 'customer@example.com',
            'shipping_name' => 'Test Customer',
            'shipping_address' => '123 Test St',
            'shipping_phone' => '0123456789',
            'shipping_notes' => 'Please deliver quickly',
            'shipping_method' => 'bank_transfer',
            'city_id' => $city->id,
            'province_id' => $province->id,
            'ward_id' => $ward->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'order_code']);

        $order = \App\Infrastructure\Persistence\Eloquent\Order\Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('bank_transfer', $order->payments()->first()->provider);
        $this->assertEquals(15000.0, $order->shipping_total);
        $this->assertEquals($subtotal * 0.1, $order->coupon_discount);

        $this->assertDatabaseCount('coupon_redemptions', 1);
        $this->assertTrue($this->cartService->items()->isEmpty());
        $this->assertNull($this->couponService->current());
    }

    public function test_guest_is_redirected_to_login_before_accessing_checkout_steps(): void
    {
        $this->cartService->addItem($this->productId, 1);

        $this->get('/checkout')
            ->assertRedirect('/dang-nhap?redirect=checkout')
            ->assertSessionHas('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');

        $this->get('/payment')
            ->assertRedirect('/dang-nhap?redirect=payment')
            ->assertSessionHas('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');
    }

    public function test_stale_customer_session_is_redirected_back_to_login_before_checkout(): void
    {
        $this->cartService->addItem($this->productId, 1);

        Session::put('customer_id', 999999);

        $this->get('/checkout')
            ->assertRedirect('/dang-nhap?redirect=checkout')
            ->assertSessionHas('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');

        $this->assertNull(session('customer_id'));
    }

    public function test_checkout_endpoints_validate_invalid_payloads(): void
    {
        $this->postJson('/calculate-fee', [
            'city_id' => 'abc',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['city_id', 'province_id', 'ward_id']);

        $this->postJson('/confirm-order', [
            'shipping_email' => '',
            'shipping_name' => '',
            'shipping_address' => '',
            'shipping_phone' => '',
            'shipping_method' => 'wire_transfer',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors([
                'shipping_email',
                'shipping_name',
                'shipping_address',
                'shipping_phone',
                'shipping_method',
            ]);
    }

    public function test_calculate_fee_requires_full_shipping_location_selection(): void
    {
        $this->postJson('/calculate-fee', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['city_id', 'province_id', 'ward_id']);
    }

    public function test_checkout_requires_valid_customer_session_to_confirm_order(): void
    {
        $this->cartService->addItem($this->productId, 1);

        $this->postJson('/confirm-order', [
            'shipping_email' => 'customer@example.com',
            'shipping_name' => 'Guest Checkout',
            'shipping_address' => '123 Test Street',
            'shipping_phone' => '0900000001',
            'shipping_method' => 'cod',
        ])->assertStatus(401)
            ->assertJson([
                'message' => 'Vui lòng đăng nhập trước khi đặt hàng.',
            ]);

        Session::put('customer_id', 999999);

        $this->postJson('/confirm-order', [
            'shipping_email' => 'customer@example.com',
            'shipping_name' => 'Stale Session Checkout',
            'shipping_address' => '123 Test Street',
            'shipping_phone' => '0900000001',
            'shipping_method' => 'cod',
        ])->assertStatus(401)
            ->assertJson([
                'message' => 'Vui lòng đăng nhập trước khi đặt hàng.',
            ]);
    }

    public function test_checkout_handles_empty_cart_failure_paths(): void
    {
        $customer = Customer::create([
            'name' => 'Empty Cart Customer',
            'email' => 'empty-cart@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0988888888',
            'is_active' => true,
        ]);

        Session::put('customer_id', $customer->id);

        $this->get('/checkout')
            ->assertRedirect('/gio-hang');

        $this->postJson('/confirm-order', [
            'shipping_email' => 'customer@example.com',
            'shipping_name' => 'Empty Cart Checkout',
            'shipping_address' => '123 Test Street',
            'shipping_phone' => '0900000001',
            'shipping_method' => 'cod',
        ])->assertStatus(422)
            ->assertJson([
                'message' => 'Cart is empty.',
            ]);
    }

    public function test_select_delivery_home_returns_expected_options_and_validates_payload(): void
    {
        [$city, $province, $ward] = $this->createLocations();

        $this->post('/select-delivery-home', [
            'action' => 'city',
            'ma_id' => $city->id,
        ])->assertOk()
            ->assertSee('--- Chọn quận / huyện ---')
            ->assertSee('District Test');

        $this->post('/select-delivery-home', [
            'action' => 'province',
            'ma_id' => $province->id,
        ])->assertOk()
            ->assertSee('--- Chọn phường / xã ---')
            ->assertSee('Ward Test');

        $this->postJson('/select-delivery-home', [
            'action' => 'invalid',
            'ma_id' => 'abc',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['action', 'ma_id']);
    }

    public function test_confirm_order_stores_product_pricing_snapshot_on_order_items(): void
    {
        $brand = Brand::create([
            'name' => 'Discount Brand',
            'slug' => 'discount-brand',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $category = ProductCategory::create([
            'name' => 'Discount Category',
            'slug' => 'discount-category',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $discountedProduct = $this->app->make(\App\Application\Catalog\Services\ProductService::class)->createProduct([
            'name' => 'Discount Checkout Product',
            'slug' => 'discount-checkout-product-' . Str::random(6),
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 300000,
            'cost_price' => 180000,
            'sale_type' => 'fixed',
            'sale_value' => 50000,
            'stock_quantity' => 20,
            'description' => 'Discount product for checkout snapshot',
            'content' => 'Discount product content',
            'category_ids' => [$category->id],
            'tags' => 'checkout,snapshot',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'name' => 'Snapshot Checkout Customer',
            'email' => 'snapshot-checkout@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0911111111',
            'is_active' => true,
        ]);

        Session::put('customer_id', $customer->id);

        $this->cartService->clear();
        $this->cartService->addItem($discountedProduct->product_id, 2);

        $this->postJson('/confirm-order', [
            'shipping_email' => 'snapshot-checkout@example.com',
            'shipping_name' => 'Snapshot Checkout Customer',
            'shipping_address' => '123 Snapshot Street',
            'shipping_phone' => '0911111111',
            'shipping_method' => 'cod',
        ])->assertOk();

        $orderItem = \App\Infrastructure\Persistence\Eloquent\Order\OrderItem::query()->latest('id')->firstOrFail();

        $this->assertSame(300000.0, (float) $orderItem->original_price);
        $this->assertSame(250000.0, (float) $orderItem->price);
        $this->assertSame(250000.0, (float) $orderItem->sale_price);
        $this->assertSame(180000.0, (float) $orderItem->cost_price);
        $this->assertSame('fixed', $orderItem->sale_type);
        $this->assertSame(50000.0, (float) $orderItem->sale_value);
        $this->assertSame(50000.0, (float) $orderItem->sale_amount);
        $this->assertSame(100000.0, (float) $orderItem->discount_amount);
        $this->assertSame(500000.0, (float) $orderItem->revenue);
        $this->assertSame(140000.0, (float) $orderItem->profit);
    }

    public function test_confirm_order_ignores_stale_coupon_session_that_no_longer_meets_minimum_total(): void
    {
        $customer = Customer::create([
            'name' => 'Stale Coupon Customer',
            'email' => 'stale-coupon@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0977777777',
            'is_active' => true,
        ]);

        Session::put('customer_id', $customer->id);

        $this->cartService->clear();
        $this->cartService->addItem($this->productId, 1);

        $coupon = Coupon::create([
            'code' => 'MIN200',
            'name' => 'Minimum 200k',
            'type' => 'fixed',
            'value' => 50000,
            'min_order_total' => 300000,
            'usage_limit' => null,
            'usage_per_customer' => null,
            'used_count' => 0,
            'is_active' => true,
        ]);

        Session::put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'discount_amount' => 50000,
            'max_discount' => 0,
        ]);

        $this->postJson('/confirm-order', [
            'shipping_email' => 'stale-coupon@example.com',
            'shipping_name' => 'Stale Coupon Customer',
            'shipping_address' => '123 Session Street',
            'shipping_phone' => '0977777777',
            'shipping_method' => 'cod',
        ])->assertOk();

        $order = \App\Infrastructure\Persistence\Eloquent\Order\Order::query()->latest('id')->firstOrFail();

        $this->assertSame(0.0, (float) $order->coupon_discount);
        $this->assertNull($order->coupon_code);
        $this->assertNull(session('coupon'));
    }

    protected function createProduct(): int
    {
        $brand = Brand::create([
            'name' => 'Checkout Brand',
            'slug' => 'checkout-brand',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $category = ProductCategory::create([
            'name' => 'Checkout Category',
            'slug' => 'checkout-category',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $product = $this->app->make(\App\Application\Catalog\Services\ProductService::class)->createProduct([
            'name' => 'Checkout Product',
            'slug' => 'checkout-product-' . Str::random(6),
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 200000,
            'cost_price' => 150000,
            'stock_quantity' => 20,
            'description' => 'Checkout description',
            'content' => 'Checkout content',
            'category_ids' => [$category->id],
            'tags' => 'checkout',
            'is_active' => true,
        ]);

        return $product->product_id;
    }

    protected function createLocations(): array
    {
        $city = Location::create([
            'name' => 'City Test',
            'code' => 'CITY01',
            'type' => 'city',
            'parent_id' => null,
            'display_order' => 0,
            'is_active' => true,
        ]);

        $province = Location::create([
            'name' => 'District Test',
            'code' => 'DIST01',
            'type' => 'province',
            'parent_id' => $city->id,
            'display_order' => 0,
            'is_active' => true,
        ]);

        $ward = Location::create([
            'name' => 'Ward Test',
            'code' => 'WARD01',
            'type' => 'ward',
            'parent_id' => $province->id,
            'display_order' => 0,
            'is_active' => true,
        ]);

        return [$city, $province, $ward];
    }
}

