<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'blog_category_id' => 1, // Tin tức công nghệ
                'title' => 'iPhone 15 Pro Max: Đánh giá chi tiết sau 1 tháng sử dụng',
                'slug' => 'iphone-15-pro-max-danh-gia-chi-tiet',
                'excerpt' => 'Sau 1 tháng sử dụng iPhone 15 Pro Max, chúng tôi có những đánh giá chi tiết về hiệu năng, camera và trải nghiệm người dùng.',
                'content' => '<h2>iPhone 15 Pro Max: Đánh giá chi tiết</h2>
                <p>Sau 1 tháng sử dụng iPhone 15 Pro Max, chúng tôi có những đánh giá chi tiết về hiệu năng, camera và trải nghiệm người dùng.</p>
                
                <h3>Thiết kế và chất liệu</h3>
                <p>iPhone 15 Pro Max sử dụng vật liệu titan cao cấp, mang lại cảm giác sang trọng và bền bỉ. Thiết kế mới với các cạnh bo tròn mềm mại hơn so với thế hệ trước.</p>
                
                <h3>Hiệu năng</h3>
                <p>Chip A17 Pro mang lại hiệu năng vượt trội, xử lý mượt mà mọi tác vụ từ gaming đến chỉnh sửa video 4K.</p>
                
                <h3>Camera</h3>
                <p>Hệ thống camera 48MP với zoom quang học 5x cho chất lượng ảnh tuyệt vời trong mọi điều kiện ánh sáng.</p>',
                'featured_image_path' => 'blog/iphone-15-pro-max-review.jpg',
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'views_count' => 1250,
                'meta_title' => 'iPhone 15 Pro Max đánh giá chi tiết',
                'meta_description' => 'Đánh giá chi tiết iPhone 15 Pro Max sau 1 tháng sử dụng',
                'meta' => json_encode([
                    'tags' => ['iphone', 'review', 'smartphone'],
                    'author' => 'Admin',
                    'reading_time' => '5 phút'
                ]),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(5),
            ],
            [
                'blog_category_id' => 2, // Đánh giá sản phẩm
                'title' => 'Samsung Galaxy S24 Ultra: So sánh với iPhone 15 Pro Max',
                'slug' => 'samsung-galaxy-s24-ultra-so-sanh-iphone-15-pro-max',
                'excerpt' => 'So sánh chi tiết giữa Samsung Galaxy S24 Ultra và iPhone 15 Pro Max về camera, hiệu năng và trải nghiệm.',
                'content' => '<h2>So sánh Galaxy S24 Ultra vs iPhone 15 Pro Max</h2>
                <p>Cuộc chiến giữa hai flagship hàng đầu của Samsung và Apple luôn thu hút sự quan tâm của người dùng.</p>
                
                <h3>Camera</h3>
                <p>Galaxy S24 Ultra có camera 200MP với zoom quang học 10x, trong khi iPhone 15 Pro Max có camera 48MP với zoom 5x.</p>
                
                <h3>Hiệu năng</h3>
                <p>Cả hai đều có hiệu năng mạnh mẽ với chip Snapdragon 8 Gen 3 và A17 Pro.</p>
                
                <h3>Màn hình</h3>
                <p>S24 Ultra có màn hình 6.8 inch Dynamic AMOLED 2X, iPhone 15 Pro Max có màn hình 6.7 inch Super Retina XDR.</p>',
                'featured_image_path' => 'blog/galaxy-s24-vs-iphone-15.jpg',
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'views_count' => 2100,
                'meta_title' => 'So sánh Galaxy S24 Ultra vs iPhone 15 Pro Max',
                'meta_description' => 'So sánh chi tiết Galaxy S24 Ultra và iPhone 15 Pro Max',
                'meta' => json_encode([
                    'tags' => ['samsung', 'iphone', 'comparison'],
                    'author' => 'Editor',
                    'reading_time' => '7 phút'
                ]),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(3),
            ],
            [
                'blog_category_id' => 3, // Hướng dẫn sử dụng
                'title' => 'Hướng dẫn sử dụng AirPods Pro hiệu quả',
                'slug' => 'huong-dan-su-dung-airpods-pro-hieu-qua',
                'excerpt' => 'Hướng dẫn chi tiết cách sử dụng AirPods Pro để có trải nghiệm âm thanh tốt nhất.',
                'content' => '<h2>Hướng dẫn sử dụng AirPods Pro</h2>
                <p>AirPods Pro mang lại trải nghiệm âm thanh tuyệt vời với nhiều tính năng thông minh.</p>
                
                <h3>Kết nối và thiết lập</h3>
                <p>Mở case AirPods Pro gần iPhone, nhấn "Kết nối" để thiết lập nhanh chóng.</p>
                
                <h3>Chế độ chống ồn</h3>
                <p>Sử dụng chế độ chống ồn chủ động để tận hưởng âm thanh trong không gian yên tĩnh.</p>
                
                <h3>Điều khiển cảm ứng</h3>
                <p>Nhấn một lần để tạm dừng/phát, nhấn hai lần để chuyển bài, nhấn giữ để chuyển chế độ.</p>',
                'featured_image_path' => 'blog/airpods-pro-guide.jpg',
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'views_count' => 850,
                'meta_title' => 'Hướng dẫn sử dụng AirPods Pro',
                'meta_description' => 'Hướng dẫn chi tiết cách sử dụng AirPods Pro hiệu quả',
                'meta' => json_encode([
                    'tags' => ['airpods', 'guide', 'tutorial'],
                    'author' => 'Admin',
                    'reading_time' => '4 phút'
                ]),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(1),
            ],
            [
                'blog_category_id' => 4, // Khuyến mãi
                'title' => 'Chương trình khuyến mãi Black Friday 2024',
                'slug' => 'chuong-trinh-khuyen-mai-black-friday-2024',
                'excerpt' => 'Thông tin chi tiết về chương trình khuyến mãi Black Friday 2024 với nhiều ưu đãi hấp dẫn.',
                'content' => '<h2>Black Friday 2024 - Ưu đãi lớn</h2>
                <p>Chương trình khuyến mãi Black Friday 2024 với nhiều ưu đãi hấp dẫn cho các sản phẩm điện tử.</p>
                
                <h3>Ưu đãi đặc biệt</h3>
                <ul>
                    <li>Giảm giá lên đến 50% cho iPhone</li>
                    <li>Giảm giá 30% cho Samsung Galaxy</li>
                    <li>Miễn phí ship cho đơn hàng từ 2 triệu</li>
                    <li>Tặng kèm phụ kiện trị giá 500k</li>
                </ul>
                
                <h3>Thời gian áp dụng</h3>
                <p>Từ 29/11/2024 đến 02/12/2024</p>',
                'featured_image_path' => 'blog/black-friday-2024.jpg',
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now(),
                'views_count' => 3200,
                'meta_title' => 'Black Friday 2024 - Ưu đãi lớn',
                'meta_description' => 'Chương trình khuyến mãi Black Friday 2024 với nhiều ưu đãi hấp dẫn',
                'meta' => json_encode([
                    'tags' => ['black-friday', 'sale', 'promotion'],
                    'author' => 'Admin',
                    'reading_time' => '3 phút'
                ]),
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ],
        ];

        DB::table('blog_posts')->insert($posts);
    }
}
