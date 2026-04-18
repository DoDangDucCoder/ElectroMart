<?php

namespace Tests\Unit;

use App\Application\Shared\Services\PerformanceTestingService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceTestingServiceTest extends TestCase
{
    public function test_performance_report_survives_partial_query_metrics_without_fallback(): void
    {
        $service = app(PerformanceTestingService::class);

        $resultsProperty = new \ReflectionProperty($service, 'testResults');
        $resultsProperty->setAccessible(true);
        $resultsProperty->setValue($service, [
            'database_performance' => [
                'status' => 'success',
                'total_time' => 0.2,
                'complex_join' => 0.05,
            ],
            'cache_performance' => [
                'status' => 'success',
                'total_time' => 0.03,
                'cache_set' => 0.001,
            ],
            'query_performance' => [
                'status' => 'error',
                'n_plus_one_query' => 0.4,
                'error' => 'JOIN failed before optimized query finished',
            ],
        ]);

        $method = new \ReflectionMethod($service, 'generatePerformanceReport');
        $method->setAccessible(true);
        $method->invoke($service);

        $metrics = $service->getPerformanceMetrics();

        $this->assertArrayHasKey('overall_score', $metrics);
        $this->assertArrayHasKey('recommendations', $metrics);
        $this->assertArrayHasKey('bottlenecks', $metrics);
        $this->assertNotContains('Failed to generate report', $metrics['recommendations']);
        $this->assertSame(90, $metrics['overall_score']);
    }

    public function test_comprehensive_tests_skip_database_bound_checks_when_database_is_unavailable(): void
    {
        $service = app(PerformanceTestingService::class);

        DB::shouldReceive('connection')
            ->once()
            ->andReturn(new class
            {
                public function getPdo(): never
                {
                    throw new \RuntimeException('Database offline');
                }
            });

        $results = $service->runComprehensiveTests();

        $this->assertSame('skipped', $results['database_performance']['status']);
        $this->assertSame('skipped', $results['query_performance']['status']);
        $this->assertSame('skipped', $results['memory_usage']['status']);
        $this->assertSame('skipped', $results['response_times']['status']);
        $this->assertSame('Database unavailable', $results['database_performance']['reason']);
        $this->assertArrayHasKey('overall_score', $service->getPerformanceMetrics());
    }
}
