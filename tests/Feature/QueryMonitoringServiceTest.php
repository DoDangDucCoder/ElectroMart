<?php

namespace Tests\Feature;

use App\Application\Shared\Services\QueryMonitoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueryMonitoringServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_query_statistics_include_slow_query_percentage(): void
    {
        $service = $this->freshService();

        $this->assertSame([
            'total_queries' => 0,
            'slow_queries' => 0,
            'average_execution_time' => 0,
            'total_execution_time' => 0,
            'slow_query_percentage' => 0,
        ], $service->getQueryStatistics());
    }

    public function test_restarting_monitoring_does_not_duplicate_query_listeners(): void
    {
        $service = $this->freshService();

        $service->startMonitoring();
        DB::select('SELECT 1 as test');
        $service->stopMonitoring();

        $this->assertSame(1, $service->getQueryStatistics()['total_queries']);

        $service->startMonitoring();
        DB::select('SELECT 1 as test');
        $service->stopMonitoring();

        $this->assertSame(1, $service->getQueryStatistics()['total_queries']);
    }

    public function test_table_usage_statistics_support_quoted_sql_identifiers(): void
    {
        $service = $this->freshService();

        $property = new \ReflectionProperty($service, 'queries');
        $property->setAccessible(true);
        $property->setValue($service, [
            [
                'sql' => 'select * from "orders" inner join "customers" on "customers"."id" = "orders"."customer_id"',
                'execution_time' => 0.25,
                'bindings' => [],
                'timestamp' => now(),
                'connection' => 'sqlite',
            ],
        ]);

        $stats = $service->getTableUsageStatistics();

        $this->assertArrayHasKey('orders', $stats);
        $this->assertArrayHasKey('customers', $stats);
        $this->assertSame(1, $stats['orders']['query_count']);
        $this->assertSame(0.25, $stats['customers']['total_time']);
    }

    public function test_duplicate_queries_report_excludes_single_execution_queries(): void
    {
        $service = $this->freshService();

        $property = new \ReflectionProperty($service, 'queries');
        $property->setAccessible(true);
        $property->setValue($service, [
            [
                'sql' => 'select * from products where id = ?',
                'execution_time' => 0.10,
                'bindings' => [1],
                'timestamp' => now(),
                'connection' => 'sqlite',
            ],
            [
                'sql' => 'select * from orders where id = ?',
                'execution_time' => 0.20,
                'bindings' => [5],
                'timestamp' => now(),
                'connection' => 'sqlite',
            ],
            [
                'sql' => 'select * from orders where id = ?',
                'execution_time' => 0.30,
                'bindings' => [6],
                'timestamp' => now(),
                'connection' => 'sqlite',
            ],
        ]);

        $duplicates = $service->getDuplicateQueries();

        $this->assertCount(1, $duplicates);
        $this->assertSame('select * from orders where id = ?', array_key_first($duplicates));
        $this->assertSame(2, $duplicates['select * from orders where id = ?']['count']);
    }

    public function test_query_patterns_detect_select_queries_with_cte_and_leading_whitespace(): void
    {
        $service = $this->freshService();

        $property = new \ReflectionProperty($service, 'queries');
        $property->setAccessible(true);
        $property->setValue($service, [
            [
                'sql' => "   select * from products order by id desc limit 5",
                'execution_time' => 0.10,
                'bindings' => [],
                'timestamp' => now(),
                'connection' => 'sqlite',
            ],
            [
                'sql' => 'with recent_orders as (select * from orders) select * from recent_orders',
                'execution_time' => 0.20,
                'bindings' => [],
                'timestamp' => now(),
                'connection' => 'sqlite',
            ],
        ]);

        $patterns = $service->analyzeQueryPatterns();

        $this->assertSame(2, $patterns['select_queries']);
        $this->assertSame(1, $patterns['order_by_queries']);
        $this->assertSame(1, $patterns['limit_queries']);
        $this->assertSame(1, $patterns['subquery_queries']);
    }

    public function test_table_usage_statistics_ignore_cte_aliases(): void
    {
        $service = $this->freshService();

        $property = new \ReflectionProperty($service, 'queries');
        $property->setAccessible(true);
        $property->setValue($service, [
            [
                'sql' => 'with recent_orders as (select * from orders) select * from recent_orders join customers on customers.id = recent_orders.customer_id',
                'execution_time' => 0.40,
                'bindings' => [],
                'timestamp' => now(),
                'connection' => 'sqlite',
            ],
        ]);

        $stats = $service->getTableUsageStatistics();

        $this->assertArrayHasKey('orders', $stats);
        $this->assertArrayHasKey('customers', $stats);
        $this->assertArrayNotHasKey('recent_orders', $stats);
    }

    private function freshService(): QueryMonitoringService
    {
        $this->app->forgetInstance(QueryMonitoringService::class);

        return $this->app->make(QueryMonitoringService::class);
    }
}
