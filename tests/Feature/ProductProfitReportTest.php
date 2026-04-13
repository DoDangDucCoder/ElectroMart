<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
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

class ProductProfitReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_product_profit_report_page(): void
    {
        $admin = $this->createAdminUser('Manager');
        [$product] = $this->createCompletedOrderFixtures();

        $this->actingAs($admin, 'web')
            ->get('/product-profit')
            ->assertOk()
            ->assertSee('Lợi nhuận sản phẩm')
            ->assertSee($product->product_name)
            ->assertSee('500.000đ')
            ->assertSee('140.000đ');
    }

    public function test_admin_products_profit_api_returns_aggregated_report(): void
    {
        $admin = $this->createAdminUser('Manager');
        [$product] = $this->createCompletedOrderFixtures();

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/statistics/products-profit')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.product_name', $product->product_name)
            ->assertJsonPath('data.0.sold_quantity', 2)
            ->assertJsonPath('data.0.revenue', 500000)
            ->assertJsonPath('data.0.cost_total', 360000)
            ->assertJsonPath('data.0.profit', 140000)
            ->assertJsonPath('summary.revenue', 500000)
            ->assertJsonPath('summary.profit', 140000);
    }

    public function test_product_profit_report_groups_same_product_when_order_item_snapshot_name_changes(): void
    {
        $admin = $this->createAdminUser('Manager');
        [$product] = $this->createCompletedOrderFixtures();

        $order = Order::create([
            'code' => 'PROFIT-ORDER-RENAMED-001',
            'status' => 'completed',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'currency' => 'VND',
            'subtotal' => 250000,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => 250000,
            'source' => 'web',
            'placed_at' => now()->subHours(12),
            'completed_at' => now(),
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Profit Product Legacy Snapshot',
            'original_price' => 300000,
            'price' => 250000,
            'sale_price' => 250000,
            'cost_price' => 180000,
            'sale_type' => 'fixed',
            'sale_value' => 50000,
            'sale_amount' => 50000,
            'discount_amount' => 50000,
            'quantity' => 1,
            'subtotal' => 250000,
            'tax_amount' => 0,
        ]);

        $product->update([
            'name' => 'Profit Product Current Name',
            'slug' => 'profit-product-current-name',
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/statistics/products-profit')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.product_id', $product->id)
            ->assertJsonPath('data.0.product_name', 'Profit Product Current Name')
            ->assertJsonPath('data.0.product_slug', 'profit-product-current-name')
            ->assertJsonPath('data.0.sold_quantity', 3)
            ->assertJsonPath('data.0.revenue', 750000)
            ->assertJsonPath('data.0.cost_total', 540000)
            ->assertJsonPath('data.0.profit', 210000)
            ->assertJsonPath('summary.products_count', 1)
            ->assertJsonPath('summary.sold_quantity', 3)
            ->assertJsonPath('summary.revenue', 750000)
            ->assertJsonPath('summary.profit', 210000);
    }

    public function test_products_profit_api_swaps_reversed_date_range_and_clamps_minimum_page_size(): void
    {
        $admin = $this->createAdminUser('Manager');
        [$product] = $this->createCompletedOrderFixtures();

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/statistics/products-profit?' . http_build_query([
            'date_from' => now()->addDay()->toDateString(),
            'date_to' => now()->subDays(3)->toDateString(),
            'per_page' => 0,
        ]))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('pagination.per_page', 1)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.product_id', $product->id);
    }

    public function test_product_profit_report_falls_back_cleanly_when_order_tables_are_missing(): void
    {
        $admin = $this->createAdminUser('Manager');

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');

        $this->actingAs($admin, 'web')
            ->get('/product-profit')
            ->assertOk()
            ->assertSee('Lợi nhuận sản phẩm')
            ->assertSee('Bảng dữ liệu đơn hàng của báo cáo lợi nhuận chưa sẵn sàng');

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/statistics/products-profit')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', false)
            ->assertJsonPath('summary.revenue', 0)
            ->assertJsonPath('summary.profit', 0)
            ->assertJsonCount(0, 'data');
    }

    public function test_product_profit_report_falls_back_to_product_cost_when_order_item_snapshot_cost_is_missing(): void
    {
        $admin = $this->createAdminUser('Manager');
        [$product] = $this->createCompletedOrderFixtures();

        if (Schema::hasColumn('order_items', 'cost_price')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('cost_price');
            });
        }

        $this->actingAs($admin, 'web')
            ->get('/product-profit')
            ->assertOk()
            ->assertSee('Lợi nhuận sản phẩm')
            ->assertSee($product->product_name)
            ->assertSee('500.000đ')
            ->assertSee('140.000đ');

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/statistics/products-profit')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.product_name', $product->product_name)
            ->assertJsonPath('data.0.cost_total', 360000)
            ->assertJsonPath('data.0.profit', 140000)
            ->assertJsonPath('summary.cost_total', 360000)
            ->assertJsonPath('summary.profit', 140000);
    }

    /**
     * @return array{0: Product, 1: ProductCategory, 2: Brand}
     */
    protected function createCompletedOrderFixtures(): array
    {
        $category = ProductCategory::create([
            'name' => 'Profit Category',
            'slug' => 'profit-category',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $brand = Brand::create([
            'name' => 'Profit Brand',
            'slug' => 'profit-brand',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $product = $this->app->make(ProductService::class)->createProduct([
            'name' => 'Profit Product',
            'slug' => 'profit-product',
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 300000,
            'cost_price' => 180000,
            'sale_type' => 'fixed',
            'sale_value' => 50000,
            'stock_quantity' => 10,
            'description' => 'Product for profit report',
            'content' => 'Content for profit report',
            'category_ids' => [$category->id],
            'tags' => 'profit',
            'is_active' => true,
        ]);

        $order = Order::create([
            'code' => 'PROFIT-ORDER-001',
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
            'placed_at' => now()->subDay(),
            'completed_at' => now(),
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'original_price' => 300000,
            'price' => 250000,
            'sale_price' => 250000,
            'cost_price' => 180000,
            'sale_type' => 'fixed',
            'sale_value' => 50000,
            'sale_amount' => 50000,
            'discount_amount' => 100000,
            'quantity' => 2,
            'subtotal' => 500000,
            'tax_amount' => 0,
        ]);

        return [$product, $category, $brand];
    }

    protected function createAdminUser(string $roleName): User
    {
        foreach (AdminPermissions::all() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => AdminAccess::guardName(),
            ]);
        }

        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => AdminAccess::guardName(),
        ]);
        $role->syncPermissions(AdminPermissions::defaultsForRole($roleName));

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
