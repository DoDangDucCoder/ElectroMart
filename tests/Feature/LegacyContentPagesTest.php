<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogCategory;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogPost;
use App\Infrastructure\Persistence\Eloquent\Cms\Video;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyContentPagesTest extends TestCase
{
    use RefreshDatabase;

    protected BlogCategory $blogCategory;
    protected BlogPost $blogPost;
    protected Video $video;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->createLegacyContactTable();
        $this->seedStorefrontShellData();
    }

    public function test_contact_page_renders_refreshed_support_layout(): void
    {
        $this->get('/lien-he')
            ->assertOk()
            ->assertSee('g-contact-page')
            ->assertSee('shopw-content-support-strip', false)
            ->assertSee('Liên hệ với đội ngũ cửa hàng')
            ->assertSee('Bản đồ và showroom');
    }

    public function test_blog_category_page_renders_refreshed_cards(): void
    {
        $this->get('/danh-muc-bai-viet/' . $this->blogCategory->slug)
            ->assertOk()
            ->assertSee('g-blog-list')
            ->assertSee('shopw-content-support-strip', false)
            ->assertSee('Quay lại trang chủ để tiếp tục xem sản phẩm')
            ->assertDontSee('Quay lại storefront chính')
            ->assertSee($this->blogCategory->name)
            ->assertSee($this->blogPost->title)
            ->assertSee('Đọc bài viết');
    }

    public function test_blog_detail_page_renders_refreshed_article_layout(): void
    {
        $this->get('/bai-viet/' . $this->blogPost->slug)
            ->assertOk()
            ->assertSee('g-blog-detail')
            ->assertSee('shopw-content-support-strip', false)
            ->assertSee($this->blogPost->title)
            ->assertSee('Bài viết liên quan');
    }

    public function test_video_page_and_watch_endpoint_render_refreshed_video_flow(): void
    {
        $this->get('/video-shop')
            ->assertOk()
            ->assertSee('g-video-page')
            ->assertSee('shopw-content-support-strip', false)
            ->assertSee('/danh-muc-bai-viet/' . $this->blogCategory->slug)
            ->assertDontSee('/danh-muc-bai-viet/tin-tuc')
            ->assertSee($this->video->title)
            ->assertSee('Xem video');

        $this->post('/watch-video', [
            'video_id' => $this->video->id,
        ])->assertOk()
            ->assertJsonFragment([
                'video_title' => $this->video->title,
            ]);
    }

    public function test_watch_video_returns_not_found_for_inactive_modern_video(): void
    {
        $inactiveVideo = Video::create([
            'title' => 'Video inactive storefront',
            'slug' => 'video-inactive-storefront',
            'description' => 'Không nên hiện ra ngoài storefront',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'thumbnail_path' => 'uploads/product/no-image.svg',
            'is_active' => false,
            'display_order' => 99,
        ]);

        $this->post('/watch-video', [
            'video_id' => $inactiveVideo->id,
        ])->assertNotFound()
            ->assertJsonFragment([
                'video_title' => 'Video không tồn tại',
            ]);
    }

    public function test_legacy_video_storefront_supports_tbl_video_and_hides_inactive_rows(): void
    {
        Schema::dropIfExists('videos');
        Schema::dropIfExists('tbl_videos');
        Schema::dropIfExists('tbl_video');

        Schema::create('tbl_video', function (Blueprint $table) {
            $table->increments('video_id');
            $table->string('video_title');
            $table->string('video_slug');
            $table->string('video_link');
            $table->string('video_image')->nullable();
            $table->text('video_desc')->nullable();
            $table->integer('video_status')->default(1);
        });

        DB::table('tbl_video')->insert([
            [
                'video_id' => 1,
                'video_title' => 'Legacy video active',
                'video_slug' => 'legacy-video-active',
                'video_link' => 'dQw4w9WgXcQ',
                'video_image' => null,
                'video_desc' => 'Video legacy đang hoạt động',
                'video_status' => 1,
            ],
            [
                'video_id' => 2,
                'video_title' => 'Legacy video inactive',
                'video_slug' => 'legacy-video-inactive',
                'video_link' => 'inactiveVideoId',
                'video_image' => null,
                'video_desc' => 'Video legacy đã ẩn',
                'video_status' => 0,
            ],
        ]);

        $this->get('/video-shop')
            ->assertOk()
            ->assertSee('Legacy video active')
            ->assertDontSee('Legacy video inactive');

        $this->post('/watch-video', [
            'video_id' => 1,
        ])->assertOk()
            ->assertJsonFragment([
                'video_title' => 'Legacy video active',
            ]);

        $this->post('/watch-video', [
            'video_id' => 2,
        ])->assertNotFound()
            ->assertJsonFragment([
                'video_title' => 'Video không tồn tại',
            ]);
    }

    protected function createLegacyContactTable(): void
    {
        if (! Schema::hasTable('tbl_infomation')) {
            Schema::create('tbl_infomation', function (Blueprint $table) {
                $table->increments('info_id');
                $table->text('info_contact')->nullable();
                $table->text('info_map')->nullable();
                $table->string('info_logo')->nullable();
                $table->text('info_fanpage')->nullable();
                $table->string('slogan_logo')->nullable();
            });
        }
    }

    protected function seedStorefrontShellData(): void
    {
        ProductCategory::create([
            'name' => 'Legacy Content Category',
            'slug' => 'legacy-content-category',
            'description' => 'Category for legacy content pages',
            'is_active' => true,
            'display_order' => 0,
        ]);

        Brand::create([
            'name' => 'Legacy Content Brand',
            'slug' => 'legacy-content-brand',
            'description' => 'Brand for legacy content pages',
            'is_active' => true,
            'display_order' => 0,
        ]);

        DB::table('tbl_infomation')->insert([
            'info_contact' => '<p>Hotline: 1900 5301</p><p>Email: support@example.com</p>',
            'info_map' => '<iframe src="https://maps.google.com"></iframe>',
            'info_logo' => null,
            'info_fanpage' => '<iframe src="https://facebook.com"></iframe>',
            'slogan_logo' => 'Support anytime',
        ]);

        $this->blogCategory = BlogCategory::create([
            'name' => 'Tin công nghệ',
            'slug' => 'tin-cong-nghe',
            'description' => 'Danh mục tin công nghệ mới',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $this->blogPost = BlogPost::create([
            'blog_category_id' => $this->blogCategory->id,
            'title' => 'Bài viết test frontend legacy',
            'slug' => 'bai-viet-test-frontend-legacy',
            'excerpt' => 'Tóm tắt bài viết test frontend legacy',
            'content' => '<p>Nội dung chi tiết bài viết test frontend legacy.</p>',
            'featured_image_path' => 'uploads/product/no-image.svg',
            'status' => 'published',
            'published_at' => now(),
            'views_count' => 12,
            'meta_title' => 'Bài viết test frontend legacy',
            'meta_description' => 'Mô tả meta bài viết legacy',
            'meta' => ['keywords' => 'legacy,test'],
        ]);

        BlogPost::create([
            'blog_category_id' => $this->blogCategory->id,
            'title' => 'Bài liên quan frontend legacy',
            'slug' => 'bai-lien-quan-frontend-legacy',
            'excerpt' => 'Tóm tắt bài liên quan',
            'content' => '<p>Nội dung bài liên quan.</p>',
            'featured_image_path' => 'uploads/product/no-image.svg',
            'status' => 'published',
            'published_at' => now(),
            'views_count' => 4,
            'meta_title' => 'Bài liên quan frontend legacy',
            'meta_description' => 'Meta bài liên quan',
            'meta' => ['keywords' => 'legacy,related'],
        ]);

        $this->video = Video::create([
            'title' => 'Video test frontend legacy',
            'slug' => 'video-test-frontend-legacy',
            'description' => 'Mô tả video test frontend legacy',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'thumbnail_path' => 'uploads/product/no-image.svg',
            'is_active' => true,
            'display_order' => 0,
        ]);
    }
}

