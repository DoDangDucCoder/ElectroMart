<?php

namespace Tests\Feature;

use App\Application\Shared\Services\CacheService;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    public function test_cache_service_tags_falls_back_on_non_taggable_store(): void
    {
        config(['cache.default' => 'file']);

        $service = app(CacheService::class);

        Cache::put('probe', 'value', 60);

        $repository = $service->tags(['products']);

        $this->assertSame('value', Cache::get('probe'));
        $this->assertNotInstanceOf(TaggedCache::class, $repository);
    }
}
