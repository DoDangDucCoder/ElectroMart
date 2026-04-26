<?php

namespace Tests\Feature;

use App\Application\Shared\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class HealthAndDocsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_degrades_gracefully_when_cache_layer_throws(): void
    {
        $this->app->instance(CacheService::class, new class extends CacheService
        {
            public function remember(string $key, callable $callback, int $ttl = 3600): mixed
            {
                throw new RuntimeException('cache unavailable');
            }
        });

        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonPath('status', 'degraded')
            ->assertJsonPath('cache', 'error');
    }

    public function test_health_cache_endpoint_returns_safe_json_when_cache_stats_fail(): void
    {
        $this->app->instance(CacheService::class, new class extends CacheService
        {
            public function getCacheStats(): array
            {
                throw new RuntimeException('cache stats unavailable');
            }
        });

        $this->getJson('/api/v1/health/cache')
            ->assertOk()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Thống kê cache hiện tạm thời chưa sẵn sàng.')
            ->assertJsonPath('data', []);
    }

    public function test_health_performance_endpoint_returns_runtime_metrics(): void
    {
        $this->getJson('/api/v1/health/performance')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'memory_usage',
                    'memory_peak',
                    'execution_time',
                    'php_version',
                    'laravel_version',
                ],
            ]);
    }

    public function test_api_docs_expose_current_product_and_health_endpoints(): void
    {
        $response = $this->getJson('/api/v1/docs')
            ->assertOk()
            ->assertJsonPath('title', 'ShopW API');

        $documentation = $response->json();
        $products = array_keys($documentation['endpoints']['products']);
        $health = array_keys($documentation['endpoints']['health']);
        $settings = array_keys($documentation['endpoints']['settings']);

        $this->assertContains('GET /api/v1/products/category/{categorySlug}', $products);
        $this->assertContains('GET /api/v1/products/brand/{brandSlug}', $products);
        $this->assertSame(
            'mảng ProductResource gợi ý',
            $documentation['endpoints']['products']['GET /api/v1/products/{slug}']['response']['recommended_products']
        );
        $this->assertContains('GET /api/v1/health/cache', $health);
        $this->assertContains('GET /api/v1/health/performance', $health);
        $this->assertContains('GET /api/v1/settings/contact', $settings);
        $this->assertContains('GET /api/v1/settings/seo', $settings);
    }

    public function test_l5_swagger_docs_can_be_generated_and_served(): void
    {
        $this->artisan('l5-swagger:generate')
            ->assertExitCode(0);

        $this->getJson('/docs')
            ->assertOk()
            ->assertJsonPath('info.title', 'ShopW API')
            ->assertJsonPath('paths./api/v1/health.get.summary', 'Application health check');

        $this->get('/api/documentation')
            ->assertOk()
            ->assertSee('SwaggerUIBundle');
    }
}
