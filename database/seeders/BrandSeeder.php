<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'code' => 'APPLE',
                'description' => 'Apple Inc. là một tập đoàn công nghệ đa quốc gia của Mỹ.',
                'logo_path' => 'brands/apple-logo.png',
                'website' => 'https://www.apple.com',
                'is_active' => true,
                'display_order' => 1,
                'meta_title' => 'Apple - Thương hiệu công nghệ hàng đầu',
                'meta_description' => 'Khám phá các sản phẩm Apple chính hãng với chất lượng cao và thiết kế đẹp.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'code' => 'SAMSUNG',
                'description' => 'Samsung Electronics là công ty điện tử lớn nhất thế giới.',
                'logo_path' => 'brands/samsung-logo.png',
                'website' => 'https://www.samsung.com',
                'is_active' => true,
                'display_order' => 2,
                'meta_title' => 'Samsung - Điện tử thông minh',
                'meta_description' => 'Sản phẩm Samsung với công nghệ tiên tiến và giá cả hợp lý.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Xiaomi',
                'slug' => 'xiaomi',
                'code' => 'XIAOMI',
                'description' => 'Xiaomi là công ty công nghệ Trung Quốc chuyên sản xuất điện thoại thông minh.',
                'logo_path' => 'brands/xiaomi-logo.png',
                'website' => 'https://www.mi.com',
                'is_active' => true,
                'display_order' => 3,
                'meta_title' => 'Xiaomi - Công nghệ giá rẻ',
                'meta_description' => 'Điện thoại Xiaomi với hiệu năng cao và giá cả phải chăng.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Oppo',
                'slug' => 'oppo',
                'code' => 'OPPO',
                'description' => 'OPPO là thương hiệu điện thoại thông minh của Trung Quốc.',
                'logo_path' => 'brands/oppo-logo.png',
                'website' => 'https://www.oppo.com',
                'is_active' => true,
                'display_order' => 4,
                'meta_title' => 'OPPO - Chụp ảnh đẹp',
                'meta_description' => 'Điện thoại OPPO với camera chụp ảnh chuyên nghiệp.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vivo',
                'slug' => 'vivo',
                'code' => 'VIVO',
                'description' => 'Vivo là thương hiệu điện thoại thông minh của Trung Quốc.',
                'logo_path' => 'brands/vivo-logo.png',
                'website' => 'https://www.vivo.com',
                'is_active' => true,
                'display_order' => 5,
                'meta_title' => 'Vivo - Âm thanh sống động',
                'meta_description' => 'Điện thoại Vivo với âm thanh chất lượng cao.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('brands')->insert($brands);
    }
}
