<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tin tức công nghệ',
                'slug' => 'tin-tuc-cong-nghe',
                'description' => 'Các tin tức mới nhất về công nghệ và điện tử',
                'is_active' => true,
                'display_order' => 1,
                'meta_title' => 'Tin tức công nghệ mới nhất',
                'meta_description' => 'Cập nhật tin tức công nghệ, điện tử mới nhất',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Đánh giá sản phẩm',
                'slug' => 'danh-gia-san-pham',
                'description' => 'Đánh giá chi tiết các sản phẩm điện tử',
                'is_active' => true,
                'display_order' => 2,
                'meta_title' => 'Đánh giá sản phẩm điện tử',
                'meta_description' => 'Đánh giá chi tiết, khách quan về các sản phẩm điện tử',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hướng dẫn sử dụng',
                'slug' => 'huong-dan-su-dung',
                'description' => 'Hướng dẫn sử dụng các sản phẩm điện tử',
                'is_active' => true,
                'display_order' => 3,
                'meta_title' => 'Hướng dẫn sử dụng sản phẩm',
                'meta_description' => 'Hướng dẫn chi tiết cách sử dụng các sản phẩm điện tử',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Khuyến mãi',
                'slug' => 'khuyen-mai',
                'description' => 'Thông tin về các chương trình khuyến mãi',
                'is_active' => true,
                'display_order' => 4,
                'meta_title' => 'Khuyến mãi sản phẩm điện tử',
                'meta_description' => 'Cập nhật các chương trình khuyến mãi hấp dẫn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('blog_categories')->insert($categories);
    }
}
