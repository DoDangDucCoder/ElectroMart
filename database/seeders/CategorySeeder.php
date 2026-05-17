<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = [
            // Main categories (3)
            [
                'name' => 'Điện thoại',
                'slug' => 'dien-thoai',
                'code' => 'PHONE',
                'parent_id' => null,
                'description' => 'Các loại điện thoại thông minh từ các thương hiệu nổi tiếng',
                'thumbnail_path' => 'categories/phone-category.jpg',
                'is_active' => true,
                'display_order' => 1,
                'meta_title' => 'Điện thoại thông minh chính hãng',
                'meta_description' => 'Mua điện thoại thông minh giá tốt, chính hãng tại cửa hàng chúng tôi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laptop',
                'slug' => 'laptop',
                'code' => 'LAPTOP',
                'parent_id' => null,
                'description' => 'Laptop gaming, văn phòng từ các thương hiệu uy tín',
                'thumbnail_path' => 'categories/laptop-category.jpg',
                'is_active' => true,
                'display_order' => 2,
                'meta_title' => 'Laptop chính hãng giá tốt',
                'meta_description' => 'Laptop gaming, văn phòng với cấu hình mạnh mẽ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phụ kiện',
                'slug' => 'phu-kien',
                'code' => 'ACCESSORIES',
                'parent_id' => null,
                'description' => 'Phụ kiện điện thoại, laptop và các thiết bị điện tử',
                'thumbnail_path' => 'categories/accessories-category.jpg',
                'is_active' => true,
                'display_order' => 3,
                'meta_title' => 'Phụ kiện điện tử chính hãng',
                'meta_description' => 'Phụ kiện điện thoại, laptop chất lượng cao',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Sub-categories for Điện thoại (3)
            [
                'name' => 'iPhone',
                'slug' => 'iphone',
                'code' => 'IPHONE',
                'parent_id' => 1,
                'description' => 'Điện thoại iPhone từ Apple',
                'thumbnail_path' => 'categories/iphone-subcategory.jpg',
                'is_active' => true,
                'display_order' => 1,
                'meta_title' => 'iPhone chính hãng Apple',
                'meta_description' => 'iPhone mới nhất với giá tốt nhất thị trường',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Samsung Galaxy',
                'slug' => 'samsung-galaxy',
                'code' => 'GALAXY',
                'parent_id' => 1,
                'description' => 'Điện thoại Samsung Galaxy',
                'thumbnail_path' => 'categories/galaxy-subcategory.jpg',
                'is_active' => true,
                'display_order' => 2,
                'meta_title' => 'Samsung Galaxy chính hãng',
                'meta_description' => 'Điện thoại Samsung Galaxy với công nghệ tiên tiến',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Android khác',
                'slug' => 'android-khac',
                'code' => 'ANDROID',
                'parent_id' => 1,
                'description' => 'Các điện thoại Android khác',
                'thumbnail_path' => 'categories/android-subcategory.jpg',
                'is_active' => true,
                'display_order' => 3,
                'meta_title' => 'Điện thoại Android giá rẻ',
                'meta_description' => 'Điện thoại Android từ các thương hiệu uy tín',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Sub-categories for Laptop (2)
            [
                'name' => 'Laptop Gaming',
                'slug' => 'laptop-gaming',
                'code' => 'GAMING',
                'parent_id' => 2,
                'description' => 'Laptop chuyên game với cấu hình mạnh',
                'thumbnail_path' => 'categories/gaming-laptop.jpg',
                'is_active' => true,
                'display_order' => 1,
                'meta_title' => 'Laptop Gaming cấu hình cao',
                'meta_description' => 'Laptop gaming với card đồ họa mạnh mẽ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laptop Văn phòng',
                'slug' => 'laptop-van-phong',
                'code' => 'OFFICE',
                'parent_id' => 2,
                'description' => 'Laptop phù hợp cho công việc văn phòng',
                'thumbnail_path' => 'categories/office-laptop.jpg',
                'is_active' => true,
                'display_order' => 2,
                'meta_title' => 'Laptop văn phòng giá tốt',
                'meta_description' => 'Laptop văn phòng với hiệu năng ổn định',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Sub-categories for Phụ kiện (3)
            [
                'name' => 'Sạc & Cáp',
                'slug' => 'sac-cap',
                'code' => 'CHARGER',
                'parent_id' => 3,
                'description' => 'Sạc điện thoại và cáp kết nối',
                'thumbnail_path' => 'categories/charger-cable.jpg',
                'is_active' => true,
                'display_order' => 1,
                'meta_title' => 'Sạc và cáp chính hãng',
                'meta_description' => 'Sạc điện thoại và cáp kết nối chất lượng cao',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ốp lưng & Kính cường lực',
                'slug' => 'op-lung-kinh-cuong-luc',
                'code' => 'CASE',
                'parent_id' => 3,
                'description' => 'Ốp lưng và kính cường lực bảo vệ điện thoại',
                'thumbnail_path' => 'categories/case-screen-protector.jpg',
                'is_active' => true,
                'display_order' => 2,
                'meta_title' => 'Ốp lưng và kính cường lực',
                'meta_description' => 'Bảo vệ điện thoại với ốp lưng và kính cường lực',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Linh kiện',
                'slug' => 'linh-kien',
                'code' => 'COMPONENTS',
                'parent_id' => 3,
                'description' => 'Linh kiện điện tử, phụ tùng thay thế',
                'thumbnail_path' => 'categories/components.jpg',
                'is_active' => true,
                'display_order' => 3,
                'meta_title' => 'Linh kiện điện tử chính hãng',
                'meta_description' => 'Linh kiện, phụ tùng chất lượng cao',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $parentSlugsBySeedId = [
            1 => 'dien-thoai',
            2 => 'laptop',
            3 => 'phu-kien',
        ];

        foreach ($categories as $category) {
            $parentReference = $category['parent_id'] ?? null;

            unset($category['created_at'], $category['updated_at']);

            $category['parent_id'] = null;

            if ($parentReference !== null) {
                $parentSlug = is_int($parentReference)
                    ? ($parentSlugsBySeedId[$parentReference] ?? null)
                    : $parentReference;

                if ($parentSlug !== null) {
                    $category['parent_id'] = DB::table('product_categories')
                        ->where('slug', $parentSlug)
                        ->value('id');
                }
            }

            $exists = DB::table('product_categories')->where('slug', $category['slug'])->exists();
            $category['updated_at'] = $now;
            $category['deleted_at'] = null;

            if (! $exists) {
                $category['created_at'] = $now;
            }

            DB::table('product_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
