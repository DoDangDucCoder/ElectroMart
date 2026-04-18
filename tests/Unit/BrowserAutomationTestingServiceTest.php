<?php

namespace Tests\Unit;

use App\Application\Shared\Services\BrowserAutomationTestingService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BrowserAutomationTestingServiceTest extends TestCase
{
    public function test_navigation_link_skips_external_and_non_http_targets(): void
    {
        config(['app.url' => 'https://shop.test']);

        Http::fake([
            'https://shop.test/catalog' => Http::response('ok', 200),
        ]);

        $service = app(BrowserAutomationTestingService::class);
        $method = new \ReflectionMethod($service, 'testNavigationLink');
        $method->setAccessible(true);

        $internal = $method->invoke($service, ['url' => '/catalog', 'text' => 'Catalog']);
        $external = $method->invoke($service, ['url' => 'https://example.com', 'text' => 'External']);
        $mailto = $method->invoke($service, ['url' => 'mailto:test@example.com', 'text' => 'Mail']);
        $anchor = $method->invoke($service, ['url' => '#top', 'text' => 'Top']);

        $this->assertTrue($internal['success']);
        $this->assertSame(200, $internal['status']);
        $this->assertTrue($external['success']);
        $this->assertTrue($external['skipped']);
        $this->assertTrue($mailto['skipped']);
        $this->assertTrue($anchor['skipped']);

        Http::assertSentCount(1);
    }

    public function test_browser_automation_report_counts_nested_success_results(): void
    {
        $service = app(BrowserAutomationTestingService::class);

        $property = new \ReflectionProperty($service, 'testResults');
        $property->setAccessible(true);
        $property->setValue($service, [
            'navigation' => [
                'header_links' => [
                    'home' => ['success' => true, 'message' => 'ok'],
                    'shop' => ['success' => false, 'message' => 'broken'],
                ],
            ],
        ]);

        $report = $service->generateTestReport();

        $this->assertSame(2, $report['summary']['total_tests']);
        $this->assertSame(1, $report['summary']['passed_tests']);
        $this->assertSame(1, $report['summary']['failed_tests']);
    }
}
