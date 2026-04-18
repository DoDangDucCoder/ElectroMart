<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\Shared\Services\CacheService;
use App\Application\Shared\Services\DatabaseOptimizationService;

class OptimizeApplicationCommand extends Command
{
    protected $signature = 'app:optimize 
                            {--cache : Warm up cache}
                            {--database : Optimize database}
                            {--indexes : Add missing indexes}
                            {--all : Run all optimizations}';

    protected $description = 'Optimize application performance';

    public function handle(
        CacheService $cacheService,
        DatabaseOptimizationService $dbOptimization
    ): int {
        $this->info('Starting application optimization...');

        $runAll = $this->option('all');
        $runCache = $this->option('cache') || $runAll;
        $runDatabase = $this->option('database') || $runAll;
        $runIndexes = $this->option('indexes') || $runAll;

        if ($runCache) {
            $this->info('Warming up cache...');
            $cacheService->warmUpCache();
            $this->info('✅ Cache warmed up successfully');
        }

        if ($runIndexes) {
            $this->info('Adding missing database indexes...');
            $results = $dbOptimization->addMissingIndexes();
            $this->info('✅ Database indexes added');
            $this->renderResultsTable($results, 'Results');
        }

        if ($runDatabase) {
            $this->info('Optimizing database tables...');
            $results = $dbOptimization->optimizeTables();
            $summaryRendered = $this->renderSummaryIfPresent($results);

            if (! $summaryRendered) {
                $this->info('✅ Database tables optimized');
                $this->renderResultsTable($results, 'Status');
            }
        }

        if (!$runCache && !$runDatabase && !$runIndexes && !$runAll) {
            $this->warn('No optimization options specified. Use --help to see available options.');
            return 1;
        }

        $this->info('🎉 Application optimization completed successfully!');
        return 0;
    }

    private function renderResultsTable(array $results, string $valueHeader): void
    {
        $rows = collect($results)
            ->reject(fn ($value, $key) => $key === '__summary')
            ->map(fn ($value, $key) => [$key, $this->formatResultValue($value)])
            ->values()
            ->all();

        if ($rows === []) {
            return;
        }

        $this->table(['Table', $valueHeader], $rows);
    }

    private function formatResultValue(mixed $value): string
    {
        if (is_array($value)) {
            $status = $value['status'] ?? null;
            $message = $value['message'] ?? null;

            if (is_string($status) || is_string($message)) {
                return trim(implode(': ', array_filter([$status, $message], fn ($item) => is_string($item) && $item !== '')));
            }

            return implode(', ', array_map(
                fn ($item) => is_scalar($item) ? (string) $item : json_encode($item),
                array_values($value)
            ));
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        return (string) $value;
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
