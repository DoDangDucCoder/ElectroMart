<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPermissionHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_editor_cannot_access_unrelated_admin_slices_without_explicit_permissions(): void
    {
        $editor = $this->createAdminUser('Editor');

        $this->actingAs($editor, 'web')
            ->get('/users')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->get('/index-spatie')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->get('/manage-order')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->get('/product-profit')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->get('/list-coupon')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->get('/read_data')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->get('/gg-document')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->get('/download_document/documents/manual.pdf/manual.pdf')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->delete('/delete_document/documents/manual.pdf')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->get('/information')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->get('/product-discovery-report')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->get('/chat-inbox')
            ->assertForbidden();

        $this->actingAs($editor, 'web')
            ->getJson('/chat-inbox/conversations')
            ->assertForbidden();
    }

    public function test_editor_can_access_product_management_with_product_permission(): void
    {
        $editor = $this->createAdminUser('Editor');

        $this->actingAs($editor, 'web')
            ->get('/all-product')
            ->assertOk();
    }

    public function test_editor_can_access_content_management_with_content_permission(): void
    {
        $editor = $this->createAdminUser('Editor');

        $this->actingAs($editor, 'web')
            ->get('/all-post')
            ->assertOk();
    }

    public function test_manager_role_keeps_order_permission_but_not_content_or_system_settings(): void
    {
        $manager = $this->createAdminUser('Manager');

        $this->assertTrue($manager->hasPermissionTo(AdminPermissions::MANAGE_ORDERS));

        $this->actingAs($manager, 'web')
            ->get('/all-post')
            ->assertForbidden();

        $this->actingAs($manager, 'web')
            ->get('/information')
            ->assertForbidden();

        $this->actingAs($manager, 'web')
            ->get('/product-discovery-report')
            ->assertForbidden();

        $this->actingAs($manager, 'web')
            ->get('/chat-inbox')
            ->assertForbidden();

        $this->actingAs($manager, 'web')
            ->getJson('/chat-inbox/conversations')
            ->assertForbidden();

        $this->actingAs($manager, 'web')
            ->get('/product-profit')
            ->assertOk();
    }

    public function test_admin_api_permissions_are_enforced_per_slice(): void
    {
        $editor = $this->createAdminUser('Editor');

        Sanctum::actingAs($editor);

        $this->getJson('/api/v1/admin/products')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/v1/admin/orders')
            ->assertForbidden();

        $this->getJson('/api/v1/admin/statistics/products-profit')
            ->assertForbidden();

        $this->getJson('/api/v1/admin/statistics/product-discovery')
            ->assertForbidden();

        $manager = $this->createAdminUser('Manager');

        Sanctum::actingAs($manager);

        $this->getJson('/api/v1/admin/statistics/products-profit')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/v1/admin/statistics/product-discovery')
            ->assertForbidden();
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
