<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'email' => 'customer1@example.com',
                'name' => 'Nguyễn Văn An',
                'phone' => '0123456789',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'loyalty_points_balance' => 1500.0,
                'meta' => json_encode([
                    'preferred_language' => 'vi',
                    'marketing_consent' => true,
                    'birth_date' => '1990-05-15'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'customer2@example.com',
                'name' => 'Trần Thị Bình',
                'phone' => '0987654321',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'loyalty_points_balance' => 2500.0,
                'meta' => json_encode([
                    'preferred_language' => 'vi',
                    'marketing_consent' => true,
                    'birth_date' => '1985-12-20'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'customer3@example.com',
                'name' => 'Lê Văn Cường',
                'phone' => '0369258147',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => null,
                'loyalty_points_balance' => 500.0,
                'meta' => json_encode([
                    'preferred_language' => 'vi',
                    'marketing_consent' => false,
                    'birth_date' => '1992-08-10'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('customers')->insert($customers);

        // Create customer addresses
        $addresses = [
            [
                'customer_id' => 1,
                'type' => 'home',
                'name' => 'Nhà riêng',
                'phone' => '0123456789',
                'address_line_1' => '123 Đường ABC',
                'address_line_2' => 'Phường XYZ',
                'city' => 'Hà Nội',
                'state' => 'Hà Nội',
                'postal_code' => '100000',
                'country' => 'Vietnam',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => 2,
                'type' => 'office',
                'name' => 'Văn phòng',
                'phone' => '0987654321',
                'address_line_1' => '456 Đường DEF',
                'address_line_2' => 'Phường GHI',
                'city' => 'TP. Hồ Chí Minh',
                'state' => 'TP. Hồ Chí Minh',
                'postal_code' => '700000',
                'country' => 'Vietnam',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('customer_addresses')->insert($addresses);
    }
}
