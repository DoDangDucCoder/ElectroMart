<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PerformanceTestingService
{
    private array $testResults = [];
    private array $performanceMetrics = [];
    private ?string $databaseAvailabilityError = null;

    public function runComprehensiveTests(): array
    {
        Log::info('Starting comprehensive performance tests...');

        $this->testResults = [
            'database_performance' => $this->testDatabasePerformance(),
            'cache_performance' => $this->testCachePerformance(),
            'query_performance' => $this->testQueryPerformance(),
            'memory_usage' => $this->testMemoryUsage(),
            'response_times' => $this->testResponseTimes(),
        ];

        try {
            $this->generatePerformanceReport();
        } catch (\Exception $e) {
            Log::error('Failed to generate performance report', ['error' => $e->getMessage()]);
            $this->performanceMetrics = [
                'overall_score' => 0,
                'recommendations' => ['Failed to generate report'],
                'bottlenecks' => ['Report generation failed'],
                'improvements' => ['Fix report generation'],
            ];
        }

        Log::info('Comprehensive performance tests completed');
        return $this->testResults;
    }

    private function testDatabasePerformance(): array
    {
        if (! $this->databaseConnectionAvailable()) {
            return $this->skipDatabaseBoundTest('database performance');
        }

        $results = [];
        $startTime = microtime(true);

        try {
            // Test 1: Simple SELECT query
            $start = microtime(true);
            $products = DB::table('products')->where('is_active', true)->limit(10)->get();
            $results['simple_select'] = microtime(true) - $start;

            // Test 2: Complex JOIN query
            $start = microtime(true);
            $complexQuery = DB::table('products')
                ->join('brands', 'products.brand_id', '=', 'brands.id')
                ->join('product_categories', 'products.primary_category_id', '=', 'product_categories.id')
                ->where('products.is_active', true)
                ->select('products.*', 'brands.name as brand_name', 'product_categories.name as category_name')
                ->limit(10)
                ->get();
            $results['complex_join'] = microtime(true) - $start;

            // Test 3: COUNT query
            $start = microtime(true);
            $productCount = DB::table('products')->where('is_active', true)->count();
            $results['count_query'] = microtime(true) - $start;

            // Test 4: INSERT query
            $start = microtime(true);
            $testData = [
                'name' => 'Test Product ' . time(),
                'slug' => 'test-product-' . time(),
                'price' => 100.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $insertId = DB::table('products')->insertGetId($testData);
            $results['insert_query'] = microtime(true) - $start;

            // Clean up test data
            DB::table('products')->where('id', $insertId)->delete();

            // Test 5: UPDATE query
            $start = microtime(true);
            DB::table('products')->where('id', 1)->update(['updated_at' => now()]);
            $results['update_query'] = microtime(true) - $start;

            $results['total_time'] = microtime(true) - $startTime;
            $results['status'] = 'success';

        } catch (\Exception $e) {
            $results['status'] = 'error';
            $results['error'] = $e->getMessage();
            Log::error('Database performance test failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    private function testCachePerformance(): array
    {
        $results = [];
        $startTime = microtime(true);

        try {
            $testKey = 'performance_test_' . time();
            $testData = ['test' => 'data', 'timestamp' => time()];

            // Test 1: Cache SET
            $start = microtime(true);
            Cache::put($testKey, $testData, 60);
            $results['cache_set'] = microtime(true) - $start;

            // Test 2: Cache GET
            $start = microtime(true);
            $retrievedData = Cache::get($testKey);
            $results['cache_get'] = microtime(true) - $start;

            // Test 3: Cache REMEMBER
            $start = microtime(true);
            $rememberedData = Cache::remember($testKey . '_remember', 60, function () {
                return ['remembered' => 'data'];
            });
            $results['cache_remember'] = microtime(true) - $start;

            // Test 4: Cache FORGET
            $start = microtime(true);
            Cache::forget($testKey);
            $results['cache_forget'] = microtime(true) - $start;

            // Test 5: Multiple operations
            $start = microtime(true);
            for ($i = 0; $i < 100; $i++) {
                Cache::put("test_key_{$i}", "test_value_{$i}", 60);
            }
            $results['bulk_cache_set'] = microtime(true) - $start;

            // Clean up
            for ($i = 0; $i < 100; $i++) {
                Cache::forget("test_key_{$i}");
            }
            Cache::forget($testKey . '_remember');

            $results['total_time'] = microtime(true) - $startTime;
            $results['status'] = 'success';

        } catch (\Exception $e) {
            $results['status'] = 'error';
            $results['error'] = $e->getMessage();
            Log::error('Cache performance test failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    private function testQueryPerformance(): array
    {
        if (! $this->databaseConnectionAvailable()) {
            return $this->skipDatabaseBoundTest('query performance');
        }

        $results = [];
        $startTime = microtime(true);

        try {
            // Test 1: N+1 Query Problem Simulation
            $start = microtime(true);
            $products = DB::table('products')->where('is_active', true)->limit(10)->get();
            foreach ($products as $product) {
                $brand = DB::table('brands')->where('id', $product->brand_id)->first();
            }
            $results['n_plus_one_query'] = microtime(true) - $start;

            // Test 2: Optimized Query with JOIN
            $start = microtime(true);
            $optimizedProducts = DB::table('products')
                ->join('brands', 'products.brand_id', '=', 'brands.id')
                ->where('products.is_active', true)
                ->select('products.*', 'brands.name as brand_name')
                ->limit(10)
                ->get();
            $results['optimized_query'] = microtime(true) - $start;

            // Test 3: Pagination Query
            $start = microtime(true);
            $paginatedProducts = DB::table('products')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            $results['pagination_query'] = microtime(true) - $start;

            // Test 4: Search Query
            $start = microtime(true);
            $searchResults = DB::table('products')
                ->where('is_active', true)
                ->where('name', 'LIKE', '%test%')
                ->orWhere('description', 'LIKE', '%test%')
                ->limit(10)
                ->get();
            $results['search_query'] = microtime(true) - $start;

            $results['total_time'] = microtime(true) - $startTime;
            $results['status'] = 'success';

        } catch (\Exception $e) {
            $results['status'] = 'error';
            $results['error'] = $e->getMessage();
            Log::error('Query performance test failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    private function testMemoryUsage(): array
    {
        if (! $this->databaseConnectionAvailable()) {
            return $this->skipDatabaseBoundTest('memory usage');
        }

        $results = [];
        $startTime = microtime(true);

        try {
            // Test 1: Memory usage before operations
            $startMemory = memory_get_usage(true);
            $results['start_memory'] = $this->formatBytes($startMemory);

            // Test 2: Load large dataset
            $start = microtime(true);
            $largeDataset = DB::table('products')->where('is_active', true)->get();
            $results['large_dataset_load'] = microtime(true) - $start;
            $results['large_dataset_count'] = $largeDataset->count();

            // Test 3: Memory usage after loading
            $afterMemory = memory_get_usage(true);
            $results['after_memory'] = $this->formatBytes($afterMemory);
            $results['memory_increase'] = $this->formatBytes($afterMemory - $startMemory);

            // Test 4: Peak memory usage
            $peakMemory = memory_get_peak_usage(true);
            $results['peak_memory'] = $this->formatBytes($peakMemory);

            // Test 5: Memory limit
            $memoryLimit = ini_get('memory_limit');
            $results['memory_limit'] = $memoryLimit;

            $results['total_time'] = microtime(true) - $startTime;
            $results['status'] = 'success';

        } catch (\Exception $e) {
            $results['status'] = 'error';
            $results['error'] = $e->getMessage();
            Log::error('Memory usage test failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    private function testResponseTimes(): array
    {
        if (! $this->databaseConnectionAvailable()) {
            return $this->skipDatabaseBoundTest('response time');
        }

        $results = [];
        $startTime = microtime(true);

        try {
            // Test 1: Homepage simulation
            $start = microtime(true);
            $this->simulateHomepageLoad();
            $results['homepage_simulation'] = microtime(true) - $start;

            // Test 2: Product listing simulation
            $start = microtime(true);
            $this->simulateProductListing();
            $results['product_listing_simulation'] = microtime(true) - $start;

            // Test 3: Product detail simulation
            $start = microtime(true);
            $this->simulateProductDetail();
            $results['product_detail_simulation'] = microtime(true) - $start;

            // Test 4: Search simulation
            $start = microtime(true);
            $this->simulateSearch();
            $results['search_simulation'] = microtime(true) - $start;

            $results['total_time'] = microtime(true) - $startTime;
            $results['status'] = 'success';

        } catch (\Exception $e) {
            $results['status'] = 'error';
            $results['error'] = $e->getMessage();
            Log::error('Response time test failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    private function simulateHomepageLoad(): void
    {
        // Simulate homepage data loading
        DB::table('products')->where('is_active', true)->limit(6)->get();
        DB::table('product_categories')->where('is_active', true)->get();
        DB::table('brands')->where('is_active', true)->get();
    }

    private function simulateProductListing(): void
    {
        // Simulate product listing page
        DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->join('product_categories', 'products.primary_category_id', '=', 'product_categories.id')
            ->where('products.is_active', true)
            ->select('products.*', 'brands.name as brand_name', 'product_categories.name as category_name')
            ->paginate(12);
    }

    private function simulateProductDetail(): void
    {
        // Simulate product detail page
        $product = DB::table('products')->where('is_active', true)->first();
        if ($product) {
            DB::table('brands')->where('id', $product->brand_id)->first();
            DB::table('product_categories')->where('id', $product->primary_category_id)->first();
            DB::table('products')->where('primary_category_id', $product->primary_category_id)->limit(6)->get();
        }
    }

    private function simulateSearch(): void
    {
        // Simulate search functionality
        DB::table('products')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('name', 'LIKE', '%test%')
                      ->orWhere('description', 'LIKE', '%test%');
            })
            ->limit(20)
            ->get();
    }

    private function generatePerformanceReport(): void
    {
        $overallScore = $this->calculateOverallScore();

        $this->performanceMetrics = [
            'overall_score' => $overallScore,
            'recommendations' => $this->generateRecommendations(),
            'bottlenecks' => $this->identifyBottlenecks(),
            'improvements' => $this->suggestImprovements($overallScore),
        ];
    }

    private function calculateOverallScore(): int
    {
        $scores = [];

        // Database performance score
        if (isset($this->testResults['database_performance']['status']) && 
            $this->testResults['database_performance']['status'] === 'success') {
            $dbTime = $this->testResults['database_performance']['total_time'];
            $scores[] = $dbTime < 0.1 ? 100 : ($dbTime < 0.5 ? 80 : ($dbTime < 1.0 ? 60 : 40));
        }

        // Cache performance score
        if (isset($this->testResults['cache_performance']['status']) && 
            $this->testResults['cache_performance']['status'] === 'success') {
            $cacheTime = $this->testResults['cache_performance']['total_time'];
            $scores[] = $cacheTime < 0.05 ? 100 : ($cacheTime < 0.1 ? 80 : ($cacheTime < 0.2 ? 60 : 40));
        }

        // Query performance score
        if (isset($this->testResults['query_performance']['status']) && 
            $this->testResults['query_performance']['status'] === 'success') {
            $queryTime = $this->testResults['query_performance']['total_time'];
            $scores[] = $queryTime < 0.2 ? 100 : ($queryTime < 0.5 ? 80 : ($queryTime < 1.0 ? 60 : 40));
        }

        return empty($scores) ? 0 : round(array_sum($scores) / count($scores));
    }

    private function generateRecommendations(): array
    {
        $recommendations = [];

        // Database recommendations
        if (isset($this->testResults['database_performance']['complex_join'])) {
            if ($this->testResults['database_performance']['complex_join'] > 0.1) {
                $recommendations[] = 'Consider adding indexes for JOIN operations';
            }
        }

        // Cache recommendations
        if (isset($this->testResults['cache_performance']['cache_set'])) {
            if ($this->testResults['cache_performance']['cache_set'] > 0.01) {
                $recommendations[] = 'Consider using Redis for better cache performance';
            }
        }

        // Query recommendations
        if (
            isset($this->testResults['query_performance']['n_plus_one_query']) &&
            isset($this->testResults['query_performance']['optimized_query'])
        ) {
            if ($this->testResults['query_performance']['n_plus_one_query'] > 
                $this->testResults['query_performance']['optimized_query']) {
                $recommendations[] = 'Implement eager loading to prevent N+1 queries';
            }
        }

        return $recommendations;
    }

    private function identifyBottlenecks(): array
    {
        $bottlenecks = [];

        foreach ($this->testResults as $test => $results) {
            if (isset($results['total_time']) && $results['total_time'] > 1.0) {
                $bottlenecks[] = "{$test}: {$results['total_time']}s";
            }
        }

        return $bottlenecks;
    }

    private function suggestImprovements(?int $overallScore = null): array
    {
        $improvements = [];

        $overallScore ??= (int) ($this->performanceMetrics['overall_score'] ?? 0);

        if ($overallScore < 60) {
            $improvements[] = 'Critical: Immediate performance optimization required';
        } elseif ($overallScore < 80) {
            $improvements[] = 'Moderate: Consider performance improvements';
        } else {
            $improvements[] = 'Good: Performance is acceptable';
        }

        return $improvements;
    }

    public function getTestResults(): array
    {
        return $this->testResults;
    }

    public function getPerformanceMetrics(): array
    {
        return $this->performanceMetrics;
    }

    public function getDetailedReport(): array
    {
        return [
            'test_results' => $this->testResults,
            'performance_metrics' => $this->performanceMetrics,
            'timestamp' => now(),
            'environment' => app()->environment(),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    private function databaseConnectionAvailable(): bool
    {
        if ($this->databaseAvailabilityError !== null) {
            return false;
        }

        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Throwable $e) {
            $this->databaseAvailabilityError = $e->getMessage();

            return false;
        }
    }

    private function skipDatabaseBoundTest(string $testName): array
    {
        $error = $this->databaseAvailabilityError ?? 'Database connection is unavailable.';

        Log::warning("Skipping {$testName} test because the database is unavailable", [
            'error' => $error,
        ]);

        return [
            'status' => 'skipped',
            'reason' => 'Database unavailable',
            'error' => $error,
        ];
    }
}
