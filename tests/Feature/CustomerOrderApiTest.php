<?php

namespace Tests\Feature;

use App\Application\Checkout\Services\CustomerCartService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Customer\Address;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerOrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_order_api_rejects_shipping_address_owned_by_another_customer(): void
    {
        $customer = $this->createCustomer('buyer@example.com');
        $otherCustomer = $this->createCustomer('other@example.com');
        $foreignAddress = Address::create([
            'customer_id' => $otherCustomer->id,
            'label' => 'Home',
            'recipient_name' => 'Other Customer',
            'recipient_phone' => '0900000002',
            'email' => 'other@example.com',
            'address_line1' => '456 Foreign Street',
            'country' => 'Vietnam',
            'is_default' => false,
        ]);

        $this->seedCartForCustomer($customer);

        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/orders', [
            'shipping_address_id' => $foreignAddress->id,
            'payment_method' => 'cod',
        ])->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Địa chỉ giao hàng không hợp lệ.',
            ]);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_customer_order_api_cannot_cancel_completed_order(): void
    {
        $customer = $this->createCustomer('completed@example.com');
        $order = Order::create([
            'code' => 'ORD-COMPLETED-' . uniqid(),
            'customer_id' => $customer->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'currency' => 'VND',
            'subtotal' => 120000,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => 120000,
            'source' => 'api',
            'placed_at' => now(),
        ]);

        Sanctum::actingAs($customer);

        $this->putJson('/api/v1/orders/' . $order->id . '/cancel')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng ở trạng thái hiện tại.',
            ]);
    }

    public function test_customer_order_api_returns_not_found_when_trying_to_cancel_other_customer_order(): void
    {
        $customer = $this->createCustomer('owner@example.com');
        $otherCustomer = $this->createCustomer('stranger@example.com');
        $order = Order::create([
            'code' => 'ORD-OTHER-' . uniqid(),
            'customer_id' => $otherCustomer->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'fulfillment_status' => 'unfulfilled',
            'currency' => 'VND',
            'subtotal' => 120000,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => 120000,
            'source' => 'api',
            'placed_at' => now(),
        ]);

        Sanctum::actingAs($customer);

        $this->putJson('/api/v1/orders/' . $order->id . '/cancel')
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng hoặc đơn không thể hủy.',
            ]);
    }

    public function test_customer_order_api_returns_consistent_validation_payload_for_unknown_coupon(): void
    {
        $customer = $this->createCustomer('coupon-validation@example.com');
        $address = Address::create([
            'customer_id' => $customer->id,
            'label' => 'Home',
            'recipient_name' => 'Coupon Customer',
            'recipient_phone' => '0900000005',
            'email' => 'coupon-validation@example.com',
            'address_line1' => '789 Coupon Street',
            'country' => 'Vietnam',
            'is_default' => false,
        ]);

        $this->seedCartForCustomer($customer);

        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/orders', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'coupon_code' => 'UNKNOWN-COUPON',
        ])->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors('coupon_code');
    }

    public function test_customer_order_api_rejects_coupon_that_fails_minimum_order_rule(): void
    {
        $customer = $this->createCustomer('coupon-min-order@example.com');
        $address = Address::create([
            'customer_id' => $customer->id,
            'label' => 'Home',
            'recipient_name' => 'Coupon Customer',
            'recipient_phone' => '0900000006',
            'email' => 'coupon-min-order@example.com',
            'address_line1' => '123 Minimum Street',
            'country' => 'Vietnam',
            'is_default' => false,
        ]);

        Coupon::create([
            'code' => 'MIN-500K',
            'name' => 'Minimum Order Coupon',
            'type' => 'fixed',
            'value' => 50000,
            'min_order_total' => 500000,
            'usage_limit' => null,
            'usage_per_customer' => null,
            'used_count' => 0,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $this->seedCartForCustomer($customer);

        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/orders', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'coupon_code' => 'MIN-500K',
        ])->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Tổng đơn hàng chưa đạt điều kiện tối thiểu.',
            ]);
    }

    public function test_customer_order_api_rejects_inactive_coupon_even_when_code_exists(): void
    {
        $customer = $this->createCustomer('coupon-inactive@example.com');
        $address = Address::create([
            'customer_id' => $customer->id,
            'label' => 'Home',
            'recipient_name' => 'Inactive Coupon Customer',
            'recipient_phone' => '0900000007',
            'email' => 'coupon-inactive@example.com',
            'address_line1' => '456 Inactive Street',
            'country' => 'Vietnam',
            'is_default' => false,
        ]);

        Coupon::create([
            'code' => 'INACTIVE-CODE',
            'name' => 'Inactive Coupon',
            'type' => 'fixed',
            'value' => 30000,
            'min_order_total' => 0,
            'usage_limit' => null,
            'usage_per_customer' => null,
            'used_count' => 0,
            'is_active' => false,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $this->seedCartForCustomer($customer);

        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/orders', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'coupon_code' => 'INACTIVE-CODE',
        ])->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Không tìm thấy mã giảm giá hoặc mã chưa được kích hoạt.',
            ]);
    }

    public function test_customer_order_api_rejects_coupon_when_usage_limit_is_exhausted(): void
    {
        $customer = $this->createCustomer('coupon-limit@example.com');
        $address = Address::create([
            'customer_id' => $customer->id,
            'label' => 'Home',
            'recipient_name' => 'Usage Limit Customer',
            'recipient_phone' => '0900000008',
            'email' => 'coupon-limit@example.com',
            'address_line1' => '789 Limit Street',
            'country' => 'Vietnam',
            'is_default' => false,
        ]);

        Coupon::create([
            'code' => 'LIMIT-HIT',
            'name' => 'Usage Limit Coupon',
            'type' => 'fixed',
            'value' => 25000,
            'min_order_total' => 0,
            'usage_limit' => 1,
            'usage_per_customer' => null,
            'used_count' => 1,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $this->seedCartForCustomer($customer);

        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/orders', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'coupon_code' => 'LIMIT-HIT',
        ])->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Mã giảm giá đã đạt giới hạn sử dụng.',
            ]);
    }

    protected function seedCartForCustomer(Customer $customer): void
    {
        $brand = Brand::create([
            'name' => 'API Order Brand',
            'slug' => 'api-order-brand',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $category = ProductCategory::create([
            'name' => 'API Order Category',
            'slug' => 'api-order-category',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $product = app(\App\Application\Catalog\Services\ProductService::class)->createProduct([
            'name' => 'API Order Product',
            'slug' => 'api-order-product',
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 120000,
            'cost_price' => 70000,
            'stock_quantity' => 10,
            'description' => 'API order product',
            'content' => 'API order product content',
            'category_ids' => [$category->id],
            'tags' => 'api,order',
            'is_active' => true,
        ]);

        app(CustomerCartService::class)->addItem($customer->id, $product->product_id, 1);
    }

    protected function createCustomer(string $email): Customer
    {
        return Customer::create([
            'name' => 'Customer',
            'email' => $email,
            'password' => bcrypt('secret123'),
            'phone' => '09' . random_int(10000000, 99999999),
            'is_active' => true,
        ]);
    }
}
