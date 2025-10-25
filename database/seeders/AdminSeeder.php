<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin roles
        $roles = [
            ['name' => 'Super Admin', 'guard_name' => 'admin'],
            ['name' => 'Admin', 'guard_name' => 'admin'],
            ['name' => 'Editor', 'guard_name' => 'admin'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create admin users
        $admins = [
            [
                'admin_email' => 'admin@shop.com',
                'admin_password' => Hash::make('admin123'),
                'admin_name' => 'Super Admin',
                'admin_phone' => '0123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admin_email' => 'editor@shop.com',
                'admin_password' => Hash::make('editor123'),
                'admin_name' => 'Editor',
                'admin_phone' => '0987654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($admins as $admin) {
            $adminId = DB::table('legacy_admins')->insertGetId($admin);
            
            // Assign roles
            $roleId = $admin['admin_email'] === 'admin@shop.com' ? 1 : 3; // Super Admin or Editor
            DB::table('legacy_admin_roles')->insert([
                'admin_admin_id' => $adminId,
                'roles_id_roles' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
