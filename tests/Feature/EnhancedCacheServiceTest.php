<?php

namespace Tests\Feature;

use App\Application\Shared\Services\EnhancedCacheService;
use Illuminate\Cache\TaggedCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EnhancedCacheServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_enhanced_cache_service_falls_back_when_cache_store_does_not_support_tags(): void
    {
        config(['cache.default' => 'database']);

        $service = app(EnhancedCacheService::class);

        Cache::put('probe', 'value', 60);
        $service->getLatestProducts(6);

        $repository = $service->tags(['products']);

        $this->assertSame('value', Cache::get('probe'));
        $this->assertNotInstanceOf(TaggedCache::class, $repository);

        $service->invalidateProductCache(99);

        $this->assertSame('value', Cache::get('probe'));
        $this->assertNull(Cache::get('products_latest_6'));
    }
}
