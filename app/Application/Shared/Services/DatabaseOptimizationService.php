<?php

namespace App\Application\Shared\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DatabaseOptimizationService
{
    public function optimizeTables(): array
    {
        $results = [];
        $tables = $this->getAllTables();
        $driver = $this->driver();
        $tableCount = count($tables);

        try {
            if (! $this->supportsMysqlMaintenance()) {
                $summary = $this->summaryResult(
                    'optimize',
                    'skipped',
                    $driver,
                    $tableCount,
                    'Optimize table is not supported on this database driver.'
                );

                $this->logOperationSummary('Database optimization completed', $summary);

                return ['__summary' => $summary];
            }

            foreach ($tables as $table) {
                $results[$table] = $this->optimizeTable($table);
            }

            $this->logOperationSummary(
                'Database optimization completed',
                $this->summaryResult('optimize', $this->aggregateResultStatus($results), $driver, $tableCount, '')
            );
        } catch (\Throwable $e) {
            Log::error('Database optimization failed', ['error' => $e->getMessage()]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    public function analyzeTables(): array
    {
        $results = [];
        $tables = $this->getAllTables();
        $driver = $this->driver();
        $tableCount = count($tables);

        try {
            if (! $this->supportsMysqlMaintenance()) {
                return $this->analyzeUnsupportedDriver($driver, $tableCount);
            }

            foreach ($tables as $table) {
                $results[$table] = $this->analyzeTable($table);
            }

            $this->logOperationSummary(
                'Database analysis completed',
                $this->summaryResult('analyze', $this->aggregateResultStatus($results), $driver, $tableCount, '')
            );
        } catch (\Throwable $e) {
            Log::error('Database analysis failed', ['error' => $e->getMessage()]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    public function addMissingIndexes(): array
    {
        $results = [];

        foreach ($this->recommendedIndexes() as $table => $tableIndexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($tableIndexes as $indexName => $columns) {
                $columns = (array) $columns;

                if (! $this->tableHasColumns($table, $columns)) {
                    $results[$table][] = "Skipped index {$indexName}: required columns are missing.";
                    continue;
                }

                try {
                    if ($this->indexExists($table, $indexName) || $this->equivalentIndexExists($table, $columns)) {
                        $results[$table][] = "Index already exists: {$indexName}";
                        continue;
                    }

                    $this->addIndex($table, $indexName, $columns);
                    $results[$table][] = "Added index: {$indexName}";
                } catch (\Throwable $e) {
                    $results[$table][] = "Failed to add index {$indexName}: " . $e->getMessage();
                }
            }
        }

        return $results;
    }

    public function getSlowQueries(): array
    {
        if (! $this->supportsPerformanceSchema()) {
            return [];
        }

        try {
            return DB::select("
                SELECT
                    DIGEST_TEXT as query,
                    COUNT_STAR as exec_count,
                    AVG_TIMER_WAIT / 1000000000000 as avg_time_seconds,
                    MAX_TIMER_WAIT / 1000000000000 as max_time_seconds,
                    SUM_ROWS_EXAMINED as total_rows_examined
                FROM performance_schema.events_statements_summary_by_digest
                WHERE AVG_TIMER_WAIT > 1000000000
                ORDER BY AVG_TIMER_WAIT DESC
                LIMIT 10
            ");
        } catch (\Throwable $e) {
            Log::warning('Failed to get slow queries', ['error' => $e->getMessage()]);

            return [];
        }
    }

    public function checkIndexes(): array
    {
        $results = [];

        try {
            foreach ($this->getAllTables() as $table) {
                $results[$table] = $this->listIndexes($table);
            }
        } catch (\Throwable $e) {
            Log::error('Index inspection failed', ['error' => $e->getMessage()]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    public function getTableStats(): array
    {
        $results = [];

        try {
            foreach ($this->getAllTables() as $table) {
                $results[$table] = (object) $this->tableStats($table);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to get table statistics', ['error' => $e->getMessage()]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    public function createMissingIndexes(): array
    {
        $results = [];

        try {
            foreach ($this->targetedMissingIndexes() as $table => $columns) {
                if (! Schema::hasTable($table)) {
                    continue;
                }

                foreach ($columns as $column => $indexName) {
                    if (! $this->schemaHasColumn($table, $column)) {
                        $results[$table][$column] = 'Column is missing';
                        continue;
                    }

                    if ($this->indexExists($table, $indexName)) {
                        $results[$table][$column] = 'Index already exists';
                        continue;
                    }

                    if ($this->equivalentIndexExists($table, [$column])) {
                        $results[$table][$column] = 'Equivalent index already exists';
                        continue;
                    }

                    try {
                        $this->addIndex($table, $indexName, [$column]);
                        $results[$table][$column] = 'Index created';
                    } catch (\Throwable $e) {
                        $results[$table][$column] = 'Failed: ' . $e->getMessage();
                    }
                }
            }

            Log::info('Missing indexes check completed');
        } catch (\Throwable $e) {
            Log::error('Failed to create missing indexes', ['error' => $e->getMessage()]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    protected function getAllTables(): array
    {
        try {
            return collect(Schema::getTableListing())
                ->filter(fn ($table) => is_string($table) && $table !== '')
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Failed to list database tables', ['error' => $e->getMessage()]);

            return [];
        }
    }

    protected function optimizeTable(string $table): array
    {
        if (! $this->supportsMysqlMaintenance()) {
            return [
                'operation' => 'optimize',
                'status' => 'skipped',
                'message' => 'Optimize table is not supported on this database driver.',
            ];
        }

        try {
            DB::statement('OPTIMIZE TABLE ' . $this->quoteIdentifier($table));

            return [
                'operation' => 'optimize',
                'status' => 'ok',
                'message' => 'Optimized successfully',
            ];
        } catch (\Throwable $e) {
            return [
                'operation' => 'optimize',
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function analyzeTable(string $table): array
    {
        if (! $this->supportsMysqlMaintenance()) {
            return [
                'operation' => 'analyze',
                'status' => 'skipped',
                'message' => 'Analyze table is not supported on this database driver.',
            ];
        }

        try {
            DB::select('ANALYZE TABLE ' . $this->quoteIdentifier($table));

            return [
                'operation' => 'analyze',
                'status' => 'ok',
                'message' => 'Analyze completed successfully',
            ];
        } catch (\Throwable $e) {
            return [
                'operation' => 'analyze',
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function analyzeUnsupportedDriver(string $driver, int $tableCount): array
    {
        try {
            $this->executeGenericAnalyzeStatement();

            $summary = $this->summaryResult(
                'analyze',
                'ok',
                $driver,
                $tableCount,
                'Analyze completed successfully.'
            );
        } catch (\Throwable) {
            $summary = $this->summaryResult(
                'analyze',
                'skipped',
                $driver,
                $tableCount,
                'Analyze is not supported on this database driver.'
            );
        }

        $this->logOperationSummary('Database analysis completed', $summary);

        return ['__summary' => $summary];
    }

    protected function executeGenericAnalyzeStatement(): void
    {
        DB::statement('ANALYZE');
    }

    protected function indexExists(string $table, string $indexName): bool
    {
        foreach ($this->listIndexes($table) as $index) {
            $name = $index->name
                ?? $index->Key_name
                ?? $index->indexname
                ?? null;

            if ($name === $indexName) {
                return true;
            }
        }

        return false;
    }

    protected function equivalentIndexExists(string $table, array $columns): bool
    {
        $normalizedColumns = array_values($columns);
        $allIndexes = $this->listIndexes($table);

        if (in_array($this->driver(), ['mysql', 'mariadb'], true)) {
            foreach ($this->groupMysqlIndexColumns($allIndexes) as $indexedColumns) {
                if ($indexedColumns === $normalizedColumns) {
                    return true;
                }
            }

            return false;
        }

        foreach ($allIndexes as $index) {
            $indexedColumns = $this->extractIndexColumns($table, $index);

            if ($indexedColumns === $normalizedColumns) {
                return true;
            }
        }

        return false;
    }

    protected function groupMysqlIndexColumns(array $indexes): array
    {
        $grouped = [];

        foreach ($indexes as $index) {
            $name = $index->Key_name ?? $index->name ?? $index->indexname ?? null;
            $column = $index->Column_name ?? null;
            $sequence = (int) ($index->Seq_in_index ?? 0);

            if (! is_string($name) || $name === '' || ! is_string($column) || $column === '') {
                continue;
            }

            $grouped[$name][$sequence > 0 ? $sequence : count($grouped[$name] ?? []) + 1] = $column;
        }

        return collect($grouped)
            ->map(function (array $columns) {
                ksort($columns);

                return array_values($columns);
            })
            ->values()
            ->all();
    }

    protected function listIndexes(string $table): array
    {
        if (! Schema::hasTable($table)) {
            return [];
        }

        try {
            return match ($this->driver()) {
                'mysql', 'mariadb' => DB::select('SHOW INDEX FROM ' . $this->quoteIdentifier($table)),
                'sqlite' => DB::select("PRAGMA index_list('" . str_replace("'", "''", $table) . "')"),
                'pgsql' => DB::select(
                    'SELECT indexname, indexdef FROM pg_indexes WHERE schemaname = current_schema() AND tablename = ?',
                    [$table]
                ),
                default => [],
            };
        } catch (\Throwable $e) {
            Log::warning('Failed to inspect indexes', [
                'table' => $table,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    protected function extractIndexColumns(string $table, object $index): array
    {
        return match ($this->driver()) {
            'mysql', 'mariadb' => $this->extractMysqlIndexColumns($index),
            'sqlite' => $this->extractSqliteIndexColumns($table, $index),
            'pgsql' => $this->extractPgsqlIndexColumns($index),
            default => [],
        };
    }

    protected function extractMysqlIndexColumns(object $index): array
    {
        $column = $index->Column_name ?? null;

        return is_string($column) && $column !== '' ? [$column] : [];
    }

    protected function extractSqliteIndexColumns(string $table, object $index): array
    {
        $indexName = $index->name ?? null;

        if (! is_string($indexName) || $indexName === '') {
            return [];
        }

        try {
            return collect(DB::select("PRAGMA index_info('" . str_replace("'", "''", $indexName) . "')"))
                ->sortBy(fn ($column) => (int) ($column->seqno ?? 0))
                ->pluck('name')
                ->filter(fn ($column) => is_string($column) && $column !== '')
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Failed to inspect SQLite index columns', [
                'table' => $table,
                'index' => $indexName,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    protected function extractPgsqlIndexColumns(object $index): array
    {
        $definition = $index->indexdef ?? null;

        if (! is_string($definition)) {
            return [];
        }

        if (! preg_match('/\((.+)\)/', $definition, $matches)) {
            return [];
        }

        return collect(explode(',', $matches[1]))
            ->map(fn ($column) => trim($column))
            ->map(function ($column) {
                $column = preg_replace('/\s+(asc|desc)\b/i', '', $column);
                $column = trim((string) $column, "\"`[] ");

                return $column;
            })
            ->filter(fn ($column) => $column !== '')
            ->values()
            ->all();
    }

    protected function tableStats(string $table): array
    {
        $rowCount = 0;

        try {
            $rowCount = DB::table($table)->count();
        } catch (\Throwable) {
            $rowCount = 0;
        }

        if ($this->supportsMysqlMaintenance()) {
            try {
                $stats = DB::selectOne(
                    '
                        SELECT
                            table_rows,
                            data_length,
                            index_length,
                            data_free
                        FROM information_schema.tables
                        WHERE table_schema = DATABASE() AND table_name = ?
                    ',
                    [$table]
                );

                return [
                    'table_rows' => (int) ($stats->table_rows ?? $rowCount),
                    'data_length' => (int) ($stats->data_length ?? 0),
                    'index_length' => (int) ($stats->index_length ?? 0),
                    'total_size' => (int) (($stats->data_length ?? 0) + ($stats->index_length ?? 0)),
                    'data_free' => (int) ($stats->data_free ?? 0),
                ];
            } catch (\Throwable $e) {
                Log::warning('Failed to inspect MySQL table statistics', [
                    'table' => $table,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'table_rows' => $rowCount,
            'data_length' => 0,
            'index_length' => 0,
            'total_size' => 0,
            'data_free' => 0,
        ];
    }

    protected function addIndex(string $table, string $indexName, array $columns): void
    {
        if (! $this->tableHasColumns($table, $columns)) {
            throw new \RuntimeException('Required columns are missing.');
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName): void {
            $blueprint->index($columns, $indexName);
        });
    }

    protected function recommendedIndexes(): array
    {
        return [
            'products' => [
                'idx_products_slug' => 'slug',
                'idx_products_sku' => 'sku',
                'idx_products_brand_category' => ['brand_id', 'primary_category_id'],
                'idx_products_active_featured' => ['is_active', 'is_featured'],
                'idx_products_price_range' => ['price', 'sale_price'],
                'idx_products_stock' => 'stock_quantity',
                'idx_products_views' => 'views_count',
                'idx_products_created' => 'created_at',
            ],
            'product_categories' => [
                'idx_categories_slug' => 'slug',
                'idx_categories_parent' => 'parent_id',
                'idx_categories_active_order' => ['is_active', 'display_order'],
            ],
            'brands' => [
                'idx_brands_slug' => 'slug',
                'idx_brands_active_order' => ['is_active', 'display_order'],
            ],
            'orders' => [
                'idx_orders_customer_status' => ['customer_id', 'status'],
                'idx_orders_status' => 'status',
                'idx_orders_placed_at' => 'placed_at',
                'idx_orders_total' => 'total',
            ],
            'order_items' => [
                'idx_order_items_order' => 'order_id',
                'idx_order_items_product' => 'product_id',
            ],
            'customers' => [
                'idx_customers_email' => 'email',
                'idx_customers_phone' => 'phone',
                'idx_customers_active' => 'is_active',
                'idx_customers_loyalty' => 'loyalty_points_balance',
            ],
            'product_media' => [
                'idx_product_media_product' => 'product_id',
                'idx_product_media_collection' => 'collection',
            ],
            'product_reviews' => [
                'idx_reviews_product' => 'product_id',
                'idx_reviews_customer' => 'customer_id',
                'idx_reviews_status' => 'status',
                'idx_reviews_rating' => 'rating',
            ],
            'blog_posts' => [
                'idx_posts_slug' => 'slug',
                'idx_posts_category' => 'blog_category_id',
                'idx_posts_status_published' => ['status', 'published_at'],
                'idx_posts_featured' => 'is_featured',
            ],
            'coupons' => [
                'idx_coupons_code' => 'code',
                'idx_coupons_active' => 'is_active',
                'idx_coupons_expires' => 'expires_at',
            ],
        ];
    }

    protected function targetedMissingIndexes(): array
    {
        return [
            'products' => [
                'is_active' => 'idx_products_is_active',
                'primary_category_id' => 'idx_products_category',
                'brand_id' => 'idx_products_brand',
                'price' => 'idx_products_price',
            ],
            'orders' => [
                'customer_id' => 'idx_orders_customer',
                'status' => 'idx_orders_status',
                'created_at' => 'idx_orders_created',
            ],
            'customers' => [
                'email' => 'idx_customers_email',
                'phone' => 'idx_customers_phone',
                'is_active' => 'idx_customers_active',
            ],
        ];
    }

    protected function tableHasColumns(string $table, array $columns): bool
    {
        foreach ($columns as $column) {
            if (! $this->schemaHasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    protected function schemaHasColumn(string $table, string $column): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable) {
            return false;
        }
    }

    protected function supportsMysqlMaintenance(): bool
    {
        return in_array($this->driver(), ['mysql', 'mariadb'], true);
    }

    protected function supportsPerformanceSchema(): bool
    {
        return $this->supportsMysqlMaintenance();
    }

    protected function driver(): string
    {
        return DB::getDriverName();
    }

    protected function quoteIdentifier(string $identifier): string
    {
        if (! preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
            throw new \InvalidArgumentException('Unsafe identifier provided.');
        }

        return match ($this->driver()) {
            'pgsql' => '"' . $identifier . '"',
            default => '`' . $identifier . '`',
        };
    }

    private function aggregateResultStatus(array $results): string
    {
        $statuses = collect($results)
            ->pluck('status')
            ->filter(fn ($status) => is_string($status) && $status !== '')
            ->values()
            ->all();

        if (in_array('error', $statuses, true)) {
            return 'error';
        }

        if (in_array('skipped', $statuses, true)) {
            return 'skipped';
        }

        return 'ok';
    }

    private function summaryResult(
        string $operation,
        string $status,
        string $driver,
        int $tableCount,
        string $message
    ): array {
        return [
            'operation' => $operation,
            'status' => $status,
            'driver' => $driver,
            'table_count' => $tableCount,
            'message' => $message,
        ];
    }

    private function logOperationSummary(string $message, array $summary): void
    {
        Log::info($message, [
            'driver' => $summary['driver'] ?? 'unknown',
            'table_count' => $summary['table_count'] ?? 0,
            'status' => $summary['status'] ?? 'unknown',
        ]);
    }
}
