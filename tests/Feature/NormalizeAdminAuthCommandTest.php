<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NormalizeAdminAuthCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_normalizes_roles_and_backfills_legacy_admins(): void
    {
        Role::create([
            'name' => 'Admin',
            'guard_name' => 'admin',
        ]);

        Schema::create('legacy_admins', function ($table) {
            $table->unsignedInteger('admin_id')->primary();
            $table->string('admin_email')->unique();
            $table->string('admin_password');
            $table->string('admin_name');
            $table->string('admin_phone')->nullable();
        });

        Schema::create('legacy_roles', function ($table) {
            $table->unsignedInteger('id_roles')->primary();
            $table->string('name');
        });

        Schema::create('legacy_admin_roles', function ($table) {
            $table->unsignedInteger('id_admin_roles')->primary();
            $table->unsignedInteger('admin_admin_id');
            $table->unsignedInteger('roles_id_roles');
        });

        DB::table('legacy_admins')->insert([
            'admin_id' => 1,
            'admin_email' => 'legacy-admin@example.com',
            'admin_password' => md5('legacy-secret'),
            'admin_name' => 'Legacy Admin',
            'admin_phone' => '0123456789',
        ]);

        DB::table('legacy_roles')->insert([
            'id_roles' => 1,
            'name' => 'super_admin',
        ]);

        DB::table('legacy_admin_roles')->insert([
            'id_admin_roles' => 1,
            'admin_admin_id' => 1,
            'roles_id_roles' => 1,
        ]);

        $this->artisan('admin:normalize-auth')->assertSuccessful();

        $user = User::where('email', 'legacy-admin@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame(md5('legacy-secret'), $user->getRawOriginal('password'));
        $this->assertTrue(AdminAccess::canAccess($user));
        $this->assertTrue($user->hasRole('Super Admin'));
        $this->assertTrue($user->hasPermissionTo(AdminPermissions::MANAGE_USERS));

        $this->post('/auth-login', [
            'email' => 'legacy-admin@example.com',
            'password' => 'legacy-secret',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user->fresh());
        $this->assertTrue(Hash::check('legacy-secret', $user->fresh()->getAuthPassword()));
        $this->assertNotSame(md5('legacy-secret'), $user->fresh()->getRawOriginal('password'));

        $this->assertDatabaseHas('roles', [
            'name' => 'Admin',
            'guard_name' => AdminAccess::guardName(),
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => AdminPermissions::MANAGE_SYSTEM,
            'guard_name' => AdminAccess::guardName(),
        ]);
    }
}
