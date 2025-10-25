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

        [$city, $province, $ward] = $this->createLocations();

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
            'shipping_method' => 'cod',
            'city_id' => $city->id,
            'province_id' => $province->id,
            'ward_id' => $ward->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'order_code']);

        $order = \App\Infrastructure\Persistence\Eloquent\Order\Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('cod', $order->payments()->first()->provider);
        $this->assertEquals(15000.0, $order->shipping_total);
        $this->assertEquals($subtotal * 0.1, $order->coupon_discount);

        $this->assertDatabaseCount('coupon_redemptions', 1);
        $this->assertTrue($this->cartService->items()->isEmpty());
        $this->assertNull($this->couponService->current());
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
