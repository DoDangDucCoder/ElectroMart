<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminCatalogCacheConsistencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_admin_category_update_invalidates_public_category_caches(): void
    {
        $admin = $this->createAdminUser();
        $category = ProductCategory::create([
            'name' => 'Điện thoại cũ',
            'slug' => 'dien-thoai-cu',
            'description' => 'Danh mục cũ',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'dien-thoai-cu']);

        $this->getJson('/api/v1/categories/tree')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'dien-thoai-cu']);

        $this->getJson('/api/v1/categories/dien-thoai-cu')
            ->assertOk()
            ->assertJsonPath('data.slug', 'dien-thoai-cu');

        Sanctum::actingAs($admin);

        $this->putJson('/api/v1/admin/categories/' . $category->id, [
            'name' => 'Điện thoại mới',
            'slug' => 'dien-thoai-moi',
            'description' => 'Danh mục mới',
            'is_active' => true,
            'display_order' => 3,
            'parent_id' => null,
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.slug', 'dien-thoai-moi');

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'dien-thoai-moi'])
            ->assertJsonMissing(['slug' => 'dien-thoai-cu']);

        $this->getJson('/api/v1/categories/tree')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'dien-thoai-moi'])
            ->assertJsonMissing(['slug' => 'dien-thoai-cu']);

        $this->getJson('/api/v1/categories/dien-thoai-cu')
            ->assertNotFound();

        $this->getJson('/api/v1/categories/dien-thoai-moi')
            ->assertOk()
            ->assertJsonPath('data.slug', 'dien-thoai-moi');
    }

    public function test_admin_category_status_change_invalidates_public_category_cache_and_statistics(): void
    {
        $admin = $this->createAdminUser();
        $category = ProductCategory::create([
            'name' => 'Laptop',
            'slug' => 'laptop',
            'description' => 'Danh mục laptop',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonFragment(['slug' => 'laptop']);

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/statistics')
            ->assertOk()
            ->assertJsonPath('data.total_categories', 1);

        $this->putJson('/api/v1/admin/categories/' . $category->id, [
            'name' => 'Laptop',
            'slug' => 'laptop',
            'description' => 'Danh mục laptop',
            'is_active' => false,
            'display_order' => 0,
            'parent_id' => null,
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.is_active', false);

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonPath('total', 0)
            ->assertJsonMissing(['slug' => 'laptop']);

        $this->getJson('/api/v1/admin/statistics')
            ->assertOk()
            ->assertJsonPath('data.total_categories', 0);
    }

    public function test_admin_brand_status_change_invalidates_public_brand_cache_and_statistics(): void
    {
        $admin = $this->createAdminUser();
        $brand = Brand::create([
            'name' => 'ShopW',
            'slug' => 'shopw',
            'description' => 'Brand test',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $this->getJson('/api/v1/brands')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonFragment(['slug' => 'shopw']);

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/statistics')
            ->assertOk()
            ->assertJsonPath('data.total_brands', 1);

        $this->putJson('/api/v1/admin/brands/' . $brand->id, [
            'name' => 'ShopW',
            'slug' => 'shopw',
            'description' => 'Brand test',
            'is_active' => false,
            'display_order' => 0,
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.is_active', false);

        $this->getJson('/api/v1/brands')
            ->assertOk()
            ->assertJsonPath('total', 0)
            ->assertJsonMissing(['slug' => 'shopw']);

        $this->getJson('/api/v1/admin/statistics')
            ->assertOk()
            ->assertJsonPath('data.total_brands', 0);
    }

    protected function createAdminUser(array $attributes = []): User
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

        $user = User::factory()->create($attributes);
        $user->assignRole($adminRole);

        return $user;
    }
}
