<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminStatisticsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sales_statistics_only_include_completed_orders_in_last_thirty_days(): void
    {
        $admin = $this->createAdminUser();

        Order::create([
            'code' => 'SALES-COMPLETED-001',
            'status' => 'completed',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'currency' => 'VND',
            'subtotal' => 500000,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => 500000,
            'source' => 'web',
            'placed_at' => now()->subDays(2),
            'completed_at' => now()->subDay(),
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDay(),
        ]);

        Order::create([
            'code' => 'SALES-CANCELLED-001',
            'status' => 'cancelled',
            'payment_status' => 'failed',
            'fulfillment_status' => 'unfulfilled',
            'currency' => 'VND',
            'subtotal' => 900000,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => 900000,
            'source' => 'web',
            'placed_at' => now()->subDay(),
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        Order::create([
            'code' => 'SALES-OLD-COMPLETED-001',
            'status' => 'completed',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'currency' => 'VND',
            'subtotal' => 700000,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => 700000,
            'source' => 'web',
            'placed_at' => now()->subDays(45),
            'completed_at' => now()->subDays(40),
            'created_at' => now()->subDays(45),
            'updated_at' => now()->subDays(40),
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/statistics/sales')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', true);

        $rows = $response->json('data');

        $this->assertCount(1, $rows);
        $this->assertSame(now()->subDay()->toDateString(), $rows[0]['date']);
        $this->assertSame(1, $rows[0]['orders_count']);
        $this->assertSame(500000, $rows[0]['total_revenue']);
        $this->assertSame(500000.0, (float) $rows[0]['avg_order_value']);
    }

    public function test_admin_product_statistics_keep_low_stock_separate_from_out_of_stock(): void
    {
        $admin = $this->createAdminUser();
        $category = ProductCategory::create([
            'name' => 'Stats Category',
            'slug' => 'stats-category',
            'is_active' => true,
            'display_order' => 0,
        ]);
        $brand = Brand::create([
            'name' => 'Stats Brand',
            'slug' => 'stats-brand',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $service = $this->app->make(ProductService::class);

        $service->createProduct([
            'name' => 'Out Of Stock Product',
            'slug' => 'out-of-stock-product',
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 1000000,
            'cost_price' => 800000,
            'stock_quantity' => 0,
            'stock_alert_threshold' => 5,
            'description' => 'Out of stock test product',
            'content' => 'Out of stock test product',
            'category_ids' => [$category->id],
            'is_active' => true,
        ]);

        $service->createProduct([
            'name' => 'Low Stock Product',
            'slug' => 'low-stock-product',
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 1200000,
            'cost_price' => 900000,
            'stock_quantity' => 3,
            'stock_alert_threshold' => 5,
            'description' => 'Low stock test product',
            'content' => 'Low stock test product',
            'category_ids' => [$category->id],
            'is_active' => true,
            'is_featured' => true,
        ]);

        $service->createProduct([
            'name' => 'Healthy Stock Product',
            'slug' => 'healthy-stock-product',
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 1400000,
            'cost_price' => 1000000,
            'stock_quantity' => 8,
            'stock_alert_threshold' => 5,
            'description' => 'Healthy stock test product',
            'content' => 'Healthy stock test product',
            'category_ids' => [$category->id],
            'is_active' => false,
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/statistics/products')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', true)
            ->assertJsonPath('data.total_products', 3)
            ->assertJsonPath('data.active_products', 2)
            ->assertJsonPath('data.featured_products', 1)
            ->assertJsonPath('data.out_of_stock', 1)
            ->assertJsonPath('data.low_stock', 1);
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
