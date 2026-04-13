<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminWorkspaceToolsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_admin_header_exposes_quick_search_profile_and_settings_links(): void
    {
        $admin = $this->createAdminUser('Admin', [
            'name' => 'Nguyen Van Admin',
        ]);

        $this->actingAs($admin, 'web')
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Bảng quản trị')
            ->assertDontSee('Quản trị cửa hàng')
            ->assertSee('action="' . route('admin.tools.search') . '"', false)
            ->assertSee('name="q"', false)
            ->assertSee('class="form-control g-admin-top-search-input"', false)
            ->assertDontSee('class="form-control search"', false)
            ->assertSee('placeholder="Tìm màn hình quản trị"', false)
            ->assertSee('backend/images/admin-avatar-default.svg', false)
            ->assertSee('g-admin-user-avatar', false)
            ->assertSee(route('admin.profile.show'), false)
            ->assertSee(route('admin.settings'), false)
            ->assertSee('Nguyen Van Admin');
    }

    public function test_admin_quick_search_filters_results_by_permission(): void
    {
        $manager = $this->createAdminUser('Manager');

        $this->actingAs($manager, 'web')
            ->get('/admin/quick-search?q=đơn hàng')
            ->assertOk()
            ->assertSee('Quản lý đơn hàng')
            ->assertSee('/manage-order', false)
            ->assertDontSee('/users', false);

        $this->actingAs($manager, 'web')
            ->get('/admin/quick-search?q=người dùng')
            ->assertOk()
            ->assertSee('Không tìm thấy màn hình quản trị phù hợp')
            ->assertDontSee('/users', false);

        $this->actingAs($manager, 'web')
            ->get('/admin/quick-search?q=%21%21%21')
            ->assertOk()
            ->assertSee('Không tìm thấy màn hình quản trị phù hợp')
            ->assertSee('Kết quả khớp')
            ->assertDontSee('Gợi ý thêm');
    }

    public function test_admin_can_update_own_profile_and_password(): void
    {
        $admin = $this->createAdminUser('Admin', [
            'name' => 'Admin Cũ',
            'email' => 'old-admin@example.com',
            'password' => bcrypt('old-password-123'),
        ]);

        $payload = [
            'name' => 'Admin Mới',
            'email' => 'new-admin@example.com',
            'current_password' => 'old-password-123',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ];

        if (Schema::hasColumn((new User())->getTable(), 'phone')) {
            $payload['phone'] = '0909000009';
        }

        $this->actingAs($admin, 'web')
            ->put('/admin/profile', $payload)
            ->assertRedirect(route('admin.profile.show'))
            ->assertSessionHas('message', 'Cập nhật hồ sơ và mật khẩu quản trị thành công.');

        $admin->refresh();

        $this->assertSame('Admin Mới', $admin->name);
        $this->assertSame('new-admin@example.com', $admin->email);
        $this->assertTrue(Hash::check('new-password-123', $admin->password));

        if (array_key_exists('phone', $payload)) {
            $this->assertSame('0909000009', $admin->phone);
        }
    }

    public function test_admin_settings_page_lists_only_accessible_shortcuts(): void
    {
        $editor = $this->createAdminUser('Editor');

        $this->actingAs($editor, 'web')
            ->get('/admin/settings')
            ->assertOk()
            ->assertSee('Trung tâm điều hướng và cấu hình')
            ->assertSee('/all-product', false)
            ->assertSee('/all-post', false)
            ->assertDontSee('/information', false)
            ->assertDontSee('/manage-order', false)
            ->assertDontSee('/users', false);
    }

    protected function createAdminUser(string $roleName, array $attributes = []): User
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

        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }
}
