<?php

namespace Tests\Unit;

use App\Application\Shared\Services\APISecurityService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class APISecurityServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget('api_security_runtime_keys');
    }

    public function test_validate_cors_uses_configured_allowed_origins_instead_of_hardcoded_placeholders(): void
    {
        Config::set('security.api.allowed_origins', ['https://shop.test']);

        $service = app(APISecurityService::class);

        $this->assertTrue($service->validateCORS('https://shop.test'));
        $this->assertFalse($service->validateCORS('https://yourdomain.com'));
    }

    public function test_generated_api_keys_are_persisted_in_runtime_store_and_can_be_revoked(): void
    {
        Config::set('security.api.keys', []);

        $service = app(APISecurityService::class);
        $apiKey = $service->generateAPIKey('Runtime Key', 1, ['read']);

        $this->assertTrue($service->validateAPIKey($apiKey)['valid']);
        $this->assertTrue($service->revokeAPIKey($apiKey));
        $this->assertFalse($service->validateAPIKey($apiKey)['valid']);
    }

    public function test_validate_nonce_rejects_blank_values(): void
    {
        $service = app(APISecurityService::class);

        $this->assertFalse($service->validateNonce(''));
        $this->assertFalse($service->validateNonce('   '));
    }

    public function test_security_headers_report_remaining_rate_limit_for_current_api_key(): void
    {
        $service = app(APISecurityService::class);
        $apiKey = 'rate-limit-key';

        RateLimiter::clear("api_rate_limit_minute_{$apiKey}");
        RateLimiter::clear("api_rate_limit_hour_{$apiKey}");
        $this->app['request']->headers->set('X-API-Key', $apiKey);

        $service->checkRateLimit($apiKey);

        $headers = $service->createSecurityHeaders();

        $this->assertSame(59, $headers['X-Rate-Limit-Remaining']);
    }
}
