<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductRecommendationImpression;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductSearchQuery;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductDiscoveryReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_discovery_api_swaps_reversed_date_range_and_clamps_minimum_page_size(): void
    {
        $admin = $this->createAdminUser();
        [$product, $category] = $this->createDiscoveryFixtures();

        ProductSearchQuery::create([
            'category_id' => $category->id,
            'keyword' => 'iphone 17',
            'product_code' => 'IP17-256',
            'price_min' => 10000000,
            'price_max' => 30000000,
            'sort' => 'popular',
            'results_count' => 4,
            'source' => 'web',
            'ip_address' => '203.0.113.10',
            'searched_at' => now()->subHours(3),
        ]);

        ProductRecommendationImpression::create([
            'product_id' => $product->id,
            'context' => 'search',
            'source_label' => 'recommended_products',
            'ip_address' => '203.0.113.10',
            'impressed_at' => now()->subHours(2),
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/statistics/product-discovery?' . http_build_query([
            'date_from' => now()->addDay()->toDateString(),
            'date_to' => now()->subDays(3)->toDateString(),
            'per_page' => 0,
        ]))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('summary.total_searches', 1)
            ->assertJsonPath('summary.unique_keywords', 1)
            ->assertJsonPath('summary.recommendation_impressions', 1)
            ->assertJsonPath('summary.recommended_products', 1)
            ->assertJsonPath('pagination.per_page', 1)
            ->assertJsonCount(1, 'recommended_products')
            ->assertJsonPath('recommended_products.0.product_id', $product->id)
            ->assertJsonPath('recommended_products.0.product_name', $product->product_name)
            ->assertJsonPath('recommended_products.0.category_name', $category->name);
    }

    /**
     * @return array{0: Product, 1: ProductCategory}
     */
    protected function createDiscoveryFixtures(): array
    {
        $category = ProductCategory::create([
            'name' => 'Discovery Category',
            'slug' => 'discovery-category',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $brand = Brand::create([
            'name' => 'Discovery Brand',
            'slug' => 'discovery-brand',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $product = $this->app->make(ProductService::class)->createProduct([
            'name' => 'Discovery Product',
            'slug' => 'discovery-product',
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 25000000,
            'cost_price' => 21000000,
            'stock_quantity' => 15,
            'description' => 'Product for discovery report tests',
            'content' => 'Content for discovery report tests',
            'category_ids' => [$category->id],
            'tags' => 'discovery,report',
            'is_active' => true,
            'thumbnail_path' => 'uploads/product/no-image.svg',
        ]);

        return [$product, $category];
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
