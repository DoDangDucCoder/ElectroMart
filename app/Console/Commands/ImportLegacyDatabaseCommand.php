<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportLegacyDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:legacy-database {--force : Force import even if tables exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all legacy tables from shopbanhang.sql file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing legacy database from shopbanhang.sql...');

        $sqlFile = base_path('shopbanhang.sql');
        
        if (!File::exists($sqlFile)) {
            $this->error('shopbanhang.sql file not found!');
            return 1;
        }

        $sqlContent = File::get($sqlFile);
        
        // Extract CREATE TABLE statements for tbl_* tables
        preg_match_all('/CREATE TABLE `(tbl_[^`]+)`[^;]+;/', $sqlContent, $matches);
        
        $tables = $matches[0];
        $tableNames = $matches[1];
        
        $this->info('Found ' . count($tables) . ' legacy tables to import');
        
        $importedCount = 0;
        $skippedCount = 0;
        
        foreach ($tables as $index => $createTableSql) {
            $tableName = $tableNames[$index];
            
            if (!$this->option('force') && DB::getSchemaBuilder()->hasTable($tableName)) {
                $this->line("Skipping $tableName (already exists)");
                $skippedCount++;
                continue;
            }
            
            try {
                // Drop table if exists and force is enabled
                if ($this->option('force')) {
                    DB::statement("DROP TABLE IF EXISTS `$tableName`");
                }
                
                DB::statement($createTableSql);
                $this->info("✓ Created table: $tableName");
                $importedCount++;
                
            } catch (\Exception $e) {
                $this->error("✗ Failed to create $tableName: " . $e->getMessage());
            }
        }
        
        // Extract INSERT statements for tbl_* tables
        preg_match_all('/INSERT INTO `(tbl_[^`]+)`[^;]+;/', $sqlContent, $insertMatches);
        
        $this->info('Importing data for ' . count($insertMatches[0]) . ' tables...');
        
        foreach ($insertMatches[0] as $index => $insertSql) {
            $tableName = $insertMatches[1][$index];
            
            try {
                DB::statement($insertSql);
                $this->line("✓ Imported data for: $tableName");
            } catch (\Exception $e) {
                $this->warn("⚠ Failed to import data for $tableName: " . $e->getMessage());
            }
        }
        
        $this->info("Import completed!");
        $this->info("Tables created: $importedCount");
        $this->info("Tables skipped: $skippedCount");
        
        return 0;
    }
}
