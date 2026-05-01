<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $apple = Brand::create([
            'name' => 'Apple',
            'slug' => 'apple',
            'description' => 'Apple brand for homepage test',
            'is_active' => true,
            'display_order' => 1,
            'meta' => null,
        ]);

        $samsung = Brand::create([
            'name' => 'Samsung',
            'slug' => 'samsung',
            'description' => 'Samsung brand for homepage test',
            'is_active' => true,
            'display_order' => 2,
            'meta' => null,
        ]);

        Product::create([
            'name' => 'MacBook Air M3 13 inch',
            'slug' => 'macbook-air-m3-13-inch',
            'brand_id' => $apple->id,
            'price' => 32990000,
            'sale_price' => 29990000,
            'cost_price' => 25000000,
            'stock_quantity' => 12,
            'is_active' => true,
            'is_featured' => true,
            'is_hot' => true,
            'display_order' => 1,
            'thumbnail_path' => null,
            'description' => 'MacBook Air M3 homepage test',
            'content' => 'MacBook Air M3 homepage test',
            'views_count' => 120,
            'meta' => null,
        ]);

        Product::create([
            'name' => 'Samsung Galaxy S24 Ultra',
            'slug' => 'samsung-galaxy-s24-ultra',
            'brand_id' => $samsung->id,
            'price' => 31990000,
            'sale_price' => 28990000,
            'cost_price' => 24000000,
            'stock_quantity' => 8,
            'is_active' => true,
            'is_featured' => true,
            'is_hot' => false,
            'display_order' => 2,
            'thumbnail_path' => null,
            'description' => 'Samsung Galaxy S24 homepage test',
            'content' => 'Samsung Galaxy S24 homepage test',
            'views_count' => 100,
            'meta' => null,
        ]);

        News::create([
            'title' => 'Tin công nghệ homepage',
            'slug' => 'tin-cong-nghe-homepage',
            'thumbnail_path' => null,
            'published_at' => now()->subDay(),
            'summary' => 'Tóm tắt tin tức nổi bật cho trang chủ.',
            'is_featured' => true,
            'is_active' => true,
        ]);
    }

    public function test_home_routes_render_tech_homepage_sections(): void
    {
        foreach (['/', '/trang-chu'] as $route) {
            $this->get($route)
                ->assertOk()
                ->assertSee('dangduc-logo-horizontal-width-600.svg')
                ->assertSee('Bạn cần gì?')
                ->assertSee('Iphone')
                ->assertSee('1900.0000')
                ->assertSee('Cửa hàng')
                ->assertSee('Giỏ hàng')
                ->assertSee('Đăng nhập')
                ->assertSee('data-chatbot-widget', false)
                ->assertDontSee('shopw-chatbot-toggle', false)
                ->assertSee('Trợ lý bán hàng')
                ->assertSee('data-support-restored="true"', false)
                ->assertSee('Hỗ trợ nhanh')
                ->assertSee('Chat Facebook')
                ->assertSee('Chat Zalo')
                ->assertSee('id="slider-carousel"', false)
                ->assertSee('data-hero-slide-count="5"', false)
                ->assertDontSee('class="tech-hero-card"', false)
                ->assertDontSee('TẶNG VOUCHER 500K')
                ->assertSee('SẢN PHẨM NỔI BẬT')
                ->assertSee('MacBook Air M3 13 inch')
                ->assertSee('Nổi bật')
                ->assertSee('32.990.000đ')
                ->assertSee('29.990.000đ')
                ->assertSee('-9%')
                ->assertSee('Khuyến mãi chớp nhoáng')
                ->assertSee('data-tech-wishlist', false)
                ->assertSee('tech-flash-sale-badge', false)
                ->assertSee('tech-sale-prev', false)
                ->assertSee('tech-sale-next', false)
                ->assertSee('TIN TỨC MỚI')
                ->assertSee('Tin tức & Hướng dẫn', false)
                ->assertSee('Tin công nghệ homepage')
                ->assertSee('Tóm tắt tin tức nổi bật cho trang chủ.')
                ->assertSee('Miễn phí giao hàng')
                ->assertSee('Đổi trả hàng')
                ->assertSee('Thanh toán an toàn')
                ->assertSee('Hỗ trợ tư vấn 24/7')
                ->assertSee('VỀ CHÚNG TÔI')
                ->assertSee('CHÍNH SÁCH')
                ->assertSee('HỖ TRỢ KHÁCH HÀNG')
                ->assertSee('Momo')
                ->assertSee('MB')
                ->assertSee('VNPAY');
        }
    }

    public function test_homepage_uses_fallback_data_when_catalog_is_empty(): void
    {
        Product::query()->delete();
        Brand::query()->delete();
        News::query()->delete();

        $this->get('/')
            ->assertOk()
            ->assertSee('id="slider-carousel"', false)
            ->assertSee('MacBook Air M3 13 inch 16GB/512GB')
            ->assertSee('5 mẫu laptop mỏng nhẹ đáng mua')
            ->assertSee('Apple')
            ->assertSee('Samsung')
            ->assertSee('Momo')
            ->assertSee('VNPAY');
    }
}
