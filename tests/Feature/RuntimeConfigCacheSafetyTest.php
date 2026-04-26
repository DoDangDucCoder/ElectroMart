<?php

namespace Tests\Feature;

use App\Http\Middleware\HTTPSRedirectMiddleware;
use App\Http\Middleware\IPWhitelistMiddleware;
use Illuminate\Http\Request;
use Tests\TestCase;

class RuntimeConfigCacheSafetyTest extends TestCase
{
    public function test_ip_whitelist_middleware_uses_cached_config_values(): void
    {
        config([
            'security.ip_whitelist.enabled' => true,
            'security.ip_whitelist.allowed' => ['203.0.113.10'],
        ]);

        $request = Request::create('/admin', 'GET', [], [], [], [
            'REMOTE_ADDR' => '198.51.100.25',
        ]);

        $response = (new IPWhitelistMiddleware())->handle(
            $request,
            fn () => response('ok')
        );

        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_https_redirect_middleware_uses_cached_config_values(): void
    {
        config(['security.force_https' => true]);

        $request = Request::create('http://shop.test/admin?tab=orders', 'GET');

        $response = (new HTTPSRedirectMiddleware())->handle(
            $request,
            fn () => response('ok')
        );

        $this->assertSame(301, $response->getStatusCode());
        $this->assertStringStartsWith('https://', $response->headers->get('Location'));
    }

    public function test_social_oauth_redirects_are_available_from_services_config(): void
    {
        $this->assertNotEmpty(config('services.google.redirect'));
        $this->assertNotEmpty(config('services.facebook.redirect'));
        $this->assertStringEndsWith('/customer/google/callback', config('services.google.redirect'));
        $this->assertStringEndsWith('/customer/facebook/callback', config('services.facebook.redirect'));
    }
}
