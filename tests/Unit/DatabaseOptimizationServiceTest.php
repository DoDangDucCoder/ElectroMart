<?php

namespace Tests\Unit;

use App\Application\Shared\Services\DatabaseOptimizationService;
use Tests\TestCase;

class DatabaseOptimizationServiceTest extends TestCase
{
    public function test_optimize_tables_returns_compact_summary_for_unsupported_drivers(): void
    {
        $service = new class extends DatabaseOptimizationService
        {
            public function driver(): string
            {
                return 'sqlite';
            }

            protected function getAllTables(): array
            {
                return ['users', 'orders'];
            }
        };

        $results = $service->optimizeTables();

        $this->assertSame([
            '__summary' => [
                'operation' => 'optimize',
                'status' => 'skipped',
                'driver' => 'sqlite',
                'table_count' => 2,
                'message' => 'Optimize table is not supported on this database driver.',
            ],
        ], $results);
    }

    public function test_analyze_tables_runs_generic_analyze_once_and_returns_summary(): void
    {
        $service = new class extends DatabaseOptimizationService
        {
            public int $genericAnalyzeCalls = 0;

            public function driver(): string
            {
                return 'sqlite';
            }

            protected function getAllTables(): array
            {
                return ['users', 'orders', 'products'];
            }

            protected function executeGenericAnalyzeStatement(): void
            {
                $this->genericAnalyzeCalls++;
            }
        };

        $results = $service->analyzeTables();

        $this->assertSame(1, $service->genericAnalyzeCalls);
        $this->assertSame([
            '__summary' => [
                'operation' => 'analyze',
                'status' => 'ok',
                'driver' => 'sqlite',
                'table_count' => 3,
                'message' => 'Analyze completed successfully.',
            ],
        ], $results);
    }

    public function test_equivalent_index_detection_supports_mysql_composite_indexes(): void
    {
        $service = new class extends DatabaseOptimizationService
        {
            public function driver(): string
            {
                return 'mysql';
            }

            protected function listIndexes(string $table): array
            {
                return [
                    (object) ['Key_name' => 'idx_orders_customer_status', 'Column_name' => 'customer_id', 'Seq_in_index' => 1],
                    (object) ['Key_name' => 'idx_orders_customer_status', 'Column_name' => 'status', 'Seq_in_index' => 2],
                ];
            }

            public function hasEquivalentIndex(string $table, array $columns): bool
            {
                return $this->equivalentIndexExists($table, $columns);
            }
        };

        $this->assertTrue($service->hasEquivalentIndex('orders', ['customer_id', 'status']));
        $this->assertFalse($service->hasEquivalentIndex('orders', ['status', 'customer_id']));
    }
}
