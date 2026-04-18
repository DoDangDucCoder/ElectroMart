<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Events\QueryExecuted;

class QueryMonitoringService
{
    private array $queries = [];
    private array $slowQueries = [];
    private float $slowQueryThreshold = 1.0; // seconds
    private bool $isMonitoring = false;
    private bool $listenerRegistered = false;

    public function startMonitoring(): void
    {
        $this->isMonitoring = true;
        $this->queries = [];
        $this->slowQueries = [];
        
        // Enable query logging
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Listen to query executed events
        if (! $this->listenerRegistered) {
            DB::listen(function (QueryExecuted $query) {
                $this->recordQuery($query);
            });

            $this->listenerRegistered = true;
        }
        
        Log::info('Query monitoring started');
    }

    public function stopMonitoring(): void
    {
        $this->isMonitoring = false;
        DB::disableQueryLog();
        Log::info('Query monitoring stopped');
    }

    private function recordQuery(QueryExecuted $query): void
    {
        if (!$this->isMonitoring) {
            return;
        }

        $executionTime = $query->time / 1000; // Convert to seconds
        $sql = $query->sql;
        $bindings = $query->bindings;

        $queryData = [
            'sql' => $sql,
            'bindings' => $bindings,
            'execution_time' => $executionTime,
            'timestamp' => now(),
            'connection' => $query->connectionName,
        ];

        $this->queries[] = $queryData;

        // Check if it's a slow query
        if ($executionTime > $this->slowQueryThreshold) {
            $this->slowQueries[] = $queryData;
            
            Log::warning('Slow query detected', [
                'sql' => $sql,
                'execution_time' => $executionTime,
                'bindings' => $bindings
            ]);
        }
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function getSlowQueries(): array
    {
        return $this->slowQueries;
    }

    public function getQueryStatistics(): array
    {
        if (empty($this->queries)) {
            return [
                'total_queries' => 0,
                'slow_queries' => 0,
                'average_execution_time' => 0,
                'total_execution_time' => 0,
                'slow_query_percentage' => 0,
            ];
        }

        $totalQueries = count($this->queries);
        $slowQueries = count($this->slowQueries);
        $totalExecutionTime = array_sum(array_column($this->queries, 'execution_time'));
        $averageExecutionTime = $totalExecutionTime / $totalQueries;

        return [
            'total_queries' => $totalQueries,
            'slow_queries' => $slowQueries,
            'average_execution_time' => round($averageExecutionTime, 4),
            'total_execution_time' => round($totalExecutionTime, 4),
            'slow_query_percentage' => round(($slowQueries / $totalQueries) * 100, 2),
        ];
    }

    public function getTopSlowQueries(int $limit = 10): array
    {
        $slowQueries = $this->slowQueries;
        
        // Sort by execution time descending
        usort($slowQueries, function ($a, $b) {
            return $b['execution_time'] <=> $a['execution_time'];
        });

        return array_slice($slowQueries, 0, $limit);
    }

    public function getDuplicateQueries(): array
    {
        $queryCounts = [];
        
        foreach ($this->queries as $query) {
            $sql = $query['sql'];
            if (!isset($queryCounts[$sql])) {
                $queryCounts[$sql] = [
                    'sql' => $sql,
                    'count' => 0,
                    'total_time' => 0,
                    'average_time' => 0,
                ];
            }
            
            $queryCounts[$sql]['count']++;
            $queryCounts[$sql]['total_time'] += $query['execution_time'];
        }

        // Calculate average execution time
        foreach ($queryCounts as &$queryData) {
            $queryData['average_time'] = $queryData['total_time'] / $queryData['count'];
        }

        $queryCounts = array_filter($queryCounts, function ($queryData) {
            return ($queryData['count'] ?? 0) > 1;
        });

        // Sort by count descending
        uasort($queryCounts, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $queryCounts;
    }

    public function analyzeQueryPatterns(): array
    {
        $patterns = [
            'select_queries' => 0,
            'insert_queries' => 0,
            'update_queries' => 0,
            'delete_queries' => 0,
            'join_queries' => 0,
            'subquery_queries' => 0,
            'order_by_queries' => 0,
            'group_by_queries' => 0,
            'limit_queries' => 0,
        ];

        foreach ($this->queries as $query) {
            $sql = strtolower($query['sql']);
            $normalizedSql = $this->normalizeSqlPrefix($sql);
            
            if ($this->isSelectQuery($normalizedSql)) {
                $patterns['select_queries']++;
            }
            if (str_starts_with($normalizedSql, 'insert')) {
                $patterns['insert_queries']++;
            }
            if (str_starts_with($normalizedSql, 'update')) {
                $patterns['update_queries']++;
            }
            if (str_starts_with($normalizedSql, 'delete')) {
                $patterns['delete_queries']++;
            }
            if (strpos($sql, ' join ') !== false) {
                $patterns['join_queries']++;
            }
            if (strpos($sql, '(select') !== false) {
                $patterns['subquery_queries']++;
            }
            if (strpos($sql, ' order by ') !== false) {
                $patterns['order_by_queries']++;
            }
            if (strpos($sql, ' group by ') !== false) {
                $patterns['group_by_queries']++;
            }
            if (strpos($sql, ' limit ') !== false) {
                $patterns['limit_queries']++;
            }
        }

        return $patterns;
    }

    private function normalizeSqlPrefix(string $sql): string
    {
        return ltrim($sql);
    }

    private function isSelectQuery(string $sql): bool
    {
        if (str_starts_with($sql, 'select')) {
            return true;
        }

        return preg_match('/^with\b[\s\S]*?\bselect\b/i', $sql) === 1;
    }

    public function getTableUsageStatistics(): array
    {
        $tableStats = [];
        
        foreach ($this->queries as $query) {
            $sql = strtolower($query['sql']);
            
            // Extract table names from SQL
            $tables = $this->extractTableNames($sql);
            
            foreach ($tables as $table) {
                if (!isset($tableStats[$table])) {
                    $tableStats[$table] = [
                        'table' => $table,
                        'query_count' => 0,
                        'total_time' => 0,
                        'average_time' => 0,
                    ];
                }
                
                $tableStats[$table]['query_count']++;
                $tableStats[$table]['total_time'] += $query['execution_time'];
            }
        }

        // Calculate average execution time
        foreach ($tableStats as &$stats) {
            $stats['average_time'] = $stats['total_time'] / $stats['query_count'];
        }

        // Sort by query count descending
        uasort($tableStats, function ($a, $b) {
            return $b['query_count'] <=> $a['query_count'];
        });

        return $tableStats;
    }

    private function extractTableNames(string $sql): array
    {
        $tables = [];
        $cteAliases = $this->extractCteAliases($sql);
        
        // Simple regex to extract table names
        $patterns = [
            '/from\s+(?:"|`|\[)?(?:(?:\w+)(?:"|`|\])?\.)?(?:"|`|\[)?(\w+)(?:"|`|\])?/i',
            '/join\s+(?:"|`|\[)?(?:(?:\w+)(?:"|`|\])?\.)?(?:"|`|\[)?(\w+)(?:"|`|\])?/i',
            '/update\s+(?:"|`|\[)?(?:(?:\w+)(?:"|`|\])?\.)?(?:"|`|\[)?(\w+)(?:"|`|\])?/i',
            '/insert\s+into\s+(?:"|`|\[)?(?:(?:\w+)(?:"|`|\])?\.)?(?:"|`|\[)?(\w+)(?:"|`|\])?/i',
            '/delete\s+from\s+(?:"|`|\[)?(?:(?:\w+)(?:"|`|\])?\.)?(?:"|`|\[)?(\w+)(?:"|`|\])?/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $sql, $matches)) {
                foreach ($matches[1] as $table) {
                    if (in_array($table, $cteAliases, true)) {
                        continue;
                    }

                    $tables[] = $table;
                }
            }
        }

        return array_unique($tables);
    }

    private function extractCteAliases(string $sql): array
    {
        if (! str_starts_with(ltrim($sql), 'with')) {
            return [];
        }

        preg_match_all('/(?:with|,)\s+(?:"|`|\[)?(\w+)(?:"|`|\])?\s+as\s*\(/i', $sql, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    public function generateOptimizationRecommendations(): array
    {
        $recommendations = [];
        $stats = $this->getQueryStatistics();
        $patterns = $this->analyzeQueryPatterns();
        $duplicates = $this->getDuplicateQueries();

        // Slow query recommendations
        if ($stats['slow_query_percentage'] > 20) {
            $recommendations[] = [
                'type' => 'performance',
                'priority' => 'high',
                'message' => 'High percentage of slow queries detected. Consider adding indexes or optimizing queries.',
                'percentage' => $stats['slow_query_percentage']
            ];
        }

        // Duplicate query recommendations
        $topDuplicates = array_slice($duplicates, 0, 5);
        foreach ($topDuplicates as $duplicate) {
            if ($duplicate['count'] > 10) {
                $recommendations[] = [
                    'type' => 'optimization',
                    'priority' => 'medium',
                    'message' => "Query executed {$duplicate['count']} times. Consider caching or query optimization.",
                    'query' => substr($duplicate['sql'], 0, 100) . '...',
                    'count' => $duplicate['count']
                ];
            }
        }

        // Pattern-based recommendations
        if ($patterns['select_queries'] > $patterns['insert_queries'] * 10) {
            $recommendations[] = [
                'type' => 'read_heavy',
                'priority' => 'low',
                'message' => 'Read-heavy workload detected. Consider read replicas or caching.',
                'select_ratio' => $patterns['select_queries'] / max($patterns['insert_queries'], 1)
            ];
        }

        if ($patterns['join_queries'] > 0) {
            $joinPercentage = ($patterns['join_queries'] / count($this->queries)) * 100;
            if ($joinPercentage > 30) {
                $recommendations[] = [
                    'type' => 'joins',
                    'priority' => 'medium',
                    'message' => 'High percentage of JOIN queries. Consider denormalization or query optimization.',
                    'join_percentage' => $joinPercentage
                ];
            }
        }

        return $recommendations;
    }

    public function exportQueryReport(): array
    {
        return [
            'timestamp' => now(),
            'statistics' => $this->getQueryStatistics(),
            'patterns' => $this->analyzeQueryPatterns(),
            'table_usage' => $this->getTableUsageStatistics(),
            'top_slow_queries' => $this->getTopSlowQueries(10),
            'duplicate_queries' => array_slice($this->getDuplicateQueries(), 0, 10),
            'recommendations' => $this->generateOptimizationRecommendations(),
        ];
    }

    public function clearData(): void
    {
        $this->queries = [];
        $this->slowQueries = [];
        Log::info('Query monitoring data cleared');
    }

    public function setSlowQueryThreshold(float $threshold): void
    {
        $this->slowQueryThreshold = $threshold;
        Log::info('Slow query threshold updated', ['threshold' => $threshold]);
    }

    public function getSlowQueryThreshold(): float
    {
        return $this->slowQueryThreshold;
    }

    public function isMonitoring(): bool
    {
        return $this->isMonitoring;
    }
}
