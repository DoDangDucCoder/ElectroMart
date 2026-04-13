<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AdminResetPasswordNotification;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_admin_login_page_renders_register_and_forgot_password_links(): void
    {
        $this->get('/admin')
            ->assertOk()
            ->assertSee(route('admin.register'), false)
            ->assertSee(route('admin.password.request'), false);
    }

    public function test_admin_login_page_renders_database_unavailable_notice_when_runtime_flag_is_enabled(): void
    {
        config()->set('app.runtime.database_unavailable', true);
        config()->set('app.runtime.database_unavailable_message', 'MySQL local is offline.');

        $this->get('/admin')
            ->assertOk()
            ->assertSee('MySQL local is offline.')
            ->assertSee('Đăng nhập tạm thời bị khóa.');
    }

    public function test_admin_login_attempt_is_blocked_cleanly_when_database_is_unavailable(): void
    {
        config()->set('app.runtime.database_unavailable', true);
        config()->set('app.runtime.database_unavailable_message', 'MySQL local is offline.');

        $this->post(route('admin.login.attempt'), [
            'email' => 'admin@example.com',
            'password' => 'secret123',
        ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors(['database']);
    }

    public function test_first_admin_can_self_register_without_invite_code(): void
    {
        $response = $this->post(route('admin.register.store'), [
            'name' => 'Bootstrap Owner',
            'email' => 'owner@example.com',
            'phone' => '0909000000',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.login'));

        $user = User::where('email', 'owner@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('Super Admin'));
        $this->assertTrue(AdminAccess::canAccess($user));
    }

    public function test_subsequent_admin_registration_requires_valid_invite_code(): void
    {
        config()->set('admin-auth.registration.invite_code', 'invite-2026');

        $this->createAdminUser(['email' => 'existing-admin@example.com']);

        $this->post(route('admin.register.store'), [
            'name' => 'Ops Manager',
            'email' => 'ops@example.com',
            'phone' => '0909111222',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertSessionHasErrors(['registration_code']);

        $this->post(route('admin.register.store'), [
            'name' => 'Ops Manager',
            'email' => 'ops@example.com',
            'phone' => '0909111222',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'registration_code' => 'invite-2026',
        ])->assertRedirect(route('admin.login'));

        $user = User::where('email', 'ops@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('Manager'));
        $this->assertTrue(AdminAccess::canAccess($user));
    }

    public function test_legacy_admin_roles_still_disable_bootstrap_registration(): void
    {
        config()->set('admin-auth.registration.invite_code', 'invite-2026');

        $legacyAdmin = $this->createAdminUser([
            'email' => 'legacy-admin@example.com',
        ], 'super_admin');

        $this->assertTrue(AdminAccess::canAccess($legacyAdmin));

        $this->post(route('admin.register.store'), [
            'name' => 'Unexpected Bootstrap',
            'email' => 'ops@example.com',
            'phone' => '0909111222',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertSessionHasErrors(['registration_code']);

        $this->assertDatabaseMissing('users', [
            'email' => 'ops@example.com',
        ]);
    }

    public function test_admin_forgot_password_sends_reset_notification_for_admin_user(): void
    {
        Notification::fake();

        $admin = $this->createAdminUser([
            'email' => 'admin@example.com',
        ]);

        $this->post(route('admin.password.email'), [
            'email' => 'admin@example.com',
        ])->assertSessionHas('status');

        Notification::assertSentTo($admin, AdminResetPasswordNotification::class);
    }

    public function test_admin_can_reset_password_from_valid_token(): void
    {
        $admin = $this->createAdminUser([
            'email' => 'admin@example.com',
            'password' => bcrypt('old-secret'),
        ]);

        $token = Password::broker('users')->createToken($admin);

        $this->post(route('admin.password.update'), [
            'token' => $token,
            'email' => $admin->email,
            'password' => 'new-secret123',
            'password_confirmation' => 'new-secret123',
        ])->assertRedirect(route('admin.login'));

        $this->post(route('admin.login.attempt'), [
            'email' => $admin->email,
            'password' => 'new-secret123',
        ])->assertRedirect(route('admin.dashboard'));
    }

    protected function createAdminUser(array $attributes = [], string $roleName = 'Admin'): User
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

        $grantedPermissions = AdminPermissions::defaultsForRole($roleName);

        if ($grantedPermissions !== []) {
            $role->syncPermissions($grantedPermissions);
        }

        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }
}
