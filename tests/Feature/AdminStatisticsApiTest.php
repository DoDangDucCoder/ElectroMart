<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
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

    public function test_admin_sales_statistics_fall_back_to_created_at_when_order_date_columns_are_missing(): void
    {
        $admin = $this->createAdminUser();

        Order::create([
            'code' => 'SALES-CREATED-FALLBACK-001',
            'status' => 'completed',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'currency' => 'VND',
            'subtotal' => 450000,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => 450000,
            'source' => 'web',
            'placed_at' => now()->subDays(10),
            'completed_at' => now()->subDays(8),
            'created_at' => now()->subDays(4),
            'updated_at' => now()->subDays(4),
        ]);

        \DB::statement('DROP INDEX IF EXISTS orders_placed_at_created_at_index');
        \DB::statement('DROP INDEX IF EXISTS idx_orders_placed');
        \DB::statement('DROP INDEX IF EXISTS idx_orders_completed');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'placed_at']);
        });

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/statistics/sales')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', true);

        $rows = $response->json('data');

        $this->assertCount(1, $rows);
        $this->assertSame(now()->subDays(4)->toDateString(), $rows[0]['date']);
        $this->assertSame(1, $rows[0]['orders_count']);
        $this->assertSame(450000, $rows[0]['total_revenue']);
    }

    public function test_admin_product_statistics_handle_missing_optional_metric_columns(): void
    {
        $admin = $this->createAdminUser();
        $category = ProductCategory::create([
            'name' => 'Stats Missing Column Category',
            'slug' => 'stats-missing-column-category',
            'is_active' => true,
            'display_order' => 0,
        ]);
        $brand = Brand::create([
            'name' => 'Stats Missing Column Brand',
            'slug' => 'stats-missing-column-brand',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $service = $this->app->make(ProductService::class);

        $service->createProduct([
            'name' => 'Stats Missing Column Product',
            'slug' => 'stats-missing-column-product',
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 900000,
            'cost_price' => 700000,
            'stock_quantity' => 2,
            'stock_alert_threshold' => 5,
            'description' => 'Stats fallback product',
            'content' => 'Stats fallback product',
            'category_ids' => [$category->id],
            'is_active' => true,
            'is_featured' => true,
        ]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'stock_alert_threshold']);
        });

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/statistics/products')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', true)
            ->assertJsonPath('data.total_products', 1)
            ->assertJsonPath('data.active_products', 1)
            ->assertJsonPath('data.featured_products', 0)
            ->assertJsonPath('data.out_of_stock', 0)
            ->assertJsonPath('data.low_stock', 0);
    }

    public function test_admin_database_optimization_tool_endpoints_return_safe_payloads(): void
    {
        $admin = $this->createAdminUser();

        Sanctum::actingAs($admin);

        $optimizeResponse = $this->postJson('/api/v1/admin/statistics/optimize-database')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Database optimization completed');

        $this->assertIsArray($optimizeResponse->json('data'));

        $indexResponse = $this->postJson('/api/v1/admin/statistics/add-indexes')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Database indexes added');

        $this->assertIsArray($indexResponse->json('data'));

        $slowQueryResponse = $this->getJson('/api/v1/admin/statistics/slow-queries')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertIsArray($slowQueryResponse->json('data'));
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
