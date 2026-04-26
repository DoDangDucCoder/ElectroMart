<?php

namespace App\Console\Commands;

use App\Application\DataMigration\Services\LegacyDataMigrator;
use App\Application\DataMigration\Services\LegacySqlImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LoadRealDataCommand extends Command
{
    protected $signature = 'data:load-real
                            {--dump=shopbanhang.sql : Path to the legacy SQL dump}
                            {--force : Drop and recreate existing legacy tbl_* tables before migrating}';

    protected $description = 'Import the real legacy shopbanhang.sql dump and migrate it into the current schema';

    public function handle(LegacySqlImporter $importer, LegacyDataMigrator $migrator): int
    {
        $dumpPath = $this->resolveDumpPath((string) $this->option('dump'));

        if (! File::exists($dumpPath)) {
            $this->error('SQL dump file not found: '.$dumpPath);

            return self::FAILURE;
        }

        $this->info('=== Real data bootstrap started ===');
        $this->info('Importing legacy tbl_* tables from '.$dumpPath);

        try {
            $stats = $importer->import(
                $dumpPath,
                (bool) $this->option('force'),
                fn (string $level, string $message) => $this->{$level}($message)
            );

            $this->line('Legacy import: '.$stats['tables_created'].' tables created, '.$stats['tables_skipped'].' skipped, '.$stats['inserts_executed'].' INSERT statements executed.');

            if ($stats['errors'] > 0) {
                $this->error('Legacy import completed with '.$stats['errors'].' error(s).');

                return self::FAILURE;
            }

            $this->info('Migrating legacy data into the current schema...');
            $migrator->migrate();

            $this->info('Real data bootstrap completed successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Real data bootstrap failed: '.$e->getMessage());
            Log::error('Real data bootstrap failed', ['exception' => $e]);

            return self::FAILURE;
        }
    }

    private function resolveDumpPath(string $dump): string
    {
        $dump = trim($dump) ?: 'shopbanhang.sql';

        if (str_starts_with($dump, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:[\\\\\\/]/', $dump) === 1) {
            return $dump;
        }

        return base_path($dump);
    }
}
