<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Chào mừng khách hàng mới',
                'type' => 'percent',
                'value' => 10.0,
                'min_order_total' => 1000000,
                'max_discount' => 500000,
                'usage_limit' => 1000,
                'usage_per_customer' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'is_stackable' => false,
                'is_active' => true,
                'meta' => [
                    'description' => 'Giảm 10% cho đơn hàng đầu tiên',
                ],
            ],
            [
                'code' => 'SAVE50K',
                'name' => 'Tiết kiệm 50k',
                'type' => 'fixed',
                'value' => 50000,
                'min_order_total' => 2000000,
                'max_discount' => 50000,
                'usage_limit' => 500,
                'usage_per_customer' => 2,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(1),
                'is_stackable' => false,
                'is_active' => true,
                'meta' => [
                    'description' => 'Giảm 50,000 VND cho đơn hàng từ 2 triệu',
                ],
            ],
            [
                'code' => 'VIP20',
                'name' => 'Khách hàng VIP',
                'type' => 'percent',
                'value' => 20.0,
                'min_order_total' => 5000000,
                'max_discount' => 2000000,
                'usage_limit' => 100,
                'usage_per_customer' => 5,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
                'is_stackable' => false,
                'is_active' => true,
                'meta' => [
                    'description' => 'Giảm 20% cho khách hàng VIP',
                ],
            ],
            [
                'code' => 'BLACKFRIDAY',
                'name' => 'Black Friday 2024',
                'type' => 'percent',
                'value' => 30.0,
                'min_order_total' => 3000000,
                'max_discount' => 3000000,
                'usage_limit' => 2000,
                'usage_per_customer' => 3,
                'starts_at' => now()->subDays(1),
                'expires_at' => now()->addDays(3),
                'is_stackable' => false,
                'is_active' => true,
                'meta' => [
                    'description' => 'Giảm 30% cho Black Friday',
                ],
            ],
        ];

        foreach ($coupons as $coupon) {
            $exists = DB::table('coupons')->where('code', $coupon['code'])->exists();
            $coupon['meta'] = json_encode($coupon['meta'], JSON_UNESCAPED_UNICODE);
            $coupon['applicable_segments'] = null;
            $coupon['updated_at'] = $now;
            $coupon['deleted_at'] = null;

            if (! $exists) {
                $coupon['created_at'] = $now;
            }

            DB::table('coupons')->updateOrInsert(
                ['code' => $coupon['code']],
                $coupon
            );
        }
    }
}
