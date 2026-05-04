<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $products = [
            [
                'name' => 'iPhone 15 Pro Max 256GB',
                'slug' => 'iphone-15-pro-max-256gb',
                'sku' => 'IP15PM256',
                'barcode' => '1234567890123',
                'unit' => 'Chiếc',
                'price' => 37900000,
                'sale_price' => 35900000,
                'cost_price' => 32000000,
                'stock_quantity' => 50,
                'stock_alert_threshold' => 10,
                'is_active' => true,
                'is_featured' => true,
                'display_order' => 1,
                'thumbnail_path' => 'products/iphone-15-pro-max.jpg',
                'description' => 'iPhone 15 Pro Max với chip A17 Pro mạnh mẽ, camera 48MP và màn hình Super Retina XDR 6.7 inch',
                'content' => '<h2>iPhone 15 Pro Max - Đỉnh cao công nghệ</h2>
                <p>iPhone 15 Pro Max mang đến trải nghiệm tuyệt vời với:</p>
                <ul>
                    <li>Chip A17 Pro với hiệu năng vượt trội</li>
                    <li>Camera chính 48MP với zoom quang học 5x</li>
                    <li>Màn hình Super Retina XDR 6.7 inch</li>
                    <li>Pin sử dụng cả ngày</li>
                    <li>Thiết kế titan cao cấp</li>
                </ul>',
                'views_count' => 1250,
                'meta' => json_encode([
                    'tags' => ['iphone', 'apple', 'smartphone', 'premium'],
                    'specifications' => [
                        'screen_size' => '6.7 inch',
                        'storage' => '256GB',
                        'camera' => '48MP',
                        'battery' => '4422 mAh'
                    ]
                ]),
                'meta_title' => 'iPhone 15 Pro Max 256GB chính hãng',
                'meta_description' => 'iPhone 15 Pro Max 256GB với chip A17 Pro, camera 48MP và màn hình 6.7 inch',
                'brand_id' => 1, // Apple
                'primary_category_id' => 4, // iPhone
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra 512GB',
                'slug' => 'samsung-galaxy-s24-ultra-512gb',
                'sku' => 'SGS24U512',
                'barcode' => '1234567890124',
                'unit' => 'Chiếc',
                'price' => 32900000,
                'sale_price' => 30900000,
                'cost_price' => 28000000,
                'stock_quantity' => 30,
                'stock_alert_threshold' => 5,
                'is_active' => true,
                'is_featured' => true,
                'display_order' => 2,
                'thumbnail_path' => 'products/galaxy-s24-ultra.jpg',
                'description' => 'Samsung Galaxy S24 Ultra với S Pen, camera 200MP và màn hình Dynamic AMOLED 2X 6.8 inch',
                'content' => '<h2>Samsung Galaxy S24 Ultra - Sức mạnh vô song</h2>
                <p>Galaxy S24 Ultra mang đến:</p>
                <ul>
                    <li>Chip Snapdragon 8 Gen 3</li>
                    <li>Camera chính 200MP với zoom quang học 10x</li>
                    <li>S Pen tích hợp</li>
                    <li>Màn hình Dynamic AMOLED 2X 6.8 inch</li>
                    <li>Pin 5000 mAh</li>
                </ul>',
                'views_count' => 980,
                'meta' => json_encode([
                    'tags' => ['samsung', 'galaxy', 'smartphone', 's-pen'],
                    'specifications' => [
                        'screen_size' => '6.8 inch',
                        'storage' => '512GB',
                        'camera' => '200MP',
                        'battery' => '5000 mAh'
                    ]
                ]),
                'meta_title' => 'Samsung Galaxy S24 Ultra 512GB chính hãng',
                'meta_description' => 'Galaxy S24 Ultra với S Pen, camera 200MP và màn hình 6.8 inch',
                'brand_id' => 2, // Samsung
                'primary_category_id' => 5, // Samsung Galaxy
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'MacBook Pro 14 inch M3',
                'slug' => 'macbook-pro-14-inch-m3',
                'sku' => 'MBP14M3',
                'barcode' => '1234567890125',
                'unit' => 'Chiếc',
                'price' => 45900000,
                'sale_price' => 42900000,
                'cost_price' => 40000000,
                'stock_quantity' => 20,
                'stock_alert_threshold' => 3,
                'is_active' => true,
                'is_featured' => true,
                'display_order' => 3,
                'thumbnail_path' => 'products/macbook-pro-14-m3.jpg',
                'description' => 'MacBook Pro 14 inch với chip M3, màn hình Liquid Retina XDR và hiệu năng vượt trội',
                'content' => '<h2>MacBook Pro 14 inch M3 - Hiệu năng đỉnh cao</h2>
                <p>MacBook Pro 14 inch M3 mang đến:</p>
                <ul>
                    <li>Chip M3 với hiệu năng vượt trội</li>
                    <li>Màn hình Liquid Retina XDR 14.2 inch</li>
                    <li>Pin sử dụng lên đến 18 giờ</li>
                    <li>Thiết kế nhôm nguyên khối</li>
                    <li>Kết nối Thunderbolt 4</li>
                </ul>',
                'views_count' => 750,
                'meta' => json_encode([
                    'tags' => ['macbook', 'apple', 'laptop', 'm3'],
                    'specifications' => [
                        'screen_size' => '14.2 inch',
                        'processor' => 'Apple M3',
                        'memory' => '8GB',
                        'storage' => '512GB SSD'
                    ]
                ]),
                'meta_title' => 'MacBook Pro 14 inch M3 chính hãng',
                'meta_description' => 'MacBook Pro 14 inch với chip M3 và màn hình Liquid Retina XDR',
                'brand_id' => 1, // Apple
                'primary_category_id' => 8, // Laptop Gaming
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AirPods Pro 2nd Gen',
                'slug' => 'airpods-pro-2nd-gen',
                'sku' => 'APP2G',
                'barcode' => '1234567890126',
                'unit' => 'Chiếc',
                'price' => 5990000,
                'sale_price' => 5490000,
                'cost_price' => 4500000,
                'stock_quantity' => 100,
                'stock_alert_threshold' => 20,
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 4,
                'thumbnail_path' => 'products/airpods-pro-2nd.jpg',
                'description' => 'AirPods Pro thế hệ 2 với chip H2, chống ồn chủ động và âm thanh không gian',
                'content' => '<h2>AirPods Pro 2nd Gen - Âm thanh hoàn hảo</h2>
                <p>AirPods Pro thế hệ 2 với:</p>
                <ul>
                    <li>Chip H2 với hiệu năng âm thanh vượt trội</li>
                    <li>Chống ồn chủ động thế hệ 2</li>
                    <li>Âm thanh không gian cá nhân hóa</li>
                    <li>Pin sử dụng lên đến 6 giờ</li>
                    <li>Case sạc MagSafe</li>
                </ul>',
                'views_count' => 2100,
                'meta' => json_encode([
                    'tags' => ['airpods', 'apple', 'earphone', 'wireless'],
                    'specifications' => [
                        'driver' => 'Dynamic driver',
                        'battery' => '6 hours',
                        'case_battery' => '30 hours',
                        'connectivity' => 'Bluetooth 5.3'
                    ]
                ]),
                'meta_title' => 'AirPods Pro 2nd Gen chính hãng',
                'meta_description' => 'AirPods Pro thế hệ 2 với chip H2 và chống ồn chủ động',
                'brand_id' => 1, // Apple
                'primary_category_id' => 10, // Sạc & Cáp
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Samsung Galaxy Buds2 Pro',
                'slug' => 'samsung-galaxy-buds2-pro',
                'sku' => 'SGB2P',
                'barcode' => '1234567890127',
                'unit' => 'Chiếc',
                'price' => 3990000,
                'sale_price' => 3490000,
                'cost_price' => 2800000,
                'stock_quantity' => 80,
                'stock_alert_threshold' => 15,
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 5,
                'thumbnail_path' => 'products/galaxy-buds2-pro.jpg',
                'description' => 'Galaxy Buds2 Pro với âm thanh 24-bit, chống ồn thông minh và thiết kế nhỏ gọn',
                'content' => '<h2>Galaxy Buds2 Pro - Âm thanh cao cấp</h2>
                <p>Galaxy Buds2 Pro mang đến:</p>
                <ul>
                    <li>Âm thanh 24-bit Hi-Fi</li>
                    <li>Chống ồn thông minh</li>
                    <li>Thiết kế nhỏ gọn, nhẹ</li>
                    <li>Pin sử dụng lên đến 5 giờ</li>
                    <li>Tương thích với Galaxy và Android</li>
                </ul>',
                'views_count' => 1650,
                'meta' => json_encode([
                    'tags' => ['galaxy', 'samsung', 'earphone', 'wireless'],
                    'specifications' => [
                        'driver' => '2-way dynamic driver',
                        'battery' => '5 hours',
                        'case_battery' => '18 hours',
                        'connectivity' => 'Bluetooth 5.3'
                    ]
                ]),
                'meta_title' => 'Samsung Galaxy Buds2 Pro chính hãng',
                'meta_description' => 'Galaxy Buds2 Pro với âm thanh 24-bit và chống ồn thông minh',
                'brand_id' => 2, // Samsung
                'primary_category_id' => 10, // Sạc & Cáp
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $brandSlugsBySeedId = [
            1 => 'apple',
            2 => 'samsung',
        ];

        $categorySlugsBySeedId = [
            1 => 'dien-thoai',
            2 => 'laptop',
            3 => 'phu-kien',
            4 => 'iphone',
            5 => 'samsung-galaxy',
            8 => 'laptop-gaming',
            10 => 'sac-cap',
        ];

        foreach ($products as $product) {
            $brandReference = $product['brand_id'] ?? null;
            $categoryReference = $product['primary_category_id'] ?? null;

            unset($product['created_at'], $product['updated_at']);

            $product['brand_id'] = null;
            $product['primary_category_id'] = null;

            if ($brandReference !== null) {
                $brandSlug = is_int($brandReference)
                    ? ($brandSlugsBySeedId[$brandReference] ?? null)
                    : $brandReference;

                if ($brandSlug !== null) {
                    $product['brand_id'] = DB::table('brands')
                        ->where('slug', $brandSlug)
                        ->value('id');
                }
            }

            if ($categoryReference !== null) {
                $categorySlug = is_int($categoryReference)
                    ? ($categorySlugsBySeedId[$categoryReference] ?? null)
                    : $categoryReference;

                if ($categorySlug !== null) {
                    $product['primary_category_id'] = DB::table('product_categories')
                        ->where('slug', $categorySlug)
                        ->value('id');
                }
            }

            $exists = DB::table('products')->where('slug', $product['slug'])->exists();
            $product['updated_at'] = $now;
            $product['deleted_at'] = null;

            if (! $exists) {
                $product['created_at'] = $now;
            }

            DB::table('products')->updateOrInsert(
                ['slug' => $product['slug']],
                $product
            );
        }

        // Insert product category relationships
        $productCategories = [
            ['product_id' => 1, 'category_id' => 4], // iPhone 15 Pro Max -> iPhone
            ['product_id' => 1, 'category_id' => 1],  // iPhone 15 Pro Max -> Điện thoại
            ['product_id' => 2, 'category_id' => 5], // Galaxy S24 Ultra -> Samsung Galaxy
            ['product_id' => 2, 'category_id' => 1],  // Galaxy S24 Ultra -> Điện thoại
            ['product_id' => 3, 'category_id' => 8], // MacBook Pro -> Laptop Gaming
            ['product_id' => 3, 'category_id' => 2],  // MacBook Pro -> Laptop
            ['product_id' => 4, 'category_id' => 10], // AirPods Pro -> Sạc & Cáp
            ['product_id' => 4, 'category_id' => 3],   // AirPods Pro -> Phụ kiện
            ['product_id' => 5, 'category_id' => 10], // Galaxy Buds2 Pro -> Sạc & Cáp
            ['product_id' => 5, 'category_id' => 3],   // Galaxy Buds2 Pro -> Phụ kiện
        ];

        $productSlugsBySeedId = [
            1 => 'iphone-15-pro-max-256gb',
            2 => 'samsung-galaxy-s24-ultra-512gb',
            3 => 'macbook-pro-14-inch-m3',
            4 => 'airpods-pro-2nd-gen',
            5 => 'samsung-galaxy-buds2-pro',
        ];

        foreach ($productCategories as $productCategory) {
            $productSlug = $productSlugsBySeedId[$productCategory['product_id']] ?? null;
            $categorySlug = $categorySlugsBySeedId[$productCategory['category_id']] ?? null;

            if ($productSlug === null || $categorySlug === null) {
                continue;
            }

            $productId = DB::table('products')->where('slug', $productSlug)->value('id');
            $categoryId = DB::table('product_categories')->where('slug', $categorySlug)->value('id');

            if (! $productId || ! $categoryId) {
                continue;
            }

            $exists = DB::table('product_category_product')
                ->where('product_id', $productId)
                ->where('product_category_id', $categoryId)
                ->exists();

            $values = [
                'product_id' => $productId,
                'product_category_id' => $categoryId,
                'updated_at' => $now,
            ];

            if (! $exists) {
                $values['created_at'] = $now;
            }

            DB::table('product_category_product')->updateOrInsert(
                [
                    'product_id' => $productId,
                    'product_category_id' => $categoryId,
                ],
                $values
            );
        }
    }
}
