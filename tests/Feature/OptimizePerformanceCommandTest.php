<?php

namespace Tests\Feature;

use App\Application\Shared\Services\PerformanceTestingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OptimizePerformanceCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_performance_optimize_command_options_complete_without_runtime_errors(): void
    {
        $this->artisan('performance:optimize --db-optimize')->assertExitCode(0);
        $this->artisan('performance:optimize --cache-stats')->assertExitCode(0);
        $this->artisan('performance:optimize --eager-loading')->assertExitCode(0);
        $this->artisan('performance:optimize --query-analyze')->assertExitCode(0);
        $this->artisan('performance:optimize --query-monitor --query-monitor-seconds=0')->assertExitCode(0);
    }

    public function test_performance_test_command_reports_skipped_database_sections_cleanly(): void
    {
        $service = Mockery::mock(PerformanceTestingService::class);
        $service->shouldReceive('runComprehensiveTests')
            ->once()
            ->andReturn([
                'database_performance' => [
                    'status' => 'skipped',
                    'reason' => 'Database unavailable',
                ],
                'cache_performance' => [
                    'status' => 'success',
                    'total_time' => 0.02,
                ],
            ]);
        $service->shouldReceive('getPerformanceMetrics')
            ->once()
            ->andReturn([
                'overall_score' => 80,
                'recommendations' => [],
                'bottlenecks' => [],
                'improvements' => [],
            ]);

        $this->app->instance(PerformanceTestingService::class, $service);

        $this->artisan('performance:optimize --performance-test')
            ->expectsOutputToContain('database_performance: Skipped (Database unavailable)')
            ->expectsOutputToContain('cache_performance: 0.02s')
            ->assertExitCode(0);
    }
}
