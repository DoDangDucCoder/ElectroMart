<?php

namespace Tests\Feature;

use App\Application\Shared\Services\MonitoringLoggingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MonitoringLoggingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_collect_metrics_survives_missing_metrics_and_alert_tables_when_thresholds_fire(): void
    {
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('metrics');

        $service = $this->freshMonitoringService();
        $this->setServiceConfig($service, function (array $config) {
            $config['monitoring']['alert_thresholds']['memory_usage_percent'] = -1;
            $config['monitoring']['alert_thresholds']['response_time_ms'] = -1;

            return $config;
        });

        $metrics = $service->collectMetrics();

        $this->assertArrayHasKey('system', $metrics);
        $this->assertArrayHasKey('application', $metrics);
        $this->assertArrayHasKey('database', $metrics);
        $this->assertArrayHasKey('uptime_seconds', $metrics['system']);
        $this->assertArrayHasKey('cpu_usage_percent', $metrics['system']);
    }

    public function test_health_status_counts_unknown_checks_as_degraded_instead_of_healthy(): void
    {
        $service = $this->freshMonitoringService();
        $this->setServiceConfig($service, function (array $config) {
            $config['health_checks'] = [
                'database' => true,
                'custom_probe' => true,
            ];

            return $config;
        });

        $status = $service->getHealthStatus();

        $this->assertSame('unknown', $status['checks']['custom_probe']['status']);
        $this->assertSame('degraded', $status['overall_status']);
    }

    public function test_health_status_skips_redis_probe_when_application_is_not_using_redis(): void
    {
        config([
            'cache.default' => 'array',
            'queue.default' => 'sync',
            'session.driver' => 'file',
            'broadcasting.default' => 'log',
        ]);

        $service = $this->freshMonitoringService();
        $status = $service->getHealthStatus();

        $this->assertSame('healthy', $status['checks']['redis']['status']);
        $this->assertSame('Redis is not in active use', $status['checks']['redis']['message']);
    }

    public function test_non_positive_thresholds_do_not_emit_metric_alerts(): void
    {
        Log::spy();

        $service = $this->freshMonitoringService();
        $this->setServiceConfig($service, function (array $config) {
            $config['monitoring']['alert_thresholds']['memory_usage_percent'] = 0;
            $config['monitoring']['alert_thresholds']['response_time_ms'] = -1;

            return $config;
        });

        $service->collectMetrics();

        Log::shouldNotHaveReceived('critical', function (string $message) {
            return $message === 'Alert: Metric threshold exceeded';
        });
    }

    private function freshMonitoringService(): MonitoringLoggingService
    {
        $this->app->forgetInstance(MonitoringLoggingService::class);

        return $this->app->make(MonitoringLoggingService::class);
    }

    private function setServiceConfig(MonitoringLoggingService $service, callable $mutator): void
    {
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);

        $config = $property->getValue($service);
        $property->setValue($service, $mutator($config));
    }
}
