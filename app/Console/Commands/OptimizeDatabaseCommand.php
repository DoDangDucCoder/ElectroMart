<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\Shared\Services\DatabaseOptimizationService;

class OptimizeDatabaseCommand extends Command
{
    protected $signature = 'db:optimize 
                            {--analyze : Only analyze tables}
                            {--optimize : Only optimize tables}
                            {--indexes : Check indexes}
                            {--stats : Show table statistics}
                            {--slow-queries : Show slow queries}
                            {--missing-indexes : Create missing indexes}';

    protected $description = 'Optimize database performance with indexes, analysis, and maintenance';

    public function handle(DatabaseOptimizationService $dbService): int
    {
        $this->info('Starting database optimization...');

        try {
            if ($this->option('analyze')) {
                $this->analyzeTables($dbService);
            } elseif ($this->option('optimize')) {
                $this->optimizeTables($dbService);
            } elseif ($this->option('indexes')) {
                $this->checkIndexes($dbService);
            } elseif ($this->option('stats')) {
                $this->showTableStats($dbService);
            } elseif ($this->option('slow-queries')) {
                $this->showSlowQueries($dbService);
            } elseif ($this->option('missing-indexes')) {
                $this->createMissingIndexes($dbService);
            } else {
                // Run all optimizations
                $this->runFullOptimization($dbService);
            }

            $this->info('Database optimization completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Database optimization failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function analyzeTables(DatabaseOptimizationService $dbService): void
    {
        $this->info('Analyzing database tables...');
        
        $results = $dbService->analyzeTables();
        
        if (isset($results['error'])) {
            $this->error('Analysis failed: ' . $results['error']);
            return;
        }

        if ($this->renderSummaryIfPresent($results)) {
            return;
        }

        $this->table(
            ['Table', 'Operation', 'Status', 'Message'],
            collect($results)->map(function ($result, $table) {
                return [
                    $table,
                    data_get($result, 'operation', data_get($result, 'Op', 'N/A')),
                    data_get($result, 'status', data_get($result, 'Msg_type', 'N/A')),
                    data_get($result, 'message', data_get($result, 'Msg_text', is_string($result) ? $result : 'N/A')),
                ];
            })->toArray()
        );
    }

    private function optimizeTables(DatabaseOptimizationService $dbService): void
    {
        $this->info('Optimizing database tables...');
        
        $results = $dbService->optimizeTables();
        
        if (isset($results['error'])) {
            $this->error('Optimization failed: ' . $results['error']);
            return;
        }

        if ($this->renderSummaryIfPresent($results)) {
            return;
        }

        $this->table(
            ['Table', 'Operation', 'Status', 'Message'],
            collect($results)->map(function ($result, $table) {
                return [
                    $table,
                    data_get($result, 'operation', data_get($result, 'Op', 'N/A')),
                    data_get($result, 'status', data_get($result, 'Msg_type', 'N/A')),
                    data_get($result, 'message', data_get($result, 'Msg_text', is_string($result) ? $result : 'N/A')),
                ];
            })->toArray()
        );
    }

    private function checkIndexes(DatabaseOptimizationService $dbService): void
    {
        $this->info('Checking database indexes...');
        
        $results = $dbService->checkIndexes();
        
        if (isset($results['error'])) {
            $this->error('Index check failed: ' . $results['error']);
            return;
        }

        foreach ($results as $table => $indexes) {
            $this->info("Table: {$table}");
            $this->table(['Key_name', 'Column_name', 'Index_type', 'Cardinality'], 
                collect($indexes)->map(function ($index) {
                    return [
                        $index->Key_name ?? $index->name ?? $index->indexname ?? 'N/A',
                        $index->Column_name ?? data_get($index, 'origin', 'N/A'),
                        $index->Index_type ?? data_get($index, 'unique', 'N/A'),
                        $index->Cardinality ?? data_get($index, 'partial', 'N/A'),
                    ];
                })->toArray()
            );
            $this->line('');
        }
    }

    private function showTableStats(DatabaseOptimizationService $dbService): void
    {
        $this->info('Showing table statistics...');
        
        $stats = $dbService->getTableStats();
        
        if (isset($stats['error'])) {
            $this->error('Failed to get table stats: ' . $stats['error']);
            return;
        }

        $this->table(['Table', 'Rows', 'Data Size', 'Index Size', 'Total Size', 'Free Space'], 
            collect($stats)->map(function ($stat, $table) {
                return [
                    $table,
                    number_format($stat->table_rows ?? 0),
                    $this->formatBytes($stat->data_length ?? 0),
                    $this->formatBytes($stat->index_length ?? 0),
                    $this->formatBytes($stat->total_size ?? 0),
                    $this->formatBytes($stat->data_free ?? 0)
                ];
            })->toArray()
        );
    }

    private function showSlowQueries(DatabaseOptimizationService $dbService): void
    {
        $this->info('Showing slow queries...');
        
        $queries = $dbService->getSlowQueries();
        
        if (isset($queries['error'])) {
            $this->error('Failed to get slow queries: ' . $queries['error']);
            return;
        }

        if (empty($queries)) {
            $this->info('No slow queries found.');
            return;
        }

        $this->table(['Query', 'Exec Count', 'Avg Time (s)', 'Max Time (s)', 'Rows Examined'], 
            collect($queries)->map(function ($query) {
                return [
                    substr($query->query ?? $query->sql_text ?? 'N/A', 0, 50) . '...',
                    number_format($query->exec_count ?? 0),
                    number_format($query->avg_time_seconds ?? 0, 4),
                    number_format($query->max_time_seconds ?? 0, 4),
                    number_format($query->total_rows_examined ?? 0)
                ];
            })->toArray()
        );
    }

    private function createMissingIndexes(DatabaseOptimizationService $dbService): void
    {
        $this->info('Creating missing indexes...');
        
        $results = $dbService->createMissingIndexes();
        
        if (isset($results['error'])) {
            $this->error('Failed to create missing indexes: ' . $results['error']);
            return;
        }

        foreach ($results as $table => $columns) {
            if ($table === 'error') continue;
            
            $this->info("Table: {$table}");
            foreach ($columns as $column => $result) {
                $status = $result === 'Index created' ? '✅' : '❌';
                $this->line("  {$status} {$column}: {$result}");
            }
            $this->line('');
        }
    }

    private function runFullOptimization(DatabaseOptimizationService $dbService): void
    {
        $this->info('Running full database optimization...');
        
        // Step 1: Analyze tables
        $this->info('Step 1: Analyzing tables...');
        $this->analyzeTables($dbService);
        
        // Step 2: Create missing indexes
        $this->info('Step 2: Creating missing indexes...');
        $this->createMissingIndexes($dbService);
        
        // Step 3: Optimize tables
        $this->info('Step 3: Optimizing tables...');
        $this->optimizeTables($dbService);
        
        // Step 4: Show statistics
        $this->info('Step 4: Showing table statistics...');
        $this->showTableStats($dbService);
        
        $this->info('Full optimization completed!');
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

    private function renderSummaryIfPresent(array $results): bool
    {
        $summary = $results['__summary'] ?? null;

        if (! is_array($summary)) {
            return false;
        }

        $this->renderSummary($summary);

        return true;
    }

    private function renderSummary(array $summary): void
    {
        $line = $this->formatSummaryLine($summary);

        if (($summary['status'] ?? null) === 'ok') {
            $this->info($line);
            return;
        }

        if (($summary['status'] ?? null) === 'error') {
            $this->error($line);
            return;
        }

        $this->warn($line);
    }

    private function formatSummaryLine(array $summary): string
    {
        $operation = (string) ($summary['operation'] ?? 'operation');
        $status = (string) ($summary['status'] ?? 'unknown');
        $driver = (string) ($summary['driver'] ?? 'unknown');
        $tableCount = (int) ($summary['table_count'] ?? 0);
        $message = trim((string) ($summary['message'] ?? ''));
        $tableLabel = $tableCount === 1 ? 'table' : 'tables';

        $line = "Database {$operation} {$status} for {$driver} driver across {$tableCount} {$tableLabel}";

        if ($message !== '') {
            $line .= ": {$message}";
        }

        return $line;
    }
}
