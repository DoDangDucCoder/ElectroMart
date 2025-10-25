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
            $this->table(['Table', 'Results'], collect($results)->map(fn($v, $k) => [$k, implode(', ', $v)])->toArray());
        }

        if ($runDatabase) {
            $this->info('Optimizing database tables...');
            $results = $dbOptimization->optimizeTables();
            $this->info('✅ Database tables optimized');
            $this->table(['Table', 'Status'], collect($results)->map(fn($v, $k) => [$k, $v])->toArray());
        }

        if (!$runCache && !$runDatabase && !$runIndexes && !$runAll) {
            $this->warn('No optimization options specified. Use --help to see available options.');
            return 1;
        }

        $this->info('🎉 Application optimization completed successfully!');
        return 0;
    }
}
