<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductApiCacheConsistencyTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;
    protected ProductCategory $phones;
    protected Brand $apple;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        $this->productService = $this->app->make(ProductService::class);

        $this->phones = ProductCategory::create([
            'name' => 'Điện thoại',
            'slug' => 'dien-thoai',
            'description' => 'Danh mục điện thoại',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $this->apple = Brand::create([
            'name' => 'Apple',
            'slug' => 'apple',
            'description' => 'Apple brand',
            'is_active' => true,
            'display_order' => 0,
        ]);
    }

    public function test_public_product_endpoints_apply_category_brand_and_featured_filters(): void
    {
        $featuredPhone = $this->createProduct('iPhone 17 Pro', $this->phones, $this->apple, [
            'is_featured' => true,
        ]);

        $laptopCategory = ProductCategory::create([
            'name' => 'Laptop',
            'slug' => 'laptop',
            'description' => 'Danh mục laptop',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $samsung = Brand::create([
            'name' => 'Samsung',
            'slug' => 'samsung',
            'description' => 'Samsung brand',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $nonFeaturedLaptop = $this->createProduct('Galaxy Book', $laptopCategory, $samsung);

        $this->getJson('/api/v1/products?category=dien-thoai')
            ->assertOk()
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonPath('data.0.slug', $featuredPhone->slug);

        $this->getJson('/api/v1/products?brand=apple')
            ->assertOk()
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonPath('data.0.slug', $featuredPhone->slug);

        $this->getJson('/api/v1/products/featured')
            ->assertOk()
            ->assertJsonPath('data.0.slug', $featuredPhone->slug)
            ->assertJsonMissing(['slug' => $nonFeaturedLaptop->slug]);
    }

    public function test_product_service_can_filter_by_category_ids_without_ambiguous_column_errors(): void
    {
        $featuredPhone = $this->createProduct('iPhone 17 Filtered', $this->phones, $this->apple, [
            'is_featured' => true,
        ]);

        $laptopCategory = ProductCategory::create([
            'name' => 'Laptop Filter',
            'slug' => 'laptop-filter',
            'description' => 'Danh mục laptop filter',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $this->createProduct('Galaxy Book Filter', $laptopCategory, $this->apple);

        $products = $this->productService->getProducts([
            'category_ids' => [$this->phones->id],
        ], 12);

        $this->assertSame(1, $products->total());
        $this->assertSame($featuredPhone->slug, $products->items()[0]->slug);
    }

    public function test_disabling_product_invalidates_public_product_caches(): void
    {
        $product = $this->createProduct('iPhone 17 Air', $this->phones, $this->apple, [
            'is_featured' => true,
        ]);

        $this->getJson('/api/v1/products')->assertOk()->assertJsonPath('pagination.total', 1);
        $this->getJson('/api/v1/products?category=dien-thoai')->assertOk()->assertJsonPath('pagination.total', 1);
        $this->getJson('/api/v1/products?brand=apple')->assertOk()->assertJsonPath('pagination.total', 1);
        $this->getJson('/api/v1/products/category/dien-thoai')->assertOk()->assertJsonPath('pagination.total', 1);
        $this->getJson('/api/v1/products/brand/apple')->assertOk()->assertJsonPath('pagination.total', 1);
        $this->getJson('/api/v1/products/latest')->assertOk()->assertJsonFragment(['slug' => $product->slug]);
        $this->getJson('/api/v1/products/featured')->assertOk()->assertJsonFragment(['slug' => $product->slug]);
        $this->getJson('/api/v1/products/' . $product->slug)->assertOk()->assertJsonPath('data.slug', $product->slug);

        Sanctum::actingAs($this->createAdminUser());

        $this->postJson('/api/v1/admin/products/' . $product->id . '/toggle-status')
            ->assertOk()
            ->assertJsonPath('data.is_active', false);

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonPath('pagination.total', 0)
            ->assertJsonMissing(['slug' => $product->slug]);

        $this->getJson('/api/v1/products/category/dien-thoai')
            ->assertOk()
            ->assertJsonPath('pagination.total', 0)
            ->assertJsonMissing(['slug' => $product->slug]);

        $this->getJson('/api/v1/products/brand/apple')
            ->assertOk()
            ->assertJsonPath('pagination.total', 0)
            ->assertJsonMissing(['slug' => $product->slug]);

        $this->getJson('/api/v1/products/latest')
            ->assertOk()
            ->assertJsonMissing(['slug' => $product->slug]);

        $this->getJson('/api/v1/products/featured')
            ->assertOk()
            ->assertJsonMissing(['slug' => $product->slug]);

        $this->getJson('/api/v1/products/' . $product->slug)
            ->assertNotFound();
    }

    public function test_disabling_category_and_brand_invalidates_related_public_product_routes(): void
    {
        $product = $this->createProduct('iPhone 17 Plus', $this->phones, $this->apple);

        $this->getJson('/api/v1/products?category=dien-thoai')
            ->assertOk()
            ->assertJsonPath('pagination.total', 1);
        $this->getJson('/api/v1/products/brand/apple?category=dien-thoai')
            ->assertOk()
            ->assertJsonPath('pagination.total', 1);
        $this->getJson('/api/v1/products/category/dien-thoai')
            ->assertOk()
            ->assertJsonPath('pagination.total', 1);

        Sanctum::actingAs($this->createAdminUser());

        $this->putJson('/api/v1/admin/categories/' . $this->phones->id, [
            'name' => $this->phones->name,
            'slug' => $this->phones->slug,
            'description' => $this->phones->description,
            'is_active' => false,
            'display_order' => $this->phones->display_order,
            'parent_id' => null,
        ])->assertOk()
            ->assertJsonPath('data.is_active', false);

        $this->getJson('/api/v1/products?category=dien-thoai')
            ->assertOk()
            ->assertJsonPath('pagination.total', 0)
            ->assertJsonMissing(['slug' => $product->slug]);

        $this->getJson('/api/v1/products/brand/apple?category=dien-thoai')
            ->assertOk()
            ->assertJsonPath('pagination.total', 0)
            ->assertJsonMissing(['slug' => $product->slug]);

        $this->getJson('/api/v1/products/category/dien-thoai')
            ->assertNotFound();

        $this->putJson('/api/v1/admin/categories/' . $this->phones->id, [
            'name' => $this->phones->name,
            'slug' => $this->phones->slug,
            'description' => $this->phones->description,
            'is_active' => true,
            'display_order' => $this->phones->display_order,
            'parent_id' => null,
        ])->assertOk();

        $this->getJson('/api/v1/products?brand=apple')
            ->assertOk()
            ->assertJsonPath('pagination.total', 1);
        $this->getJson('/api/v1/products/category/dien-thoai?brand=apple')
            ->assertOk()
            ->assertJsonPath('pagination.total', 1);
        $this->getJson('/api/v1/products/brand/apple')
            ->assertOk()
            ->assertJsonPath('pagination.total', 1);

        $this->putJson('/api/v1/admin/brands/' . $this->apple->id, [
            'name' => $this->apple->name,
            'slug' => $this->apple->slug,
            'description' => $this->apple->description,
            'is_active' => false,
            'display_order' => $this->apple->display_order,
        ])->assertOk()
            ->assertJsonPath('data.is_active', false);

        $this->getJson('/api/v1/products?brand=apple')
            ->assertOk()
            ->assertJsonPath('pagination.total', 0)
            ->assertJsonMissing(['slug' => $product->slug]);

        $this->getJson('/api/v1/products/category/dien-thoai?brand=apple')
            ->assertOk()
            ->assertJsonPath('pagination.total', 0)
            ->assertJsonMissing(['slug' => $product->slug]);

        $this->getJson('/api/v1/products/brand/apple')
            ->assertNotFound();
    }

    public function test_public_product_category_routes_and_filters_support_primary_category_without_pivot_rows(): void
    {
        $camera = ProductCategory::create([
            'name' => 'Camera',
            'slug' => 'camera',
            'description' => 'Danh muc camera',
            'is_active' => true,
            'display_order' => 2,
        ]);

        $brand = Brand::create([
            'name' => 'LensCo',
            'slug' => 'lensco',
            'description' => 'LensCo brand',
            'is_active' => true,
            'display_order' => 2,
        ]);

        $product = $this->productService->createProduct([
            'name' => 'Mirrorless Body',
            'slug' => 'mirrorless-body',
            'brand_id' => $brand->id,
            'primary_category_id' => $camera->id,
            'price' => 2200000,
            'cost_price' => 1800000,
            'stock_quantity' => 8,
            'description' => 'Mirrorless body',
            'content' => 'Mirrorless body content',
            'category_ids' => [$camera->id],
            'is_active' => true,
        ]);

        $product->categories()->detach();

        $this->getJson('/api/v1/products/category/camera')
            ->assertOk()
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonFragment(['slug' => 'mirrorless-body']);

        $this->getJson('/api/v1/products?category=camera')
            ->assertOk()
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonFragment(['slug' => 'mirrorless-body']);
    }

    protected function createProduct(string $name, ProductCategory $category, Brand $brand, array $overrides = [])
    {
        return $this->productService->createProduct(array_merge([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::lower(Str::random(4)),
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 1500000,
            'cost_price' => 1200000,
            'stock_quantity' => 10,
            'description' => 'Mô tả ' . $name,
            'content' => 'Nội dung ' . $name,
            'category_ids' => [$category->id],
            'is_active' => true,
            'is_featured' => false,
        ], $overrides));
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
