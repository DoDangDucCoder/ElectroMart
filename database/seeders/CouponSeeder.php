<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Chào mừng khách hàng mới',
                'description' => 'Giảm 10% cho đơn hàng đầu tiên',
                'type' => 'percentage',
                'value' => 10.0,
                'minimum_amount' => 1000000,
                'maximum_discount' => 500000,
                'usage_limit' => 1000,
                'usage_limit_per_customer' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SAVE50K',
                'name' => 'Tiết kiệm 50k',
                'description' => 'Giảm 50,000 VND cho đơn hàng từ 2 triệu',
                'type' => 'fixed',
                'value' => 50000,
                'minimum_amount' => 2000000,
                'maximum_discount' => 50000,
                'usage_limit' => 500,
                'usage_limit_per_customer' => 2,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(1),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'VIP20',
                'name' => 'Khách hàng VIP',
                'description' => 'Giảm 20% cho khách hàng VIP',
                'type' => 'percentage',
                'value' => 20.0,
                'minimum_amount' => 5000000,
                'maximum_discount' => 2000000,
                'usage_limit' => 100,
                'usage_limit_per_customer' => 5,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'BLACKFRIDAY',
                'name' => 'Black Friday 2024',
                'description' => 'Giảm 30% cho Black Friday',
                'type' => 'percentage',
                'value' => 30.0,
                'minimum_amount' => 3000000,
                'maximum_discount' => 3000000,
                'usage_limit' => 2000,
                'usage_limit_per_customer' => 3,
                'starts_at' => now()->subDays(1),
                'expires_at' => now()->addDays(3),
                'is_active' => true,
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ],
        ];

        DB::table('coupons')->insert($coupons);
    }
}
