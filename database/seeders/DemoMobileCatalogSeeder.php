<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Support\AssetUrl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoMobileCatalogSeeder extends Seeder
{
    public function run()
    {
        $category = ProductCategory::where('slug', 'laptop')->first();
        if (!$category) {
            $category = ProductCategory::create([
                'name' => 'Laptop',
                'slug' => 'laptop',
                'is_active' => true,
            ]);
        }

        $products = [
            [
                "name" => "Laptop Acer Nitro ProPanel ANV16S-71-58WQ",
                "brand" => "Acer",
                "finalPrice" => 39990000,
                "originalPrice" => null,
                "imageUrl" => "https://lh3.googleusercontent.com/proxy/7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU",
                "specs" => "Core 5 210H, RTX 5050, 32GB, 512GB, 2.1 kg, 16\" WQXGA/ IPS/ 180Hz",
                "description" => "Laptop Acer Nitro ProPanel ANV16S-71-58WQ (Core 5 210H/ GeForce RTX™ 5050/ 32GB/ 512GB/ Win 11 Home SL)"
            ],
            [
                "name" => "Laptop Asus TUF Gaming F16 FX608JHI-TU209W",
                "brand" => "Asus",
                "finalPrice" => 41290000,
                "originalPrice" => 42990000,
                "imageUrl" => "https://lh3.googleusercontent.com/proxy/7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU",
                "specs" => "i7-14650HX, RTX 5050, 16GB, 512GB, 2.2 kg, 16\" WUXGA/ IPS/ 165Hz",
                "description" => "Laptop Asus TUF Gaming F16 FX608JHI-TU209W (i7-14650HX/ GeForce RTX™ 5050/ 16GB/ 512GB/ Win 11 Home)"
            ],
            [
                "name" => "Laptop Asus Vivobook S14 S3407CA-SF923W",
                "brand" => "Asus",
                "finalPrice" => 27790000,
                "originalPrice" => 31990000,
                "imageUrl" => "https://lh3.googleusercontent.com/proxy/7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU",
                "specs" => "U7-255H, Intel Graphics, 16GB, 512GB, 1.4 kg, 14\" WUXGA/ OLED/ 60Hz",
                "description" => "Laptop Asus Vivobook S14 S3407CA-SF923W (Ultra 7-255H/ 16GB/ 512GB/ Win 11 Home)"
            ],
            [
                "name" => "Laptop Lenovo ThinkPad X9-14 Gen 1",
                "brand" => "Lenovo",
                "finalPrice" => 55190000,
                "originalPrice" => null,
                "imageUrl" => "https://lh3.googleusercontent.com/proxy/7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU",
                "specs" => "U7-258V, Intel Graphics, 32GB, 512GB, 1.2 kg, 14\" WUXGA/ OLED/ 60Hz",
                "description" => "Laptop Lenovo ThinkPad X9-14 Gen 1 - 21QA006JVN (Ultra 7-258V/ Intel® Graphics/ 32GB/ 512GB/ Windows 11 Pro)"
            ],
            [
                "name" => "Laptop Asus Vivobook S14 S3407CA-SF913W",
                "brand" => "Asus",
                "finalPrice" => 24890000,
                "originalPrice" => 29990000,
                "imageUrl" => "https://lh3.googleusercontent.com/proxy/7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU",
                "specs" => "U5-225H, Intel Graphics, 16GB, 512GB, 1.4 kg, 14\" WUXGA/ OLED/ 60Hz",
                "description" => "Laptop Asus Vivobook S14 S3407CA-SF913W (Ultra 5-225H/ 16GB/ 512GB/ Win 11 Home)"
            ],
            [
                "name" => "Laptop Lenovo ThinkPad X1 Carbon Gen 13",
                "brand" => "Lenovo",
                "finalPrice" => 60990000,
                "originalPrice" => 64790000,
                "imageUrl" => "https://lh3.googleusercontent.com/proxy/7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU7L5Fz6pXmU",
                "specs" => "U7-258V, Intel Graphics, 32GB, 512GB, 1 kg, 14\" 2.8K/ OLED",
                "description" => "Laptop Lenovo ThinkPad X1 Carbon Gen 13 - 21NS010BVN (Ultra 7-258V/ 32GB/ 512GB/ Win 11 Pro)"
            ]
        ];

        foreach ($products as $p) {
            $brand = Brand::firstOrCreate(
                ['slug' => Str::slug($p['brand'])],
                [
                    'name' => $p['brand'],
                    'is_active' => true,
                ]
            );
            
            $price = $p['originalPrice'] ?? $p['finalPrice'];
            $salePrice = $p['originalPrice'] ? $p['finalPrice'] : null;

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($p['name'])],
                [
                    'name' => $p['name'],
                    'brand_id' => $brand->id,
                    'primary_category_id' => $category->id,
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'is_active' => true,
                    'is_featured' => true,
                    'stock_quantity' => 10,
                    'description' => $p['description'],
                    'content' => $p['specs'],
                    'thumbnail_path' => AssetUrl::isAbsoluteUrl($p['imageUrl'] ?? null)
                        ? $p['imageUrl']
                        : AssetUrl::PRODUCT_FALLBACK,
                    'meta' => [
                        'specs_preview' => [
                            ['icon' => 'fa-microchip', 'label' => explode(',', $p['specs'])[0] ?? 'CPU'],
                            ['icon' => 'fa-memory', 'label' => explode(',', $p['specs'])[2] ?? 'RAM'],
                            ['icon' => 'fa-hdd-o', 'label' => explode(',', $p['specs'])[3] ?? 'SSD'],
                        ],
                    ],
                ]
            );

            $product->categories()->syncWithoutDetaching([$category->id]);
        }
    }
}
