<?php

namespace Tests\Feature;

use App\Application\Shared\Services\BrowserAutomationTestingService;
use App\Application\Shared\Services\CrossBrowserCompatibilityTestingService;
use App\Application\Shared\Services\UserJourneyTestingService;
use Tests\TestCase;

class EndToEndTestingCommandTest extends TestCase
{
    public function test_e2e_report_handles_failed_category_without_summary(): void
    {
        $browserService = \Mockery::mock(BrowserAutomationTestingService::class);
        $browserService->shouldReceive('runBrowserTests')->once()->andThrow(new \RuntimeException('browser down'));

        $journeyService = \Mockery::mock(UserJourneyTestingService::class);
        $journeyService->shouldReceive('runUserJourneyTests')->once()->andReturn([
            'checkout' => ['success' => true, 'message' => 'ok'],
        ]);
        $journeyService->shouldReceive('generateTestReport')->once()->andReturn([
            'summary' => [
                'total_tests' => 1,
                'passed_tests' => 1,
                'failed_tests' => 0,
                'success_rate' => 100,
            ],
            'recommendations' => [],
        ]);

        $crossBrowserService = \Mockery::mock(CrossBrowserCompatibilityTestingService::class);

        $this->app->instance(BrowserAutomationTestingService::class, $browserService);
        $this->app->instance(UserJourneyTestingService::class, $journeyService);
        $this->app->instance(CrossBrowserCompatibilityTestingService::class, $crossBrowserService);

        $this->artisan('e2e:test --browser --journey --report')
            ->assertExitCode(0);
    }

    public function test_e2e_report_summary_keeps_failed_categories_in_overall_summary(): void
    {
        $before = glob(storage_path('app/reports/e2e_test_report_*.json')) ?: [];

        $browserService = \Mockery::mock(BrowserAutomationTestingService::class);
        $browserService->shouldReceive('runBrowserTests')->once()->andThrow(new \RuntimeException('browser down'));

        $journeyService = \Mockery::mock(UserJourneyTestingService::class);
        $journeyService->shouldReceive('runUserJourneyTests')->once()->andReturn([
            'checkout_journey' => [
                'step_1' => ['success' => true, 'message' => 'ok'],
            ],
        ]);
        $journeyService->shouldReceive('generateTestReport')->once()->andReturn([
            'summary' => [
                'total_tests' => 1,
                'passed_tests' => 1,
                'failed_tests' => 0,
                'success_rate' => 100,
            ],
            'recommendations' => [],
        ]);

        $crossBrowserService = \Mockery::mock(CrossBrowserCompatibilityTestingService::class);

        $this->app->instance(BrowserAutomationTestingService::class, $browserService);
        $this->app->instance(UserJourneyTestingService::class, $journeyService);
        $this->app->instance(CrossBrowserCompatibilityTestingService::class, $crossBrowserService);

        $this->artisan('e2e:test --browser --journey --report')
            ->assertExitCode(0);

        $after = glob(storage_path('app/reports/e2e_test_report_*.json')) ?: [];
        $reportPath = $this->latestGeneratedArtifact($after, $before);

        $this->assertNotNull($reportPath);

        $report = json_decode((string) file_get_contents($reportPath), true);

        $this->assertSame(2, $report['summary']['total_categories']);
        $this->assertSame(1, $report['summary']['successful_categories']);
        $this->assertSame(1, $report['summary']['failed_categories']);
    }

    public function test_e2e_csv_export_flattens_nested_results(): void
    {
        $before = glob(storage_path('app/exports/e2e_test_results_*.csv')) ?: [];

        $browserService = \Mockery::mock(BrowserAutomationTestingService::class);
        $browserService->shouldReceive('runBrowserTests')->once()->andReturn([
            'navigation' => [
                'header_links' => [
                    'home' => ['success' => true, 'message' => 'ok'],
                ],
            ],
        ]);
        $browserService->shouldReceive('generateTestReport')->once()->andReturn([
            'summary' => [
                'total_tests' => 1,
                'passed_tests' => 1,
                'failed_tests' => 0,
                'success_rate' => 100,
            ],
            'recommendations' => [],
        ]);

        $journeyService = \Mockery::mock(UserJourneyTestingService::class);
        $crossBrowserService = \Mockery::mock(CrossBrowserCompatibilityTestingService::class);

        $this->app->instance(BrowserAutomationTestingService::class, $browserService);
        $this->app->instance(UserJourneyTestingService::class, $journeyService);
        $this->app->instance(CrossBrowserCompatibilityTestingService::class, $crossBrowserService);

        $this->artisan('e2e:test --browser --export=csv')
            ->assertExitCode(0);

        $after = glob(storage_path('app/exports/e2e_test_results_*.csv')) ?: [];
        $csvPath = $this->latestGeneratedArtifact($after, $before);

        $this->assertNotNull($csvPath);

        $csv = (string) file_get_contents($csvPath);

        $this->assertStringContainsString('browser_automation,"navigation > header_links > home",PASS,ok', $csv);
    }

    protected function tearDown(): void
    {
        \Mockery::close();

        parent::tearDown();
    }

    private function latestGeneratedArtifact(array $after, array $before): ?string
    {
        $newFiles = array_values(array_diff($after, $before));

        if (! empty($newFiles)) {
            usort($newFiles, fn (string $a, string $b) => filemtime($a) <=> filemtime($b));

            return end($newFiles) ?: null;
        }

        if (empty($after)) {
            return null;
        }

        usort($after, fn (string $a, string $b) => filemtime($a) <=> filemtime($b));

        return end($after) ?: null;
    }
}
