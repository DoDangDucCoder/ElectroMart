<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Cms\BlogCategory;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_show_only_returns_published_posts_and_includes_related_posts(): void
    {
        $category = BlogCategory::create([
            'name' => 'Tin tức',
            'slug' => 'tin-tuc',
            'description' => 'Tin tức công nghệ',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $published = BlogPost::create([
            'blog_category_id' => $category->id,
            'title' => 'OPPO Sale',
            'slug' => 'oppo-sale',
            'excerpt' => 'Bài viết published',
            'content' => 'Nội dung published',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'views_count' => 0,
            'is_featured' => true,
        ]);

        $related = BlogPost::create([
            'blog_category_id' => $category->id,
            'title' => 'OPPO Tips',
            'slug' => 'oppo-tips',
            'excerpt' => 'Bài viết liên quan',
            'content' => 'Nội dung liên quan',
            'status' => 'published',
            'published_at' => now()->subMinutes(30),
            'views_count' => 0,
            'is_featured' => false,
        ]);

        BlogPost::create([
            'blog_category_id' => $category->id,
            'title' => 'Bản nháp nội bộ',
            'slug' => 'ban-nhap-noi-bo',
            'excerpt' => 'Draft',
            'content' => 'Draft',
            'status' => 'draft',
            'published_at' => null,
            'views_count' => 0,
            'is_featured' => false,
        ]);

        $this->getJson('/api/v1/blog/' . $published->slug)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.slug', $published->slug)
            ->assertJsonFragment(['slug' => $related->slug]);

        $this->getJson('/api/v1/blog/ban-nhap-noi-bo')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }

    public function test_blog_search_trims_keyword_and_rejects_empty_input(): void
    {
        $category = BlogCategory::create([
            'name' => 'Đánh giá',
            'slug' => 'danh-gia',
            'description' => 'Đánh giá thiết bị',
            'is_active' => true,
            'display_order' => 0,
        ]);

        BlogPost::create([
            'blog_category_id' => $category->id,
            'title' => 'iPhone 17 review',
            'slug' => 'iphone-17-review',
            'excerpt' => 'Review iPhone',
            'content' => 'Nội dung review',
            'status' => 'published',
            'published_at' => now(),
            'views_count' => 0,
            'is_featured' => false,
        ]);

        $this->getJson('/api/v1/blog/search?q=' . urlencode('   iPhone 17   '))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 1)
            ->assertJsonPath('keyword', 'iPhone 17');

        $this->getJson('/api/v1/blog/search?q=' . urlencode('   '))
            ->assertStatus(400)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Search keyword is required');
    }
}
