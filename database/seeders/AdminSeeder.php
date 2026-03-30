<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
                'email' => 'admin@shop.com',
                'password' => Hash::make('admin123'),
                'name' => 'Super Admin',
                'phone' => '0123456789',
                'role' => 'Super Admin',
            ],
            [
                'email' => 'editor@shop.com',
                'password' => Hash::make('editor123'),
                'name' => 'Editor',
                'phone' => '0987654321',
                'role' => 'Editor',
            ],
        ];

        foreach ($admins as $admin) {
            $user = User::updateOrCreate([
                'email' => $admin['email'],
            ], [
                'name' => $admin['name'],
                'phone' => $admin['phone'],
                'password' => $admin['password'],
            ]);

            $user->syncRoles([$admin['role']]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
