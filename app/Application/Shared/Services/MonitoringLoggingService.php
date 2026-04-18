<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MonitoringLoggingService
{
    private array $config = [];
    private array $metrics = [];

    public function __construct()
    {
        $this->initializeConfig();
    }

    private function initializeConfig(): void
    {
        $this->config = [
            'monitoring' => [
                'enabled' => config('monitoring.enabled', true),
                'metrics_retention_days' => config('monitoring.metrics_retention_days', 30),
                'alert_thresholds' => [
                    'response_time_ms' => 2000,
                    'error_rate_percent' => 5,
                    'memory_usage_percent' => 80,
                    'cpu_usage_percent' => 80,
                    'disk_usage_percent' => 85,
                ],
            ],
            'logging' => [
                'enabled' => config('logging.enabled', true),
                'log_levels' => ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'],
                'channels' => ['single', 'daily', 'slack', 'papertrail', 'syslog', 'errorlog'],
                'structured_logging' => true,
            ],
            'health_checks' => [
                'database' => true,
                'redis' => true,
                'cache' => true,
                'storage' => true,
                'queue' => true,
            ],
        ];
    }

    public function collectMetrics(): array
    {
        if (!$this->config['monitoring']['enabled']) {
            return [];
        }

        $this->metrics = [
            'system' => $this->collectSystemMetrics(),
            'application' => $this->collectApplicationMetrics(),
            'database' => $this->collectDatabaseMetrics(),
            'cache' => $this->collectCacheMetrics(),
            'queue' => $this->collectQueueMetrics(),
            'performance' => $this->collectPerformanceMetrics(),
        ];

        $this->storeMetrics();
        $this->checkAlerts();

        return $this->metrics;
    }

    private function collectSystemMetrics(): array
    {
        try {
            $memoryUsage = memory_get_usage(true);
            $memoryPeak = memory_get_peak_usage(true);
            $memoryLimit = ini_get('memory_limit');
            $loadAverage = function_exists('sys_getloadavg') ? \sys_getloadavg() : [];
            
            return [
                'memory_usage_bytes' => $memoryUsage,
                'memory_peak_bytes' => $memoryPeak,
                'memory_limit_bytes' => $this->parseMemoryLimit($memoryLimit),
                'memory_usage_percent' => $this->calculateMemoryUsagePercent($memoryUsage, $memoryLimit),
                'cpu_usage_percent' => $this->getCpuUsage(),
                'disk_usage_percent' => $this->getDiskUsage(),
                'load_average' => is_array($loadAverage) ? $loadAverage : [],
                'uptime_seconds' => $this->getUptime(),
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to collect system metrics', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function collectApplicationMetrics(): array
    {
        try {
            return [
                'requests_per_minute' => $this->getRequestsPerMinute(),
                'response_time_avg_ms' => $this->getAverageResponseTime(),
                'error_rate_percent' => $this->getErrorRate(),
                'active_users' => $this->getActiveUsers(),
                'cache_hit_rate' => $this->getCacheHitRate(),
                'queue_size' => $this->getQueueSize(),
                'database_connections' => $this->getDatabaseConnections(),
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to collect application metrics', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function collectDatabaseMetrics(): array
    {
        try {
            $driver = DB::getDriverName();

            if ($driver !== 'mysql') {
                return [
                    'driver' => $driver,
                    'timestamp' => now()->toISOString(),
                ];
            }

            $connection = DB::connection();
            $connection->getPdo();
            
            $status = $connection->select('SHOW STATUS');
            $statusArray = [];
            foreach ($status as $stat) {
                $statusArray[$stat->Variable_name] = $stat->Value;
            }

            return [
                'driver' => $driver,
                'connections_active' => $statusArray['Threads_connected'] ?? 0,
                'connections_running' => $statusArray['Threads_running'] ?? 0,
                'queries_per_second' => $statusArray['Queries'] ?? 0,
                'slow_queries' => $statusArray['Slow_queries'] ?? 0,
                'table_locks_waited' => $statusArray['Table_locks_waited'] ?? 0,
                'innodb_buffer_pool_hit_rate' => $this->calculateInnoDBHitRate($statusArray),
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to collect database metrics', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function collectCacheMetrics(): array
    {
        try {
            $cacheDriver = config('cache.default');
            
            if ($cacheDriver === 'redis') {
                $redis = Redis::connection();
                $info = $redis->info();
                
                return [
                    'driver' => $cacheDriver,
                    'memory_used_bytes' => $info['used_memory'] ?? 0,
                    'memory_peak_bytes' => $info['used_memory_peak'] ?? 0,
                    'keys_count' => $info['db0']['keys'] ?? 0,
                    'hits' => $info['keyspace_hits'] ?? 0,
                    'misses' => $info['keyspace_misses'] ?? 0,
                    'hit_rate_percent' => $this->calculateHitRate($info['keyspace_hits'] ?? 0, $info['keyspace_misses'] ?? 0),
                    'timestamp' => now()->toISOString(),
                ];
            }

            return [
                'driver' => $cacheDriver,
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to collect cache metrics', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function collectQueueMetrics(): array
    {
        try {
            $queueDriver = config('queue.default');
            
            if ($queueDriver === 'redis') {
                $redis = Redis::connection();
                
                return [
                    'driver' => $queueDriver,
                    'pending_jobs' => $redis->llen('queues:default'),
                    'failed_jobs' => $redis->llen('queues:default:failed'),
                    'processed_jobs' => $redis->get('queues:default:processed') ?? 0,
                    'timestamp' => now()->toISOString(),
                ];
            }

            return [
                'driver' => $queueDriver,
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to collect queue metrics', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function collectPerformanceMetrics(): array
    {
        try {
            return [
                'response_time_p95_ms' => $this->getResponseTimePercentile(95),
                'response_time_p99_ms' => $this->getResponseTimePercentile(99),
                'throughput_rps' => $this->getThroughput(),
                'concurrent_users' => $this->getConcurrentUsers(),
                'api_endpoint_performance' => $this->getApiEndpointPerformance(),
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to collect performance metrics', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function storeMetrics(): void
    {
        try {
            $timestamp = now()->toISOString();
            $metricsKey = "metrics:{$timestamp}";
            
            Cache::put($metricsKey, $this->metrics, now()->addDays($this->config['monitoring']['metrics_retention_days']));
            
            // Store in database for long-term analysis
            if (Schema::hasTable('metrics')) {
                DB::table('metrics')->insert([
                    'timestamp' => $timestamp,
                    'metrics' => json_encode($this->metrics),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to store metrics', ['error' => $e->getMessage()]);
        }
    }

    private function checkAlerts(): void
    {
        $thresholds = $this->config['monitoring']['alert_thresholds'];
        
        foreach ($this->metrics as $category => $metrics) {
            if (is_array($metrics)) {
                foreach ($metrics as $metric => $value) {
                    if (is_numeric($value)) {
                        $this->checkMetricThreshold($category, $metric, $value, $thresholds);
                    }
                }
            }
        }
    }

    private function checkMetricThreshold(string $category, string $metric, float $value, array $thresholds): void
    {
        $thresholdKey = $this->getThresholdKey($metric);

        if ($thresholdKey === null || ! isset($thresholds[$thresholdKey])) {
            return;
        }

        $threshold = (float) $thresholds[$thresholdKey];

        if ($threshold <= 0) {
            return;
        }

        if ($value > $threshold) {
            $this->sendAlert($category, $metric, $value, $threshold);
        }
    }

    private function getThresholdKey(string $metric): ?string
    {
        $mapping = [
            'response_time_avg_ms' => 'response_time_ms',
            'error_rate_percent' => 'error_rate_percent',
            'memory_usage_percent' => 'memory_usage_percent',
            'cpu_usage_percent' => 'cpu_usage_percent',
            'disk_usage_percent' => 'disk_usage_percent',
        ];

        return $mapping[$metric] ?? null;
    }

    private function sendAlert(string $category, string $metric, float $value, float $threshold): void
    {
        $alert = [
            'type' => 'threshold_exceeded',
            'category' => $category,
            'metric' => $metric,
            'value' => $value,
            'threshold' => $threshold,
            'timestamp' => now()->toISOString(),
            'severity' => $this->getAlertSeverity($value, $threshold),
        ];

        Log::critical('Alert: Metric threshold exceeded', $alert);

        $this->storeAlert($alert);

        // Send notification (implement based on your notification system)
        $this->sendNotification($alert);
    }

    private function storeAlert(array $alert): void
    {
        try {
            if (! Schema::hasTable('alerts')) {
                return;
            }

            DB::table('alerts')->insert([
                'type' => $alert['type'],
                'category' => $alert['category'],
                'metric' => $alert['metric'],
                'value' => $alert['value'],
                'threshold' => $alert['threshold'],
                'severity' => $alert['severity'],
                'resolved' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to store monitoring alert', [
                'metric' => $alert['metric'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function getAlertSeverity(float $value, float $threshold): string
    {
        if ($threshold <= 0) {
            return 'low';
        }

        $ratio = $value / $threshold;
        
        if ($ratio >= 2.0) return 'critical';
        if ($ratio >= 1.5) return 'high';
        if ($ratio >= 1.2) return 'medium';
        return 'low';
    }

    private function sendNotification(array $alert): void
    {
        // Implement notification sending logic
        // This could be email, Slack, Discord, etc.
        try {
            // Example: Send to Slack
            if (config('monitoring.slack_webhook_url')) {
                $this->sendSlackNotification($alert);
            }
            
            // Example: Send email
            if (config('monitoring.email_alerts')) {
                $this->sendEmailNotification($alert);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send notification', ['error' => $e->getMessage()]);
        }
    }

    private function sendSlackNotification(array $alert): void
    {
        $webhookUrl = config('monitoring.slack_webhook_url');
        
        $message = [
            'text' => "🚨 Alert: {$alert['metric']} threshold exceeded",
            'attachments' => [
                [
                    'color' => $this->getSlackColor($alert['severity']),
                    'fields' => [
                        [
                            'title' => 'Metric',
                            'value' => $alert['metric'],
                            'short' => true,
                        ],
                        [
                            'title' => 'Value',
                            'value' => $alert['value'],
                            'short' => true,
                        ],
                        [
                            'title' => 'Threshold',
                            'value' => $alert['threshold'],
                            'short' => true,
                        ],
                        [
                            'title' => 'Severity',
                            'value' => strtoupper($alert['severity']),
                            'short' => true,
                        ],
                    ],
                    'timestamp' => time(),
                ],
            ],
        ];

        // Send HTTP request to Slack webhook
        // Implementation depends on your HTTP client preference
    }

    private function sendEmailNotification(array $alert): void
    {
        // Implement email notification logic
        // This could use Laravel's Mail facade
    }

    private function getSlackColor(string $severity): string
    {
        $colors = [
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'warning',
            'low' => 'good',
        ];

        return $colors[$severity] ?? 'good';
    }

    public function getHealthStatus(): array
    {
        $healthChecks = $this->config['health_checks'];
        $status = [];

        foreach ($healthChecks as $check => $enabled) {
            if ($enabled) {
                $status[$check] = $this->performHealthCheck($check);
            }
        }

        return [
            'overall_status' => $this->getOverallHealthStatus($status),
            'checks' => $status,
            'timestamp' => now()->toISOString(),
        ];
    }

    private function performHealthCheck(string $check): array
    {
        try {
            switch ($check) {
                case 'database':
                    return $this->checkDatabaseHealth();
                case 'redis':
                    return $this->checkRedisHealth();
                case 'cache':
                    return $this->checkCacheHealth();
                case 'storage':
                    return $this->checkStorageHealth();
                case 'queue':
                    return $this->checkQueueHealth();
                default:
                    return ['status' => 'unknown', 'message' => 'Unknown health check'];
            }
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    private function checkDatabaseHealth(): array
    {
        try {
            $startTime = microtime(true);
            DB::select('SELECT 1');
            $responseTime = (microtime(true) - $startTime) * 1000;

            return [
                'status' => 'healthy',
                'response_time_ms' => round($responseTime, 2),
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    private function checkRedisHealth(): array
    {
        try {
            if (! $this->usesRedis()) {
                return [
                    'status' => 'healthy',
                    'message' => 'Redis is not in active use',
                    'timestamp' => now()->toISOString(),
                ];
            }

            $startTime = microtime(true);
            Redis::ping();
            $responseTime = (microtime(true) - $startTime) * 1000;

            return [
                'status' => 'healthy',
                'response_time_ms' => round($responseTime, 2),
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    private function checkCacheHealth(): array
    {
        try {
            $testKey = 'health_check_' . time();
            $testValue = 'test_value';
            
            $startTime = microtime(true);
            Cache::put($testKey, $testValue, 60);
            $retrievedValue = Cache::get($testKey);
            Cache::forget($testKey);
            $responseTime = (microtime(true) - $startTime) * 1000;

            if ($retrievedValue === $testValue) {
                return [
                    'status' => 'healthy',
                    'response_time_ms' => round($responseTime, 2),
                    'timestamp' => now()->toISOString(),
                ];
            } else {
                return [
                    'status' => 'unhealthy',
                    'error' => 'Cache read/write test failed',
                    'timestamp' => now()->toISOString(),
                ];
            }
        } catch (\Throwable $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    private function checkStorageHealth(): array
    {
        try {
            $testFile = 'health_check_' . time() . '.txt';
            $testContent = 'health check content';
            
            $startTime = microtime(true);
            Storage::put($testFile, $testContent);
            $retrievedContent = Storage::get($testFile);
            Storage::delete($testFile);
            $responseTime = (microtime(true) - $startTime) * 1000;

            if ($retrievedContent === $testContent) {
                return [
                    'status' => 'healthy',
                    'response_time_ms' => round($responseTime, 2),
                    'timestamp' => now()->toISOString(),
                ];
            } else {
                return [
                    'status' => 'unhealthy',
                    'error' => 'Storage read/write test failed',
                    'timestamp' => now()->toISOString(),
                ];
            }
        } catch (\Throwable $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    private function checkQueueHealth(): array
    {
        try {
            $queueDriver = config('queue.default');
            
            if ($queueDriver === 'redis') {
                $redis = Redis::connection();
                $queueSize = $redis->llen('queues:default');
                
                return [
                    'status' => 'healthy',
                    'queue_size' => $queueSize,
                    'timestamp' => now()->toISOString(),
                ];
            }

            return [
                'status' => 'healthy',
                'message' => 'Queue driver not configured for health check',
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    private function getOverallHealthStatus(array $checks): string
    {
        $problemCount = 0;
        
        foreach ($checks as $check) {
            if (($check['status'] ?? 'unknown') !== 'healthy') {
                $problemCount++;
            }
        }

        if ($problemCount === 0) return 'healthy';
        if ($problemCount === count($checks)) return 'unhealthy';
        return 'degraded';
    }

    // Helper methods for metric collection
    private function parseMemoryLimit(string $limit): int
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit) - 1]);
        $value = (int) $limit;

        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    private function calculateMemoryUsagePercent(int $usage, string $limit): float
    {
        $limitBytes = $this->parseMemoryLimit($limit);
        return $limitBytes > 0 ? round(($usage / $limitBytes) * 100, 2) : 0;
    }

    private function getCpuUsage(): float
    {
        // This is a simplified implementation
        // In production, you might want to use more sophisticated methods
        if (! function_exists('sys_getloadavg')) {
            return 0;
        }

        $load = \sys_getloadavg();
        if (! is_array($load) || ! isset($load[0])) {
            return 0;
        }

        return max(round($load[0] * 100, 2), 0);
    }

    private function getDiskUsage(): float
    {
        $path = base_path();
        $bytes = @disk_free_space($path);
        $total = @disk_total_space($path);

        if ($bytes === false || $total === false) {
            return 0;
        }

        return $total > 0 ? round((($total - $bytes) / $total) * 100, 2) : 0;
    }

    private function getUptime(): int
    {
        $uptimeFile = '/proc/uptime';

        if (! is_file($uptimeFile) || ! is_readable($uptimeFile)) {
            return 0;
        }

        $uptime = @file_get_contents($uptimeFile);

        if (! is_string($uptime) || trim($uptime) === '') {
            return 0;
        }

        return (int) explode(' ', trim($uptime))[0];
    }

    private function getRequestsPerMinute(): int
    {
        // This would need to be implemented based on your access logs
        return 0;
    }

    private function getAverageResponseTime(): float
    {
        // This would need to be implemented based on your application metrics
        return 0;
    }

    private function getErrorRate(): float
    {
        // This would need to be implemented based on your error logs
        return 0;
    }

    private function getActiveUsers(): int
    {
        // This would need to be implemented based on your session data
        return 0;
    }

    private function getCacheHitRate(): float
    {
        // This would need to be implemented based on your cache metrics
        return 0;
    }

    private function getQueueSize(): int
    {
        // This would need to be implemented based on your queue metrics
        return 0;
    }

    private function getDatabaseConnections(): int
    {
        // This would need to be implemented based on your database metrics
        return 0;
    }

    private function calculateInnoDBHitRate(array $status): float
    {
        $reads = $status['Innodb_buffer_pool_reads'] ?? 0;
        $readRequests = $status['Innodb_buffer_pool_read_requests'] ?? 0;
        
        if ($readRequests > 0) {
            return round((1 - ($reads / $readRequests)) * 100, 2);
        }
        
        return 0;
    }

    private function calculateHitRate(int $hits, int $misses): float
    {
        $total = $hits + $misses;
        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    private function getResponseTimePercentile(int $percentile): float
    {
        // This would need to be implemented based on your response time data
        return 0;
    }

    private function getThroughput(): float
    {
        // This would need to be implemented based on your request data
        return 0;
    }

    private function getConcurrentUsers(): int
    {
        // This would need to be implemented based on your session data
        return 0;
    }

    private function getApiEndpointPerformance(): array
    {
        // This would need to be implemented based on your API metrics
        return [];
    }

    private function usesRedis(): bool
    {
        return in_array(config('cache.default'), ['redis'], true)
            || in_array(config('queue.default'), ['redis'], true)
            || in_array(config('session.driver'), ['redis'], true)
            || in_array(config('broadcasting.default'), ['redis'], true);
    }

    public function getMetricsHistory(int $hours = 24): array
    {
        try {
            if (! Schema::hasTable('metrics')) {
                return [];
            }

            $since = now()->subHours($hours);
            
            $metrics = DB::table('metrics')
                ->where('timestamp', '>=', $since)
                ->orderBy('timestamp', 'desc')
                ->get()
                ->map(function ($metric) {
                    return [
                        'timestamp' => $metric->timestamp,
                        'metrics' => json_decode($metric->metrics, true),
                    ];
                });

            return $metrics->toArray();
        } catch (\Throwable $e) {
            Log::error('Failed to get metrics history', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function getAlerts(int $days = 7): array
    {
        try {
            if (! Schema::hasTable('alerts')) {
                return [];
            }

            $since = now()->subDays($days);
            
            $alerts = DB::table('alerts')
                ->where('created_at', '>=', $since)
                ->orderBy('created_at', 'desc')
                ->get();

            return $alerts->toArray();
        } catch (\Throwable $e) {
            Log::error('Failed to get alerts', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function resolveAlert(int $alertId): bool
    {
        try {
            if (! Schema::hasTable('alerts')) {
                return false;
            }

            DB::table('alerts')
                ->where('id', $alertId)
                ->update([
                    'resolved' => true,
                    'resolved_at' => now(),
                    'updated_at' => now(),
                ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to resolve alert', ['alert_id' => $alertId, 'error' => $e->getMessage()]);
            return false;
        }
    }
}
