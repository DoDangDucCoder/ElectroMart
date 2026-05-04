<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $brands = [
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'code' => 'APPLE',
                'description' => 'Apple Inc. là một tập đoàn công nghệ đa quốc gia của Mỹ.',
                'logo_path' => 'brands/apple-logo.png',
                'is_active' => true,
                'display_order' => 1,
                'meta' => [
                    'website' => 'https://www.apple.com',
                    'meta_title' => 'Apple - Thương hiệu công nghệ hàng đầu',
                    'meta_description' => 'Khám phá các sản phẩm Apple chính hãng với chất lượng cao và thiết kế đẹp.',
                ],
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'code' => 'SAMSUNG',
                'description' => 'Samsung Electronics là công ty điện tử lớn nhất thế giới.',
                'logo_path' => 'brands/samsung-logo.png',
                'is_active' => true,
                'display_order' => 2,
                'meta' => [
                    'website' => 'https://www.samsung.com',
                    'meta_title' => 'Samsung - Điện tử thông minh',
                    'meta_description' => 'Sản phẩm Samsung với công nghệ tiên tiến và giá cả hợp lý.',
                ],
            ],
            [
                'name' => 'Xiaomi',
                'slug' => 'xiaomi',
                'code' => 'XIAOMI',
                'description' => 'Xiaomi là công ty công nghệ Trung Quốc chuyên sản xuất điện thoại thông minh.',
                'logo_path' => 'brands/xiaomi-logo.png',
                'is_active' => true,
                'display_order' => 3,
                'meta' => [
                    'website' => 'https://www.mi.com',
                    'meta_title' => 'Xiaomi - Công nghệ giá rẻ',
                    'meta_description' => 'Điện thoại Xiaomi với hiệu năng cao và giá cả phải chăng.',
                ],
            ],
            [
                'name' => 'Oppo',
                'slug' => 'oppo',
                'code' => 'OPPO',
                'description' => 'OPPO là thương hiệu điện thoại thông minh của Trung Quốc.',
                'logo_path' => 'brands/oppo-logo.png',
                'is_active' => true,
                'display_order' => 4,
                'meta' => [
                    'website' => 'https://www.oppo.com',
                    'meta_title' => 'OPPO - Chụp ảnh đẹp',
                    'meta_description' => 'Điện thoại OPPO với camera chụp ảnh chuyên nghiệp.',
                ],
            ],
            [
                'name' => 'Vivo',
                'slug' => 'vivo',
                'code' => 'VIVO',
                'description' => 'Vivo là thương hiệu điện thoại thông minh của Trung Quốc.',
                'logo_path' => 'brands/vivo-logo.png',
                'is_active' => true,
                'display_order' => 5,
                'meta' => [
                    'website' => 'https://www.vivo.com',
                    'meta_title' => 'Vivo - Âm thanh sống động',
                    'meta_description' => 'Điện thoại Vivo với âm thanh chất lượng cao.',
                ],
            ],
        ];

        foreach ($brands as $brand) {
            $exists = DB::table('brands')->where('slug', $brand['slug'])->exists();
            $brand['meta'] = json_encode($brand['meta'], JSON_UNESCAPED_UNICODE);
            $brand['updated_at'] = $now;
            $brand['deleted_at'] = null;

            if (! $exists) {
                $brand['created_at'] = $now;
            }

            DB::table('brands')->updateOrInsert(
                ['slug' => $brand['slug']],
                $brand
            );
        }
    }
}
