<?php

namespace Tests\Unit;

use App\Application\Shared\Services\AuthorizationSecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthorizationSecurityServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_role_creates_permissions_with_current_guard(): void
    {
        Config::set('permission.defaults.guard', 'web');

        $service = app(AuthorizationSecurityService::class);

        $this->assertTrue($service->createRole('catalog_editor', ['edit_products']));

        $this->assertDatabaseHas('permissions', [
            'name' => 'edit_products',
            'guard_name' => 'web',
        ]);
    }

    public function test_grant_permission_to_role_uses_current_guard_for_new_permissions(): void
    {
        Config::set('permission.defaults.guard', 'web');

        $service = app(AuthorizationSecurityService::class);
        $service->createRole('support_agent');

        $this->assertTrue($service->grantPermissionToRole('support_agent', 'view_orders'));

        $permission = Permission::where('name', 'view_orders')->firstOrFail();

        $this->assertSame('web', $permission->guard_name);
    }

    public function test_get_role_permissions_ignores_same_named_role_from_other_guard(): void
    {
        Config::set('permission.defaults.guard', 'web');

        $webRole = Role::create(['name' => 'auditor', 'guard_name' => 'web']);
        $apiRole = Role::create(['name' => 'auditor', 'guard_name' => 'api']);
        $webPermission = Permission::create(['name' => 'view_logs', 'guard_name' => 'web']);
        $apiPermission = Permission::create(['name' => 'manage_tokens', 'guard_name' => 'api']);
        $webRole->givePermissionTo($webPermission);
        $apiRole->givePermissionTo($apiPermission);

        $service = app(AuthorizationSecurityService::class);

        $this->assertSame(['view_logs'], $service->getRolePermissions('auditor'));
    }

    public function test_revoke_permission_from_role_uses_current_guard(): void
    {
        Config::set('permission.defaults.guard', 'web');

        $webRole = Role::create(['name' => 'support', 'guard_name' => 'web']);
        $webPermission = Permission::create(['name' => 'view_orders', 'guard_name' => 'web']);
        $apiRole = Role::create(['name' => 'support', 'guard_name' => 'api']);
        $apiPermission = Permission::create(['name' => 'view_orders', 'guard_name' => 'api']);
        $webRole->givePermissionTo($webPermission);
        $apiRole->givePermissionTo($apiPermission);

        $service = app(AuthorizationSecurityService::class);

        $this->assertTrue($service->revokePermissionFromRole('support', 'view_orders'));
        $this->assertFalse($webRole->fresh()->hasPermissionTo('view_orders'));
        $this->assertTrue($apiRole->fresh()->hasPermissionTo('view_orders', 'api'));
    }

    public function test_revoke_permission_from_role_does_not_flush_unrelated_cache_keys(): void
    {
        Config::set('permission.defaults.guard', 'web');

        $role = Role::create(['name' => 'auditor', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'view_logs', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $service = app(AuthorizationSecurityService::class);
        Cache::put('probe', 'still-here', 60);

        $this->assertTrue($service->checkPermission('auditor', 'view_logs'));
        $this->assertTrue($service->revokePermissionFromRole('auditor', 'view_logs'));
        $this->assertSame('still-here', Cache::get('probe'));
    }
}
