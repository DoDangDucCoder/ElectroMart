<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Laravel Shop',
                'type' => 'string',
                'description' => 'Tên website',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_description',
                'value' => 'Cửa hàng điện tử chính hãng với giá tốt nhất',
                'type' => 'text',
                'description' => 'Mô tả website',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_logo',
                'value' => 'logo/site-logo.png',
                'type' => 'string',
                'description' => 'Logo website',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_email',
                'value' => 'contact@laravelshop.com',
                'type' => 'string',
                'description' => 'Email liên hệ',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_phone',
                'value' => '0123456789',
                'type' => 'string',
                'description' => 'Số điện thoại liên hệ',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_address',
                'value' => '123 Đường ABC, Quận XYZ, TP. Hà Nội',
                'type' => 'text',
                'description' => 'Địa chỉ liên hệ',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_currency',
                'value' => 'VND',
                'type' => 'string',
                'description' => 'Đơn vị tiền tệ mặc định',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_language',
                'value' => 'vi',
                'type' => 'string',
                'description' => 'Ngôn ngữ mặc định',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'free_shipping_threshold',
                'value' => '2000000',
                'type' => 'integer',
                'description' => 'Ngưỡng miễn phí ship (VND)',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_shipping_fee',
                'value' => '30000',
                'type' => 'integer',
                'description' => 'Phí ship mặc định (VND)',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/laravelshop',
                'type' => 'string',
                'description' => 'Link Facebook',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/laravelshop',
                'type' => 'string',
                'description' => 'Link Instagram',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_youtube',
                'value' => 'https://youtube.com/laravelshop',
                'type' => 'string',
                'description' => 'Link YouTube',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Chế độ bảo trì',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'registration_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Cho phép đăng ký tài khoản',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();
            $description = $setting['description'] ?? null;
            $isPublic = (bool) ($setting['is_public'] ?? false);

            unset($setting['description'], $setting['is_public'], $setting['created_at'], $setting['updated_at']);

            $setting['group'] = match (true) {
                str_starts_with($setting['key'], 'contact_') => 'contact',
                str_starts_with($setting['key'], 'social_') => 'social',
                str_contains($setting['key'], 'shipping') => 'shipping',
                in_array($setting['key'], ['site_name', 'site_description', 'site_logo'], true) => 'site',
                default => 'system',
            };
            $setting['meta'] = json_encode([
                'description' => $description,
                'is_public' => $isPublic,
            ], JSON_UNESCAPED_UNICODE);
            $setting['updated_at'] = $now;

            if (! $exists) {
                $setting['created_at'] = $now;
            }

            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
