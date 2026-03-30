<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_guest_is_redirected_to_admin_login_for_protected_admin_pages(): void
    {
        $this->get('/dashboard')->assertRedirect('/admin');
        $this->get('/users')->assertRedirect('/admin');
    }

    public function test_admin_login_page_renders_refreshed_auth_shell(): void
    {
        $this->get('/admin')
            ->assertOk()
            ->assertSee('Đăng nhập khu vực quản trị')
            ->assertSee('Đăng nhập quản trị')
            ->assertSee('Ghi nhớ đăng nhập trên thiết bị này')
            ->assertSee('Quay lại trang chủ website');
    }

    public function test_removed_public_admin_routes_are_not_available(): void
    {
        $this->get('/register-auth')->assertNotFound();
        $this->get('/login-google')->assertNotFound();
        $this->get('/login-facebook')->assertNotFound();
        $this->get('/admin-dashboard')->assertNotFound();
        $this->get('/mail-example')->assertNotFound();
        $this->get('/list_document')->assertNotFound();
    }

    public function test_non_admin_user_gets_forbidden_for_admin_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'web')
            ->get('/users')
            ->assertForbidden();
    }

    public function test_admin_user_can_access_admin_user_management(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web')
            ->get('/users')
            ->assertOk();
    }

    public function test_admin_can_log_in_via_canonical_auth_route(): void
    {
        $admin = $this->createAdminUser([
            'email' => 'admin@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post('/auth-login', [
            'email' => $admin->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'web');
    }

    public function test_logout_route_only_accepts_post(): void
    {
        $this->get('/logout-auth')->assertStatus(405);
    }

    protected function createAdminUser(array $attributes = [], string $roleName = 'Admin', ?array $permissions = null): User
    {
        foreach (AdminPermissions::all() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => AdminAccess::guardName(),
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => AdminAccess::guardName(),
        ]);

        $grantedPermissions = $permissions ?? AdminPermissions::defaultsForRole($roleName);

        if ($grantedPermissions !== []) {
            $adminRole->syncPermissions($grantedPermissions);
        }

        $user = User::factory()->create($attributes);
        $user->assignRole($adminRole);

        return $user;
    }
}
