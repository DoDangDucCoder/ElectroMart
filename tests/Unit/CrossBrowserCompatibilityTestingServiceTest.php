<?php

namespace Tests\Unit;

use App\Application\Shared\Services\CrossBrowserCompatibilityTestingService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CrossBrowserCompatibilityTestingServiceTest extends TestCase
{
    public function test_cross_browser_page_test_normalizes_storefront_routes(): void
    {
        config(['app.url' => 'https://shop.test']);

        Http::fake([
            'https://shop.test/lien-he' => Http::response('<html><body><header></header></body></html>', 200),
        ]);

        $service = new CrossBrowserCompatibilityTestingService();
        $method = new \ReflectionMethod($service, 'testPageInBrowser');
        $method->setAccessible(true);

        $result = $method->invoke($service, '/contact', 'Contact Page', [
            'user_agent' => 'UnitTest',
            'features' => ['html5', 'css3'],
        ]);

        $this->assertTrue($result['success']);
        $this->assertSame('https://shop.test/lien-he', $result['url']);
        Http::assertSent(fn ($request) => (string) $request->url() === 'https://shop.test/lien-he');
    }

    public function test_cross_browser_report_counts_nested_success_results(): void
    {
        $service = new CrossBrowserCompatibilityTestingService();

        $property = new \ReflectionProperty($service, 'testResults');
        $property->setAccessible(true);
        $property->setValue($service, [
            'browser_compatibility' => [
                'chrome' => [
                    'home_page' => ['success' => true, 'compatibility_score' => 100],
                ],
                'firefox' => [
                    'home_page' => ['success' => false, 'compatibility_score' => 60],
                ],
            ],
        ]);

        $report = $service->generateTestReport();

        $this->assertSame(2, $report['summary']['total_tests']);
        $this->assertSame(1, $report['summary']['passed_tests']);
        $this->assertSame(1, $report['summary']['failed_tests']);
    }
}
