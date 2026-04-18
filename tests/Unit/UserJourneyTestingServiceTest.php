<?php

namespace Tests\Unit;

use App\Application\Shared\Services\UserJourneyTestingService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserJourneyTestingServiceTest extends TestCase
{
    public function test_protected_journey_step_accepts_expected_auth_redirect(): void
    {
        config(['app.url' => 'https://shop.test']);

        Http::fake([
            'https://shop.test/history' => Http::response('', 302, [
                'Location' => 'https://shop.test/dang-nhap',
            ]),
        ]);

        $service = new UserJourneyTestingService();
        $method = new \ReflectionMethod($service, 'testJourneyStep');
        $method->setAccessible(true);

        $result = $method->invoke($service, 'order_history', [
            'url' => '/orders',
            'expected_elements' => ['order_list'],
            'actions' => ['view_order'],
        ]);

        $this->assertTrue($result['success']);
        $this->assertSame('/history', $result['resolved_path']);
        $this->assertSame('/dang-nhap', $result['auth_redirect']);
        Http::assertSentCount(1);
    }

    public function test_user_journey_report_counts_nested_success_results(): void
    {
        $service = new UserJourneyTestingService();

        $property = new \ReflectionProperty($service, 'testResults');
        $property->setAccessible(true);
        $property->setValue($service, [
            'guest_user_journey' => [
                'complete_journey' => [
                    'success' => true,
                    'steps' => [
                        ['success' => true, 'message' => 'step 1'],
                        ['success' => false, 'message' => 'step 2'],
                    ],
                ],
            ],
        ]);

        $report = $service->generateTestReport();

        $this->assertSame(3, $report['summary']['total_tests']);
        $this->assertSame(2, $report['summary']['passed_tests']);
        $this->assertSame(1, $report['summary']['failed_tests']);
    }
}
