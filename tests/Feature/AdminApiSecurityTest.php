<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_api_routes(): void
    {
        $this->getJson('/api/v1/admin/products')->assertUnauthorized();
    }

    public function test_non_admin_user_gets_forbidden_on_admin_api_routes(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/admin/products')->assertForbidden();
    }

    public function test_admin_can_log_in_via_admin_api_and_receive_token(): void
    {
        $admin = $this->createAdminUser([
            'email' => 'api-admin@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/v1/admin/auth/login', [
            'email' => $admin->email,
            'password' => 'secret123',
            'device_name' => 'phpunit',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'api-admin@example.com')
            ->assertJsonPath('data.token_type', 'Bearer');

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_admin_can_access_admin_api_routes(): void
    {
        $admin = $this->createAdminUser();

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/products')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/v1/admin/statistics/products-profit')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/v1/admin/statistics/product-discovery')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_admin_statistics_api_falls_back_when_core_tables_are_missing(): void
    {
        $admin = $this->createAdminUser();

        Sanctum::actingAs($admin);

        Schema::partialMock()
            ->shouldReceive('hasTable')
            ->andReturnUsing(static function (string $table): bool {
                return ! in_array($table, [
                    'settings',
                    'orders',
                    'products',
                    'product_categories',
                    'brands',
                    'customers',
                ], true);
            });

        Cache::forget('site_statistics');
        Cache::forget('all_settings');

        $this->getJson('/api/v1/admin/statistics')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_products', 0)
            ->assertJsonPath('data.total_orders', 0);

        $this->getJson('/api/v1/admin/statistics/sales')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', false)
            ->assertJsonCount(0, 'data');

        $this->getJson('/api/v1/admin/statistics/products')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', false)
            ->assertJsonPath('data.total_products', 0);
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
