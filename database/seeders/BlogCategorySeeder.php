<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $hasMetaColumn = Schema::hasColumn('blog_categories', 'meta');

        $categories = [
            [
                'name' => 'Tin tức công nghệ',
                'slug' => 'tin-tuc-cong-nghe',
                'description' => 'Các tin tức mới nhất về công nghệ và điện tử',
                'is_active' => true,
                'display_order' => 1,
                'meta' => [
                    'meta_title' => 'Tin tức công nghệ mới nhất',
                    'meta_description' => 'Cập nhật tin tức công nghệ, điện tử mới nhất',
                ],
            ],
            [
                'name' => 'Đánh giá sản phẩm',
                'slug' => 'danh-gia-san-pham',
                'description' => 'Đánh giá chi tiết các sản phẩm điện tử',
                'is_active' => true,
                'display_order' => 2,
                'meta' => [
                    'meta_title' => 'Đánh giá sản phẩm điện tử',
                    'meta_description' => 'Đánh giá chi tiết, khách quan về các sản phẩm điện tử',
                ],
            ],
            [
                'name' => 'Hướng dẫn sử dụng',
                'slug' => 'huong-dan-su-dung',
                'description' => 'Hướng dẫn sử dụng các sản phẩm điện tử',
                'is_active' => true,
                'display_order' => 3,
                'meta' => [
                    'meta_title' => 'Hướng dẫn sử dụng sản phẩm',
                    'meta_description' => 'Hướng dẫn chi tiết cách sử dụng các sản phẩm điện tử',
                ],
            ],
            [
                'name' => 'Khuyến mãi',
                'slug' => 'khuyen-mai',
                'description' => 'Thông tin về các chương trình khuyến mãi',
                'is_active' => true,
                'display_order' => 4,
                'meta' => [
                    'meta_title' => 'Khuyến mãi sản phẩm điện tử',
                    'meta_description' => 'Cập nhật các chương trình khuyến mãi hấp dẫn',
                ],
            ],
        ];

        foreach ($categories as $category) {
            $exists = DB::table('blog_categories')->where('slug', $category['slug'])->exists();

            if ($hasMetaColumn) {
                $category['meta'] = json_encode($category['meta'], JSON_UNESCAPED_UNICODE);
            } else {
                unset($category['meta']);
            }

            $category['updated_at'] = $now;
            $category['deleted_at'] = null;

            if (! $exists) {
                $category['created_at'] = $now;
            }

            DB::table('blog_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
