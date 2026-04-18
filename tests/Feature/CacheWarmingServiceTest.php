<?php

namespace Tests\Feature;

use App\Application\Shared\Services\CacheWarmingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CacheWarmingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_popular_cache_warming_falls_back_when_products_schema_has_no_views_count(): void
    {
        config(['cache.default' => 'array']);
        Cache::flush();

        Schema::table('products', function ($table) {
            $table->dropColumn('views_count');
        });

        DB::table('products')->insert([
            'name' => 'Legacy Popular Product',
            'slug' => 'legacy-popular-product',
            'price' => 199000,
            'is_active' => true,
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        app(CacheWarmingService::class)->warmUpStrategy('popular');

        $this->assertTrue(Cache::store('file')->has('product_slug_legacy-popular-product'));
        $this->assertSame(
            'legacy-popular-product',
            Cache::store('file')->get('product_slug_legacy-popular-product')->slug
        );
    }

    public function test_popular_cache_warming_uses_published_blog_posts_schema(): void
    {
        config(['cache.default' => 'array']);
        Cache::flush();

        DB::table('blog_posts')->insert([
            [
                'title' => 'Published cacheable post',
                'slug' => 'published-cacheable-post',
                'status' => 'published',
                'created_at' => now()->subMinute(),
                'updated_at' => now()->subMinute(),
                'published_at' => now()->subMinute(),
            ],
            [
                'title' => 'Draft should stay out',
                'slug' => 'draft-should-stay-out',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => null,
            ],
        ]);

        app(CacheWarmingService::class)->warmUpStrategy('popular');

        $this->assertTrue(Cache::has('blog_post_published-cacheable-post'));
        $this->assertFalse(Cache::has('blog_post_draft-should-stay-out'));
        $this->assertSame(
            'Published cacheable post',
            Cache::get('blog_post_published-cacheable-post')->title
        );
    }

    public function test_popular_cache_warming_handles_legacy_blog_posts_without_status_or_timestamp_columns(): void
    {
        config(['cache.default' => 'array']);
        Cache::flush();

        Schema::table('blog_posts', function ($table) {
            $table->dropIndex('blog_posts_status_published_at_index');
        });

        Schema::table('blog_posts', function ($table) {
            $table->dropColumn(['status', 'published_at', 'created_at', 'updated_at']);
        });

        DB::table('blog_posts')->insert([
            [
                'title' => 'Legacy cacheable post',
                'slug' => 'legacy-cacheable-post',
            ],
            [
                'title' => 'Another legacy cacheable post',
                'slug' => 'another-legacy-cacheable-post',
            ],
        ]);

        app(CacheWarmingService::class)->warmUpStrategy('popular');

        $this->assertTrue(Cache::has('blog_post_legacy-cacheable-post'));
        $this->assertTrue(Cache::has('blog_post_another-legacy-cacheable-post'));
        $this->assertSame(
            'Legacy cacheable post',
            Cache::get('blog_post_legacy-cacheable-post')->title
        );
    }
}
