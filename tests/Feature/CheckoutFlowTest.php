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
            ->assertSee('g-checkout-redesign')
            ->assertSee('shopw-checkout-container', false)
            ->assertSee('why-choose-us', false)
            ->assertSee('shipping_email')
            ->assertSee('calculate_delivery')
            ->assertSee('send_order')
            ->assertSee('Thông tin giao hàng')
            ->assertSee('data-checkout-fee-display="method"', false)
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

    public function test_confirm_order_recalculates_shipping_fee_from_submitted_location(): void
    {
        $customer = Customer::create([
            'name' => 'Fresh Fee Customer',
            'email' => 'fresh-fee@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0987654321',
            'is_active' => true,
        ]);

        Session::put('customer_id', $customer->id);

        $this->cartService->addItem($this->productId, 1);

        [$oldCity, $oldProvince, $oldWard] = $this->createLocations();

        $newCity = Location::create([
            'name' => 'City Fresh',
            'code' => 'CITY02',
            'type' => 'city',
            'parent_id' => null,
            'display_order' => 1,
            'is_active' => true,
        ]);
        $newProvince = Location::create([
            'name' => 'District Fresh',
            'code' => 'DIST02',
            'type' => 'province',
            'parent_id' => $newCity->id,
            'display_order' => 1,
            'is_active' => true,
        ]);
        $newWard = Location::create([
            'name' => 'Ward Fresh',
            'code' => 'WARD02',
            'type' => 'ward',
            'parent_id' => $newProvince->id,
            'display_order' => 1,
            'is_active' => true,
        ]);

        ShippingFee::create([
            'city_id' => $oldCity->id,
            'province_id' => $oldProvince->id,
            'ward_id' => $oldWard->id,
            'amount' => 15000,
        ]);
        ShippingFee::create([
            'city_id' => $newCity->id,
            'province_id' => $newProvince->id,
            'ward_id' => $newWard->id,
            'amount' => 45000,
        ]);

        $this->post('/calculate-fee', [
            'city_id' => $oldCity->id,
            'province_id' => $oldProvince->id,
            'ward_id' => $oldWard->id,
        ])->assertOk()
            ->assertJson(['fee' => 15000.0]);

        $response = $this->postJson('/confirm-order', [
            'shipping_email' => 'fresh-fee@example.com',
            'shipping_name' => 'Fresh Fee Customer',
            'shipping_address' => '123 Fresh Street',
            'shipping_phone' => '0987654321',
            'shipping_method' => 'cod',
            'city_id' => $newCity->id,
            'province_id' => $newProvince->id,
            'ward_id' => $newWard->id,
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message', 'order_code']);

        $order = \App\Infrastructure\Persistence\Eloquent\Order\Order::with('shipping')->latest('id')->firstOrFail();

        $this->assertEquals(45000.0, $order->shipping_total);
        $this->assertEquals(45000.0, $order->shipping?->shipping_fee);
    }

    public function test_guest_can_access_checkout_steps(): void
    {
        $this->cartService->addItem($this->productId, 1);

        $this->get('/checkout')
            ->assertOk();

        $this->get('/payment')
            ->assertOk();
    }

    public function test_stale_customer_session_is_treated_as_guest_before_checkout(): void
    {
        $this->cartService->addItem($this->productId, 1);

        Session::put('customer_id', 999999);

        $this->get('/checkout')
            ->assertOk();

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

    public function test_guest_can_confirm_order(): void
    {
        $this->cartService->addItem($this->productId, 1);

        $response = $this->postJson('/confirm-order', [
            'shipping_email' => 'customer@example.com',
            'shipping_name' => 'Guest Checkout',
            'shipping_address' => '123 Test Street',
            'shipping_phone' => '0900000001',
            'shipping_method' => 'cod',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'order_code']);

        $this->assertEquals($response->json('order_code'), Session::get('guest_order_code'));
    }

    public function test_stale_session_checkout_is_treated_as_guest(): void
    {
        $this->cartService->addItem($this->productId, 1);
        Session::put('customer_id', 999999);

        $response = $this->postJson('/confirm-order', [
            'shipping_email' => 'customer@example.com',
            'shipping_name' => 'Stale Session Checkout',
            'shipping_address' => '123 Test Street',
            'shipping_phone' => '0900000001',
            'shipping_method' => 'cod',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'order_code']);

        $this->assertNull(Session::get('customer_id'));
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
                'message' => 'Giỏ hàng đang trống.',
            ]);
    }

    public function test_paypal_routes_fail_gracefully_without_gateway_context(): void
    {
        $this->get('/process-transaction')
            ->assertRedirect('/checkout')
            ->assertSessionHas('error', 'Không tìm thấy tổng tiền thanh toán PayPal.');

        if (! class_exists('Srmklive\\PayPal\\Services\\PayPal')) {
            $this->withSession(['total_payment' => 12.5])
                ->get('/process-transaction')
                ->assertRedirect('/checkout')
                ->assertSessionHas('error', 'Thanh toán PayPal hiện chưa được cấu hình.');
        }

        $this->get('/success-transaction')
            ->assertRedirect('/checkout')
            ->assertSessionHas('error', 'Thiếu mã xác nhận giao dịch PayPal.');

        $this->get('/cancel-transaction')
            ->assertRedirect('/checkout')
            ->assertSessionHas('error', 'Bạn đã hủy giao dịch PayPal.');
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
            'slug' => 'discount-checkout-product-'.Str::random(6),
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

    public function test_customer_can_view_own_bank_transfer_instruction_page(): void
    {
        $customer = Customer::create([
            'name' => 'Bank Transfer Customer',
            'email' => 'bank-transfer@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0966666666',
            'is_active' => true,
        ]);

        Session::put('customer_id', $customer->id);

        $this->cartService->addItem($this->productId, 1);

        $response = $this->postJson('/confirm-order', [
            'shipping_email' => 'bank-transfer@example.com',
            'shipping_name' => 'Bank Transfer Customer',
            'shipping_address' => '123 Bank Street',
            'shipping_phone' => '0966666666',
            'shipping_method' => 'bank_transfer',
        ]);

        $orderCode = $response->json('order_code');

        $this->get("/payment/bank-transfer/{$orderCode}")
            ->assertOk()
            ->assertSee('g-bank-transfer-instructions')
            ->assertSee('Hướng dẫn chuyển khoản')
            ->assertSee($orderCode)
            ->assertSee('Thông tin chuyển khoản')
            ->assertSee('Số tài khoản')
            ->assertSee('Nội dung chuyển khoản')
            ->assertSee('DH '.$orderCode)
            ->assertSee('Các bước tiếp theo')
            ->assertSee('Xem lịch sử đơn hàng')
            ->assertSee('Tiếp tục mua sắm');
    }

    public function test_guest_can_view_bank_transfer_instruction_page_after_checkout(): void
    {
        $this->cartService->addItem($this->productId, 1);

        $response = $this->postJson('/confirm-order', [
            'shipping_email' => 'guest@example.com',
            'shipping_name' => 'Guest Bank Transfer',
            'shipping_address' => '123 Guest Street',
            'shipping_phone' => '0911111111',
            'shipping_method' => 'bank_transfer',
        ]);

        $orderCode = $response->json('order_code');

        $this->get("/payment/bank-transfer/{$orderCode}")
            ->assertOk()
            ->assertSee('Hướng dẫn chuyển khoản');
    }

    public function test_customer_cannot_view_other_customer_bank_transfer_order(): void
    {
        $customer1 = Customer::create([
            'name' => 'Customer One',
            'email' => 'customer1@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0955555555',
            'is_active' => true,
        ]);

        $customer2 = Customer::create([
            'name' => 'Customer Two',
            'email' => 'customer2@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0944444444',
            'is_active' => true,
        ]);

        Session::put('customer_id', $customer1->id);
        $this->cartService->addItem($this->productId, 1);

        $response = $this->postJson('/confirm-order', [
            'shipping_email' => 'customer1@example.com',
            'shipping_name' => 'Customer One',
            'shipping_address' => '123 Test Street',
            'shipping_phone' => '0955555555',
            'shipping_method' => 'bank_transfer',
        ]);

        $orderCode = $response->json('order_code');

        Session::put('customer_id', $customer2->id);

        $this->get("/payment/bank-transfer/{$orderCode}")
            ->assertRedirect('/history')
            ->assertSessionHas('error', 'Bạn không có quyền xem đơn hàng này.');
    }

    public function test_bank_transfer_instruction_page_only_shows_for_bank_transfer_orders(): void
    {
        $customer = Customer::create([
            'name' => 'COD Customer',
            'email' => 'cod@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0933333333',
            'is_active' => true,
        ]);

        Session::put('customer_id', $customer->id);
        $this->cartService->addItem($this->productId, 1);

        $response = $this->postJson('/confirm-order', [
            'shipping_email' => 'cod@example.com',
            'shipping_name' => 'COD Customer',
            'shipping_address' => '123 COD Street',
            'shipping_phone' => '0933333333',
            'shipping_method' => 'cod',
        ]);

        $orderCode = $response->json('order_code');

        $this->get("/payment/bank-transfer/{$orderCode}")
            ->assertRedirect("/view-history-order/{$orderCode}")
            ->assertSessionHas('error', 'Đơn hàng này không sử dụng phương thức chuyển khoản ngân hàng.');
    }

    public function test_bank_transfer_instruction_page_shows_paid_status_correctly(): void
    {
        $customer = Customer::create([
            'name' => 'Paid Customer',
            'email' => 'paid@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0922222222',
            'is_active' => true,
        ]);

        Session::put('customer_id', $customer->id);
        $this->cartService->addItem($this->productId, 1);

        $response = $this->postJson('/confirm-order', [
            'shipping_email' => 'paid@example.com',
            'shipping_name' => 'Paid Customer',
            'shipping_address' => '123 Paid Street',
            'shipping_phone' => '0922222222',
            'shipping_method' => 'bank_transfer',
        ]);

        $orderCode = $response->json('order_code');
        $order = \App\Infrastructure\Persistence\Eloquent\Order\Order::where('code', $orderCode)->firstOrFail();
        $order->update(['payment_status' => 'paid']);

        $this->get("/payment/bank-transfer/{$orderCode}")
            ->assertOk()
            ->assertSee('Đơn hàng đã thanh toán')
            ->assertSee('Đã thanh toán')
            ->assertSee('Đơn hàng của bạn đã được thanh toán thành công');
    }

    public function test_invalid_order_code_redirects_to_history_with_error(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0911111111',
            'is_active' => true,
        ]);

        Session::put('customer_id', $customer->id);

        $this->get('/payment/bank-transfer/INVALID123')
            ->assertRedirect('/history')
            ->assertSessionHas('error', 'Không tìm thấy đơn hàng.');
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
            'slug' => 'checkout-product-'.Str::random(6),
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
