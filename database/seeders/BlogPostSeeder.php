<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

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
                'featured_image_path' => 'assets/news/anker-735-review-1-year.svg',
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
                'featured_image_path' => 'assets/news/anker-735-compare.svg',
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
                'featured_image_path' => 'assets/news/macbook-battery-tips.svg',
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
                'featured_image_path' => 'assets/news/anker-735-compare.svg',
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
            [
                'blog_category_id' => 2,
                'title' => 'Sony HT-S40R 600W: trải nghiệm âm thanh tại gia',
                'slug' => 'sony-ht-s40r-600w-trai-nghiem-am-thanh-tai-gia',
                'excerpt' => 'Gợi ý cách bố trí loa thanh Sony HT-S40R để xem phim, nghe nhạc và chơi game cân bằng hơn trong phòng khách.',
                'content' => '<h2>Trải nghiệm Sony HT-S40R 600W</h2><p>Sony HT-S40R phù hợp với người dùng muốn nâng cấp âm thanh TV bằng hệ loa thanh có subwoofer và loa sau.</p><h3>Cách bố trí</h3><p>Đặt loa thanh thẳng với TV, subwoofer gần tường bên và loa sau ở vị trí ngang tai để tạo hiệu ứng vòm rõ hơn.</p><h3>Phù hợp với ai?</h3><p>Sản phẩm phù hợp cho phòng khách vừa, nhu cầu xem phim gia đình và nghe nhạc giải trí hằng ngày.</p>',
                'featured_image_path' => 'assets/news/anker-735-review-1-year.svg',
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now()->subDays(4),
                'views_count' => 980,
                'meta_title' => 'Đánh giá Sony HT-S40R 600W',
                'meta_description' => 'Trải nghiệm loa thanh Sony HT-S40R 600W cho phòng khách',
                'meta' => json_encode(['tags' => ['sony', 'loa thanh', 'home theater'], 'author' => 'Admin', 'reading_time' => '4 phút']),
            ],
            [
                'blog_category_id' => 3,
                'title' => 'Kinh nghiệm chọn màn hình gaming chơi game mượt',
                'slug' => 'kinh-nghiem-chon-man-hinh-gaming-choi-game-muot',
                'excerpt' => 'Các tiêu chí cần xem khi chọn màn hình gaming: tần số quét, thời gian phản hồi, tấm nền, độ phân giải và cổng kết nối.',
                'content' => '<h2>Chọn màn hình gaming theo nhu cầu</h2><p>Một màn hình gaming tốt cần cân bằng giữa tần số quét, độ phân giải và chất lượng màu.</p><h3>Tần số quét</h3><p>Game eSports nên ưu tiên 144Hz trở lên, còn game nhập vai có thể chọn màn hình độ phân giải cao hơn.</p><h3>Cổng kết nối</h3><p>Kiểm tra DisplayPort hoặc HDMI tương thích để tận dụng đúng tần số quét của màn hình.</p>',
                'featured_image_path' => 'assets/news/anker-735-compare.svg',
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(6),
                'views_count' => 760,
                'meta_title' => 'Kinh nghiệm chọn màn hình gaming',
                'meta_description' => 'Hướng dẫn chọn màn hình gaming phù hợp nhu cầu',
                'meta' => json_encode(['tags' => ['màn hình gaming', 'eSports', 'phụ kiện'], 'author' => 'Editor', 'reading_time' => '5 phút']),
            ],
            [
                'blog_category_id' => 3,
                'title' => 'Mẹo tối ưu pin MacBook khi làm việc di động',
                'slug' => 'meo-toi-uu-pin-macbook-khi-lam-viec-di-dong',
                'excerpt' => 'Những thiết lập đơn giản giúp MacBook dùng pin lâu hơn khi học tập, họp online hoặc làm việc ngoài văn phòng.',
                'content' => '<h2>Tối ưu pin MacBook</h2><p>Pin MacBook bền hơn khi bạn kiểm soát độ sáng, ứng dụng nền và cách sạc hằng ngày.</p><h3>Thiết lập nhanh</h3><p>Bật chế độ tiết kiệm pin, giảm độ sáng màn hình và tắt các ứng dụng không cần thiết khi làm việc di động.</p><h3>Thói quen sạc</h3><p>Không cần luôn giữ pin ở 100%; hãy để macOS quản lý sạc tối ưu nếu máy hỗ trợ.</p>',
                'featured_image_path' => 'assets/news/macbook-battery-tips.svg',
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now()->subDays(7),
                'views_count' => 1420,
                'meta_title' => 'Mẹo tối ưu pin MacBook',
                'meta_description' => 'Cách kéo dài thời lượng pin MacBook khi làm việc di động',
                'meta' => json_encode(['tags' => ['macbook', 'pin', 'mẹo sử dụng'], 'author' => 'Admin', 'reading_time' => '4 phút']),
            ],
            [
                'blog_category_id' => 2,
                'title' => 'MSI MAG 255PXF phù hợp với game thủ nào?',
                'slug' => 'msi-mag-255pxf-phu-hop-voi-game-thu-nao',
                'excerpt' => 'Phân tích nhanh nhóm người dùng phù hợp với màn hình MSI MAG 255PXF khi chơi FPS, MOBA và làm việc đa nhiệm.',
                'content' => '<h2>MSI MAG 255PXF cho game thủ</h2><p>Dòng màn hình này hướng tới người chơi cần tốc độ phản hồi nhanh và khung hình ổn định.</p><h3>Điểm đáng chú ý</h3><p>Kích thước vừa phải giúp dễ bao quát toàn màn hình, phù hợp các tựa game FPS và MOBA.</p>',
                'featured_image_path' => 'assets/news/anker-735-compare.svg',
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(9),
                'views_count' => 640,
                'meta_title' => 'MSI MAG 255PXF phù hợp với game thủ nào',
                'meta_description' => 'Gợi ý chọn MSI MAG 255PXF cho nhu cầu gaming',
                'meta' => json_encode(['tags' => ['msi', 'màn hình', 'gaming'], 'author' => 'Editor', 'reading_time' => '3 phút']),
            ],
            [
                'blog_category_id' => 3,
                'title' => 'Kinh nghiệm chọn laptop gaming theo ngân sách',
                'slug' => 'kinh-nghiem-chon-laptop-gaming-theo-ngan-sach',
                'excerpt' => 'Cách ưu tiên CPU, GPU, RAM, màn hình và tản nhiệt khi chọn laptop gaming trong từng mức ngân sách.',
                'content' => '<h2>Chọn laptop gaming theo ngân sách</h2><p>Không nên chỉ nhìn GPU; tản nhiệt, màn hình và khả năng nâng cấp cũng ảnh hưởng nhiều đến trải nghiệm dài hạn.</p><h3>Ưu tiên cấu hình</h3><p>Với game nặng, GPU và tản nhiệt nên được ưu tiên. Với học tập kèm giải trí, RAM và SSD dễ nâng cấp là lợi thế.</p>',
                'featured_image_path' => 'assets/news/anker-735-review-1-year.svg',
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'views_count' => 1110,
                'meta_title' => 'Kinh nghiệm chọn laptop gaming',
                'meta_description' => 'Hướng dẫn chọn laptop gaming theo ngân sách',
                'meta' => json_encode(['tags' => ['laptop gaming', 'tư vấn mua hàng'], 'author' => 'Admin', 'reading_time' => '5 phút']),
            ],
            [
                'blog_category_id' => 3,
                'title' => 'Chọn chuột và bàn phím cho góc làm việc gọn gàng',
                'slug' => 'chon-chuot-va-ban-phim-cho-goc-lam-viec-gon-gang',
                'excerpt' => 'Một vài tiêu chí chọn chuột, bàn phím và phụ kiện giúp góc làm việc gọn, ít dây và thao tác thoải mái hơn.',
                'content' => '<h2>Chọn phụ kiện cho góc làm việc</h2><p>Chuột và bàn phím phù hợp giúp giảm mỏi tay, tăng tốc thao tác và giữ bàn làm việc gọn hơn.</p><h3>Kết nối</h3><p>Nếu ưu tiên gọn, hãy chọn phụ kiện không dây có pin tốt. Nếu ưu tiên độ trễ thấp, phụ kiện có dây vẫn là lựa chọn chắc chắn.</p>',
                'featured_image_path' => 'assets/news/macbook-battery-tips.svg',
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(11),
                'views_count' => 590,
                'meta_title' => 'Chọn chuột và bàn phím cho góc làm việc',
                'meta_description' => 'Gợi ý chọn phụ kiện cho góc làm việc gọn gàng',
                'meta' => json_encode(['tags' => ['chuột', 'bàn phím', 'phụ kiện'], 'author' => 'Editor', 'reading_time' => '4 phút']),
            ],
        ];

        $categorySlugsBySeedId = [
            1 => 'tin-tuc-cong-nghe',
            2 => 'danh-gia-san-pham',
            3 => 'huong-dan-su-dung',
            4 => 'khuyen-mai',
        ];

        foreach ($posts as $post) {
            $categoryReference = $post['blog_category_id'] ?? null;
            $categorySlug = is_int($categoryReference)
                ? ($categorySlugsBySeedId[$categoryReference] ?? null)
                : $categoryReference;

            unset($post['created_at'], $post['updated_at']);

            $post['blog_category_id'] = $categorySlug !== null
                ? DB::table('blog_categories')->where('slug', $categorySlug)->value('id')
                : null;

            $exists = DB::table('blog_posts')->where('slug', $post['slug'])->exists();
            $post['updated_at'] = $now;
            $post['deleted_at'] = null;

            if (! $exists) {
                $post['created_at'] = $now;
            }

            DB::table('blog_posts')->updateOrInsert(
                ['slug' => $post['slug']],
                $post
            );
        }
    }
}
