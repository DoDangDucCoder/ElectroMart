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

        $this->table(['Table', 'Operation', 'Msg_type', 'Msg_text'], 
            collect($results)->map(function ($result, $table) {
                return [
                    $table,
                    $result->Op ?? 'N/A',
                    $result->Msg_type ?? 'N/A',
                    $result->Msg_text ?? 'N/A'
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

        $this->table(['Table', 'Operation', 'Msg_type', 'Msg_text'], 
            collect($results)->map(function ($result, $table) {
                return [
                    $table,
                    $result->Op ?? 'N/A',
                    $result->Msg_type ?? 'N/A',
                    $result->Msg_text ?? 'N/A'
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
                        $index->Key_name ?? 'N/A',
                        $index->Column_name ?? 'N/A',
                        $index->Index_type ?? 'N/A',
                        $index->Cardinality ?? 'N/A'
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
                    substr($query->query ?? 'N/A', 0, 50) . '...',
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
}
