<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

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

        foreach ($customers as $customer) {
            $exists = DB::table('customers')->where('email', $customer['email'])->exists();

            unset($customer['created_at'], $customer['updated_at']);

            if ($exists) {
                unset($customer['password']);
            }

            $customer['updated_at'] = $now;
            $customer['deleted_at'] = null;

            if (! $exists) {
                $customer['created_at'] = $now;
            }

            DB::table('customers')->updateOrInsert(
                ['email' => $customer['email']],
                $customer
            );
        }

        // Create customer addresses
        $addresses = [
            [
                'customer_email' => 'customer1@example.com',
                'label' => 'Nhà riêng',
                'recipient_name' => 'Nguyễn Văn An',
                'recipient_phone' => '0123456789',
                'address_line1' => '123 Đường ABC',
                'address_line2' => 'Phường XYZ',
                'postal_code' => '100000',
                'country' => 'Vietnam',
                'is_default' => true,
                'meta' => [
                    'city_name' => 'Hà Nội',
                    'province_name' => 'Hà Nội',
                    'address_type' => 'home',
                ],
            ],
            [
                'customer_email' => 'customer2@example.com',
                'label' => 'Văn phòng',
                'recipient_name' => 'Trần Thị Bình',
                'recipient_phone' => '0987654321',
                'address_line1' => '456 Đường DEF',
                'address_line2' => 'Phường GHI',
                'postal_code' => '700000',
                'country' => 'Vietnam',
                'is_default' => true,
                'meta' => [
                    'city_name' => 'TP. Hồ Chí Minh',
                    'province_name' => 'TP. Hồ Chí Minh',
                    'address_type' => 'office',
                ],
            ],
        ];

        foreach ($addresses as $address) {
            $customerEmail = $address['customer_email'];
            $customerId = DB::table('customers')->where('email', $customerEmail)->value('id');

            if (! $customerId) {
                continue;
            }

            unset($address['customer_email']);

            $exists = DB::table('customer_addresses')
                ->where('customer_id', $customerId)
                ->where('label', $address['label'])
                ->exists();

            $address['customer_id'] = $customerId;
            $address['meta'] = json_encode($address['meta'], JSON_UNESCAPED_UNICODE);
            $address['updated_at'] = $now;
            $address['deleted_at'] = null;

            if (! $exists) {
                $address['created_at'] = $now;
            }

            DB::table('customer_addresses')->updateOrInsert(
                [
                    'customer_id' => $customerId,
                    'label' => $address['label'],
                ],
                $address
            );
        }
    }
}
