<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Application\Shared\Services\QueryOptimizationService;
use App\Application\Shared\Services\EnhancedCacheService;
use App\Application\Shared\Services\QueryMonitoringService;
use App\Application\Shared\Services\DatabaseOptimizationService;
use App\Application\Shared\Services\EagerLoadingService;
use App\Application\Shared\Services\CacheWarmingService;
use App\Application\Shared\Services\PerformanceTestingService;

class OptimizePerformanceCommand extends Command
{
    protected $signature = 'performance:optimize 
                            {--cache-warm : Warm up cache}
                            {--query-monitor : Start query monitoring}
                            {--query-analyze : Analyze query performance}
                            {--cache-stats : Show cache statistics}
                            {--db-optimize : Optimize database}
                            {--eager-loading : Test eager loading}
                            {--performance-test : Run performance tests}
                            {--cache-warming-strategy : Test cache warming strategies}
                            {--full : Run full optimization}';

    protected $description = 'Optimize application performance with caching, query optimization, and monitoring';

    public function handle(
        QueryOptimizationService $queryService,
        EnhancedCacheService $cacheService,
        QueryMonitoringService $monitoringService,
        DatabaseOptimizationService $dbService,
        EagerLoadingService $eagerLoadingService,
        CacheWarmingService $cacheWarmingService,
        PerformanceTestingService $performanceTestingService
    ): int {
        $this->info('Starting performance optimization...');

        try {
            if ($this->option('cache-warm')) {
                $this->warmUpCache($cacheService);
            } elseif ($this->option('query-monitor')) {
                $this->startQueryMonitoring($monitoringService);
            } elseif ($this->option('query-analyze')) {
                $this->analyzeQueryPerformance($queryService, $monitoringService);
            } elseif ($this->option('cache-stats')) {
                $this->showCacheStatistics($cacheService);
            } elseif ($this->option('db-optimize')) {
                $this->optimizeDatabase($dbService);
            } elseif ($this->option('eager-loading')) {
                $this->testEagerLoading($eagerLoadingService);
            } elseif ($this->option('performance-test')) {
                $this->runPerformanceTests($performanceTestingService);
            } elseif ($this->option('cache-warming-strategy')) {
                $this->testCacheWarmingStrategies($cacheWarmingService);
            } elseif ($this->option('full')) {
                $this->runFullOptimization($queryService, $cacheService, $monitoringService, $dbService, $eagerLoadingService, $cacheWarmingService, $performanceTestingService);
            } else {
                $this->showHelp();
            }

            $this->info('Performance optimization completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Performance optimization failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function warmUpCache(EnhancedCacheService $cacheService): void
    {
        $this->info('Warming up cache...');
        
        $cacheService->warmUpCache();
        
        $this->info('Cache warming completed!');
    }

    private function startQueryMonitoring(QueryMonitoringService $monitoringService): void
    {
        $this->info('Starting query monitoring...');
        
        $monitoringService->startMonitoring();
        
        $this->info('Query monitoring started. Use Ctrl+C to stop.');
        
        // Keep monitoring for 30 seconds
        sleep(30);
        
        $monitoringService->stopMonitoring();
        
        $this->showQueryStatistics($monitoringService);
    }

    private function analyzeQueryPerformance(
        QueryOptimizationService $queryService,
        QueryMonitoringService $monitoringService
    ): void {
        $this->info('Analyzing query performance...');
        
        // Start monitoring
        $monitoringService->startMonitoring();
        
        // Run some sample queries
        $this->runSampleQueries($queryService);
        
        // Stop monitoring
        $monitoringService->stopMonitoring();
        
        // Show analysis
        $this->showQueryAnalysis($monitoringService);
    }

    private function runSampleQueries(QueryOptimizationService $queryService): void
    {
        $this->info('Running sample queries...');
        
        try {
            // Sample product queries
            $products = DB::table('products')->where('is_active', true)->limit(10)->get();
            $this->line('✓ Products query executed');
            
            // Sample category queries
            $categories = DB::table('product_categories')->where('is_active', true)->get();
            $this->line('✓ Categories query executed');
            
            // Sample order queries
            $orders = DB::table('orders')->where('status', 'completed')->limit(5)->get();
            $this->line('✓ Orders query executed');
            
            // Sample customer queries
            $customers = DB::table('customers')->where('is_active', true)->limit(5)->get();
            $this->line('✓ Customers query executed');
            
        } catch (\Exception $e) {
            $this->warn('Some sample queries failed: ' . $e->getMessage());
        }
    }

    private function showQueryAnalysis(QueryMonitoringService $monitoringService): void
    {
        $this->info('Query Performance Analysis:');
        $this->line('');
        
        // Statistics
        $stats = $monitoringService->getQueryStatistics();
        $this->table(['Metric', 'Value'], [
            ['Total Queries', $stats['total_queries']],
            ['Slow Queries', $stats['slow_queries']],
            ['Average Execution Time', $stats['average_execution_time'] . 's'],
            ['Total Execution Time', $stats['total_execution_time'] . 's'],
            ['Slow Query Percentage', $stats['slow_query_percentage'] . '%'],
        ]);
        
        // Top slow queries
        $slowQueries = $monitoringService->getTopSlowQueries(5);
        if (!empty($slowQueries)) {
            $this->line('');
            $this->info('Top Slow Queries:');
            $this->table(['SQL', 'Execution Time'], 
                array_map(function ($query) {
                    return [
                        substr($query['sql'], 0, 50) . '...',
                        $query['execution_time'] . 's'
                    ];
                }, $slowQueries)
            );
        }
        
        // Recommendations
        $recommendations = $monitoringService->generateOptimizationRecommendations();
        if (!empty($recommendations)) {
            $this->line('');
            $this->info('Optimization Recommendations:');
            foreach ($recommendations as $rec) {
                $priority = $rec['priority'] === 'high' ? '🔴' : ($rec['priority'] === 'medium' ? '🟡' : '🟢');
                $this->line("{$priority} {$rec['message']}");
            }
        }
    }

    private function showQueryStatistics(QueryMonitoringService $monitoringService): void
    {
        $stats = $monitoringService->getQueryStatistics();
        
        $this->info('Query Monitoring Results:');
        $this->table(['Metric', 'Value'], [
            ['Total Queries', $stats['total_queries']],
            ['Slow Queries', $stats['slow_queries']],
            ['Average Execution Time', $stats['average_execution_time'] . 's'],
            ['Slow Query Percentage', $stats['slow_query_percentage'] . '%'],
        ]);
    }

    private function showCacheStatistics(EnhancedCacheService $cacheService): void
    {
        $this->info('Cache Statistics:');
        
        $stats = $cacheService->getCacheStatistics();
        
        $this->table(['Metric', 'Value'], [
            ['Memory Cache Items', $stats['memory_cache']],
            ['Redis Connected', $stats['redis_connected'] ? 'Yes' : 'No'],
            ['File Cache Size', $this->formatBytes($stats['file_cache_size'])],
        ]);
        
        if (isset($stats['redis_info'])) {
            $this->line('');
            $this->info('Redis Information:');
            $redisInfo = $stats['redis_info'];
            $this->table(['Metric', 'Value'], [
                ['Redis Version', $redisInfo['redis_version'] ?? 'N/A'],
                ['Used Memory', $this->formatBytes($redisInfo['used_memory'] ?? 0)],
                ['Connected Clients', $redisInfo['connected_clients'] ?? 'N/A'],
                ['Total Commands Processed', $redisInfo['total_commands_processed'] ?? 'N/A'],
            ]);
        }
    }

    private function optimizeDatabase(DatabaseOptimizationService $dbService): void
    {
        $this->info('Optimizing database...');
        
        $results = $dbService->optimizeDatabase();
        
        $this->info('Database optimization completed!');
        
        // Show results summary
        $this->line('');
        $this->info('Optimization Results:');
        foreach ($results as $operation => $result) {
            if (is_array($result) && !isset($result['error'])) {
                $this->line("✓ {$operation}: " . count($result) . " items processed");
            } else {
                $this->line("❌ {$operation}: Failed");
            }
        }
    }

    private function testEagerLoading(EagerLoadingService $eagerLoadingService): void
    {
        $this->info('Testing eager loading optimization...');
        
        // Test relationship statistics
        $stats = $eagerLoadingService->getRelationshipStatistics();
        
        $this->info('Eager Loading Statistics:');
        $this->table(['Model', 'Contexts', 'Total Relationships', 'Most Used Context'], 
            collect($stats)->map(function ($stat, $model) {
                return [
                    $model,
                    implode(', ', $stat['contexts']),
                    $stat['total_relationships'],
                    $stat['most_used_context']
                ];
            })->toArray()
        );
        
        $this->info('Eager loading test completed!');
    }

    private function runPerformanceTests(PerformanceTestingService $performanceTestingService): void
    {
        $this->info('Running comprehensive performance tests...');
        
        $results = $performanceTestingService->runComprehensiveTests();
        $metrics = $performanceTestingService->getPerformanceMetrics();
        
        // Show overall score
        $this->info('Performance Test Results:');
        $this->line('');
        $this->line("Overall Score: {$metrics['overall_score']}/100");
        
        // Show test results
        foreach ($results as $test => $result) {
            if (isset($result['status']) && $result['status'] === 'success') {
                $this->line("✓ {$test}: {$result['total_time']}s");
            } else {
                $this->line("❌ {$test}: Failed");
            }
        }
        
        // Show recommendations
        if (!empty($metrics['recommendations'])) {
            $this->line('');
            $this->info('Recommendations:');
            foreach ($metrics['recommendations'] as $recommendation) {
                $this->line("• {$recommendation}");
            }
        }
        
        // Show bottlenecks
        if (!empty($metrics['bottlenecks'])) {
            $this->line('');
            $this->warn('Bottlenecks detected:');
            foreach ($metrics['bottlenecks'] as $bottleneck) {
                $this->line("⚠ {$bottleneck}");
            }
        }
        
        $this->info('Performance tests completed!');
    }

    private function testCacheWarmingStrategies(CacheWarmingService $cacheWarmingService): void
    {
        $this->info('Testing cache warming strategies...');
        
        // Test individual strategies
        $strategies = ['critical', 'frequent', 'popular', 'statistics', 'background'];
        
        foreach ($strategies as $strategy) {
            $this->info("Testing strategy: {$strategy}");
            $cacheWarmingService->warmUpStrategy($strategy);
        }
        
        // Show statistics
        $stats = $cacheWarmingService->getWarmingStatistics();
        
        $this->info('Cache Warming Statistics:');
        $this->table(['Strategy', 'Status'], 
            collect($stats['strategies'])->map(function ($strategy) {
                return [$strategy, 'Completed'];
            })->toArray()
        );
        
        $this->info('Cache warming strategy test completed!');
    }

    private function runFullOptimization(
        QueryOptimizationService $queryService,
        EnhancedCacheService $cacheService,
        QueryMonitoringService $monitoringService,
        DatabaseOptimizationService $dbService,
        EagerLoadingService $eagerLoadingService,
        CacheWarmingService $cacheWarmingService,
        PerformanceTestingService $performanceTestingService
    ): void {
        $this->info('Running full performance optimization...');
        
        // Step 1: Optimize database connection
        $this->info('Step 1: Optimizing database connection...');
        $queryService->optimizeConnection();
        
        // Step 2: Optimize database
        $this->info('Step 2: Optimizing database...');
        $this->optimizeDatabase($dbService);
        
        // Step 3: Warm up cache
        $this->info('Step 3: Warming up cache...');
        $this->warmUpCache($cacheService);
        
        // Step 4: Monitor queries
        $this->info('Step 4: Monitoring queries...');
        $monitoringService->startMonitoring();
        
        // Run sample queries
        $this->runSampleQueries($queryService);
        
        $monitoringService->stopMonitoring();
        
        // Step 5: Test eager loading
        $this->info('Step 5: Testing eager loading...');
        $this->testEagerLoading($eagerLoadingService);
        
        // Step 6: Cache warming strategies
        $this->info('Step 6: Testing cache warming strategies...');
        $this->testCacheWarmingStrategies($cacheWarmingService);
        
        // Step 7: Performance tests
        $this->info('Step 7: Running performance tests...');
        $this->runPerformanceTests($performanceTestingService);
        
        // Step 8: Show results
        $this->info('Step 8: Performance analysis...');
        $this->showQueryAnalysis($monitoringService);
        $this->showCacheStatistics($cacheService);
        
        $this->info('Full optimization completed!');
    }

    private function showHelp(): void
    {
        $this->info('Performance Optimization Commands:');
        $this->line('');
        $this->line('Available options:');
        $this->line('  --cache-warm     Warm up application cache');
        $this->line('  --query-monitor  Start query monitoring');
        $this->line('  --query-analyze  Analyze query performance');
        $this->line('  --cache-stats    Show cache statistics');
        $this->line('  --db-optimize    Optimize database');
        $this->line('  --eager-loading  Test eager loading');
        $this->line('  --performance-test Run performance tests');
        $this->line('  --cache-warming-strategy Test cache warming strategies');
        $this->line('  --full           Run full optimization');
        $this->line('');
        $this->line('Examples:');
        $this->line('  php artisan performance:optimize --cache-warm');
        $this->line('  php artisan performance:optimize --query-monitor');
        $this->line('  php artisan performance:optimize --full');
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
