<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class QueryOptimizationService
{
    private array $queryCache = [];
    private array $slowQueries = [];
    private float $slowQueryThreshold = 1.0; // seconds

    public function optimizeQuery(Builder $query): Builder
    {
        $this->startQueryTimer();
        
        // Apply query optimizations
        $this->applyEagerLoading($query);
        $this->applySelectOptimization($query);
        $this->applyWhereOptimization($query);
        $this->applyOrderByOptimization($query);
        
        return $query;
    }

    public function executeWithOptimization(Builder $query, string $cacheKey = null, int $cacheTtl = 3600)
    {
        $startTime = microtime(true);
        
        // Check cache first
        if ($cacheKey && $this->hasCache($cacheKey)) {
            Log::info('Query served from cache', ['key' => $cacheKey]);
            return $this->getFromCache($cacheKey);
        }
        
        // Execute optimized query
        $optimizedQuery = $this->optimizeQuery($query);
        $result = $optimizedQuery->get();
        
        $executionTime = microtime(true) - $startTime;
        
        // Log slow queries
        if ($executionTime > $this->slowQueryThreshold) {
            $this->logSlowQuery($optimizedQuery->toSql(), $executionTime, $optimizedQuery->getBindings());
        }
        
        // Cache result if key provided
        if ($cacheKey) {
            $this->putToCache($cacheKey, $result, $cacheTtl);
        }
        
        Log::info('Query executed', [
            'sql' => $optimizedQuery->toSql(),
            'time' => $executionTime,
            'cached' => $cacheKey !== null
        ]);
        
        return $result;
    }

    private function applyEagerLoading(Builder $query): void
    {
        $model = $query->getModel();
        
        // Define common relationships to eager load
        $commonRelationships = $this->getCommonRelationships($model);
        
        if (!empty($commonRelationships)) {
            $query->with($commonRelationships);
        }
    }

    private function getCommonRelationships(Model $model): array
    {
        $relationships = [];
        
        switch (get_class($model)) {
            case \App\Infrastructure\Persistence\Eloquent\Catalog\Product::class:
                $relationships = ['brand', 'primaryCategory', 'media', 'reviews'];
                break;
                
            case \App\Infrastructure\Persistence\Eloquent\Order\Order::class:
                $relationships = ['customer', 'items', 'shipping', 'payment'];
                break;
                
            case \App\Infrastructure\Persistence\Eloquent\Customer\Customer::class:
                $relationships = ['addresses', 'orders'];
                break;
                
            case \App\Infrastructure\Persistence\Eloquent\Catalog\Category::class:
                $relationships = ['parent', 'children', 'products'];
                break;
        }
        
        return $relationships;
    }

    private function applySelectOptimization(Builder $query): void
    {
        // Only select necessary columns for better performance
        $model = $query->getModel();
        
        if (method_exists($model, 'getOptimizedColumns')) {
            $query->select($model->getOptimizedColumns());
        }
    }

    private function applyWhereOptimization(Builder $query): void
    {
        $wheres = $query->getQuery()->wheres;
        
        foreach ($wheres as $where) {
            // Optimize string comparisons
            if ($where['type'] === 'Basic' && is_string($where['value'])) {
                // Use LIKE with index-friendly patterns
                if (strpos($where['value'], '%') !== false) {
                    // Ensure LIKE queries use indexes properly
                    $this->optimizeLikeQuery($query, $where);
                }
            }
        }
    }

    private function applyOrderByOptimization(Builder $query): void
    {
        $orders = $query->getQuery()->orders;
        
        if (empty($orders)) {
            // Add default ordering for better performance
            $query->orderBy('id', 'desc');
        }
    }

    private function optimizeLikeQuery(Builder $query, array $where): void
    {
        // For LIKE queries, ensure they can use indexes
        $column = $where['column'];
        $value = $where['value'];
        
        // If LIKE starts with %, it can't use indexes efficiently
        if (strpos($value, '%') === 0) {
            Log::warning('Inefficient LIKE query detected', [
                'column' => $column,
                'value' => $value
            ]);
        }
    }

    private function logSlowQuery(string $sql, float $executionTime, array $bindings): void
    {
        $this->slowQueries[] = [
            'sql' => $sql,
            'time' => $executionTime,
            'bindings' => $bindings,
            'timestamp' => now()
        ];
        
        Log::warning('Slow query detected', [
            'sql' => $sql,
            'execution_time' => $executionTime,
            'bindings' => $bindings
        ]);
    }

    public function getSlowQueries(): array
    {
        return $this->slowQueries;
    }

    public function clearSlowQueries(): void
    {
        $this->slowQueries = [];
    }

    // Cache methods
    private function hasCache(string $key): bool
    {
        return isset($this->queryCache[$key]);
    }

    private function getFromCache(string $key)
    {
        return $this->queryCache[$key]['data'] ?? null;
    }

    private function putToCache(string $key, $data, int $ttl): void
    {
        $this->queryCache[$key] = [
            'data' => $data,
            'expires_at' => now()->addSeconds($ttl)
        ];
    }

    private function startQueryTimer(): void
    {
        // Enable query logging for this service
        DB::enableQueryLog();
    }

    public function getQueryLog(): array
    {
        return DB::getQueryLog();
    }

    public function clearQueryLog(): void
    {
        DB::flushQueryLog();
    }

    // Query analysis methods
    public function analyzeQueryPerformance(string $sql, array $bindings = []): array
    {
        try {
            // Use EXPLAIN to analyze query performance
            $explainQuery = "EXPLAIN " . $sql;
            $explanation = DB::select($explainQuery, $bindings);
            
            return [
                'explanation' => $explanation,
                'recommendations' => $this->generateRecommendations($explanation)
            ];
        } catch (\Exception $e) {
            Log::error('Failed to analyze query performance', [
                'sql' => $sql,
                'error' => $e->getMessage()
            ]);
            
            return ['error' => $e->getMessage()];
        }
    }

    private function generateRecommendations(array $explanation): array
    {
        $recommendations = [];
        
        foreach ($explanation as $row) {
            if ($row->type === 'ALL') {
                $recommendations[] = 'Consider adding an index on ' . $row->key;
            }
            
            if ($row->Extra && strpos($row->Extra, 'Using filesort') !== false) {
                $recommendations[] = 'Consider optimizing ORDER BY clause';
            }
            
            if ($row->Extra && strpos($row->Extra, 'Using temporary') !== false) {
                $recommendations[] = 'Consider optimizing GROUP BY or ORDER BY';
            }
        }
        
        return array_unique($recommendations);
    }

    // Bulk operations optimization
    public function optimizeBulkInsert(array $data, string $table): int
    {
        if (empty($data)) {
            return 0;
        }
        
        $chunkSize = 1000; // Process in chunks for better performance
        $inserted = 0;
        
        foreach (array_chunk($data, $chunkSize) as $chunk) {
            DB::table($table)->insert($chunk);
            $inserted += count($chunk);
        }
        
        Log::info('Bulk insert completed', [
            'table' => $table,
            'records' => $inserted
        ]);
        
        return $inserted;
    }

    public function optimizeBulkUpdate(array $updates, string $table, string $keyColumn = 'id'): int
    {
        if (empty($updates)) {
            return 0;
        }
        
        $updated = 0;
        
        foreach ($updates as $update) {
            $id = $update[$keyColumn];
            unset($update[$keyColumn]);
            
            DB::table($table)->where($keyColumn, $id)->update($update);
            $updated++;
        }
        
        Log::info('Bulk update completed', [
            'table' => $table,
            'records' => $updated
        ]);
        
        return $updated;
    }

    // Connection optimization
    public function optimizeConnection(): void
    {
        try {
            // Set optimal MySQL settings
            DB::statement("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
            DB::statement("SET SESSION innodb_buffer_pool_size = 134217728"); // 128MB
            DB::statement("SET SESSION query_cache_size = 67108864"); // 64MB
            DB::statement("SET SESSION tmp_table_size = 67108864"); // 64MB
            DB::statement("SET SESSION max_heap_table_size = 67108864"); // 64MB
            
            Log::info('Database connection optimized');
        } catch (\Exception $e) {
            Log::error('Failed to optimize database connection', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
