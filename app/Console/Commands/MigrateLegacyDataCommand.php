<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\DataMigration\Services\DataMigrationService;

class MigrateLegacyDataCommand extends Command
{
    protected $signature = 'migrate:legacy-data 
                            {--table= : Migrate specific table only}
                            {--dry-run : Show what would be migrated without actually migrating}
                            {--force : Force migration even if target tables have data}';

    protected $description = 'Migrate data from legacy tables to new schema';

    public function handle(DataMigrationService $migrationService): int
    {
        $this->info('Starting legacy data migration...');

        if ($this->option('dry-run')) {
            $this->info('DRY RUN MODE - No data will be migrated');
            return $this->showMigrationPlan($migrationService);
        }

        if (!$this->option('force')) {
            if ($this->hasExistingData()) {
                if (!$this->confirm('Target tables contain data. Continue anyway?')) {
                    $this->info('Migration cancelled.');
                    return 0;
                }
            }
        }

        try {
            $results = $migrationService->migrateAll();
            
            $this->displayResults($results);
            
            $this->info('Migration completed successfully!');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function showMigrationPlan(DataMigrationService $migrationService): int
    {
        $mappings = \DB::table('migration_mappings')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $this->table(
            ['Source Table', 'Target Table', 'Records to Migrate'],
            $mappings->map(function ($mapping) {
                $count = \DB::table($mapping->source_table)->count();
                return [
                    $mapping->source_table,
                    $mapping->target_table,
                    $count
                ];
            })
        );

        return 0;
    }

    private function hasExistingData(): bool
    {
        $tables = ['products', 'brands', 'product_categories', 'customers', 'orders'];
        
        foreach ($tables as $table) {
            if (\Schema::hasTable($table) && \DB::table($table)->count() > 0) {
                return true;
            }
        }
        
        return false;
    }

    private function displayResults(array $results): void
    {
        $this->info('Migration Results:');
        $this->newLine();

        $totalMigrated = 0;
        $successCount = 0;

        foreach ($results as $table => $result) {
            if ($result['success']) {
                $this->info("✅ {$table}: {$result['records_migrated']} records migrated");
                $totalMigrated += $result['records_migrated'];
                $successCount++;
            } else {
                $this->error("❌ {$table}: Failed - {$result['error']}");
            }
        }

        $this->newLine();
        $this->info("Summary: {$successCount} tables migrated successfully, {$totalMigrated} total records migrated");
    }
}
