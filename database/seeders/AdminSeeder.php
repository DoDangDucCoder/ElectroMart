<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (AdminPermissions::all() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => AdminAccess::guardName(),
            ]);
        }

        foreach (AdminAccess::canonicalRoleNames() as $roleName) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => AdminAccess::guardName(),
            ]);

            $missingPermissions = array_diff(
                AdminPermissions::defaultsForRole($roleName),
                $role->permissions()
                    ->where('guard_name', AdminAccess::guardName())
                    ->pluck('name')
                    ->all()
            );

            if ($missingPermissions !== []) {
                $role->givePermissionTo($missingPermissions);
            }
        }

        $admins = [
            [
                'email' => (string) config('admin-auth.seed_defaults.primary_email', 'admin@shop.com'),
                'password' => Hash::make((string) config('admin-auth.seed_defaults.primary_password', 'admin123')),
                'name' => 'Super Admin',
                'phone' => '0123456789',
                'role' => 'Super Admin',
            ],
            [
                'email' => (string) config('admin-auth.seed_defaults.editor_email', 'editor@shop.com'),
                'password' => Hash::make((string) config('admin-auth.seed_defaults.editor_password', 'editor123')),
                'name' => 'Editor',
                'phone' => '0987654321',
                'role' => 'Editor',
            ],
        ];

        foreach ($admins as $admin) {
            $user = User::updateOrCreate([
                'email' => $admin['email'],
            ], array_filter([
                'name' => $admin['name'],
                'password' => $admin['password'],
                'phone' => Schema::hasColumn((new User())->getTable(), 'phone') ? $admin['phone'] : null,
            ], static fn ($value): bool => $value !== null));

            $user->syncRoles([$admin['role']]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
