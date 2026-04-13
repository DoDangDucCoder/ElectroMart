<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminOrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_order_api_index_filters_orders_by_status_payment_fulfillment_customer_and_code(): void
    {
        $admin = $this->createAdminUser();
        $customer = $this->createCustomer('buyer@example.com');
        $otherCustomer = $this->createCustomer('other@example.com');

        $matchingOrder = $this->createOrder([
            'customer_id' => $customer->id,
            'code' => 'ORD-MATCH-001',
            'status' => 'completed',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
        ]);

        $this->createOrder([
            'customer_id' => $otherCustomer->id,
            'code' => 'ORD-PENDING-001',
            'status' => 'pending',
            'payment_status' => 'pending',
            'fulfillment_status' => 'unfulfilled',
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/orders?status=completed&payment_status=paid&fulfillment_status=fulfilled&customer_id=' . $customer->id . '&code=MATCH&per_page=1')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonPath('pagination.per_page', 1)
            ->assertJsonPath('data.0.id', $matchingOrder->id)
            ->assertJsonPath('data.0.code', 'ORD-MATCH-001');
    }

    public function test_admin_order_api_show_returns_not_found_for_missing_order(): void
    {
        Sanctum::actingAs($this->createAdminUser());

        $this->getJson('/api/v1/admin/orders/999999')
            ->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Order not found');
    }

    public function test_admin_order_api_update_status_rejects_invalid_payloads_and_only_updates_provided_fields(): void
    {
        $admin = $this->createAdminUser();
        $order = $this->createOrder([
            'status' => 'pending',
            'payment_status' => 'pending',
            'fulfillment_status' => 'unfulfilled',
        ]);

        Sanctum::actingAs($admin);

        $this->putJson('/api/v1/admin/orders/' . $order->id . '/status', [])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->putJson('/api/v1/admin/orders/' . $order->id . '/status', [
            'status' => 'not-a-real-status',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('status');

        $this->putJson('/api/v1/admin/orders/' . $order->id . '/status', [
            'status' => 'confirmed',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonPath('data.status', 'confirmed');

        $order->refresh();

        $this->assertSame('confirmed', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('unfulfilled', $order->fulfillment_status);
    }

    protected function createOrder(array $attributes = []): Order
    {
        return Order::create(array_merge([
            'code' => 'ORD-' . uniqid(),
            'status' => 'pending',
            'payment_status' => 'pending',
            'fulfillment_status' => 'unfulfilled',
            'currency' => 'VND',
            'subtotal' => 1000000,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => 1000000,
            'source' => 'web',
            'placed_at' => now(),
        ], $attributes));
    }

    protected function createCustomer(string $email): Customer
    {
        return Customer::create([
            'name' => 'Order Customer',
            'email' => $email,
            'password' => bcrypt('secret123'),
            'phone' => '09' . random_int(10000000, 99999999),
            'is_active' => true,
        ]);
    }

    protected function createAdminUser(): User
    {
        foreach (AdminPermissions::all() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => AdminAccess::guardName(),
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => AdminAccess::guardName(),
        ]);
        $adminRole->syncPermissions(AdminPermissions::defaultsForRole('Admin'));

        $user = User::factory()->create();
        $user->assignRole($adminRole);

        return $user;
    }
}
