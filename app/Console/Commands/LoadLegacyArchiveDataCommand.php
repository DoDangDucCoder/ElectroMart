<?php

namespace App\Console\Commands;

use App\Application\DataMigration\Services\LegacyDataMigrator;
use App\Application\DataMigration\Services\LegacySqlImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LoadLegacyArchiveDataCommand extends Command
{
    protected $signature = 'legacy:load-archive
                            {--dump= : Explicit path to an old legacy SQL dump}
                            {--force : Drop and recreate existing legacy tbl_* tables before migrating}
                            {--confirm-legacy-archive : Confirm this is old archive data and not the current production catalog}';

    protected $description = 'Import old legacy archive data from an explicitly provided SQL dump and migrate it into the current schema';

    public function handle(LegacySqlImporter $importer, LegacyDataMigrator $migrator): int
    {
        if (app()->environment('production')) {
            $this->error('This legacy archive load command is disabled in production.');

            return self::FAILURE;
        }

        if (! $this->option('confirm-legacy-archive')) {
            $this->warn('This command imports old legacy archive data, not current production data.');
            $this->line('Use --confirm-legacy-archive only after backing up the database and confirming the archive is intentionally needed.');

            return self::FAILURE;
        }

        $dumpOption = trim((string) $this->option('dump'));

        if ($dumpOption === '') {
            $this->error('A legacy archive dump path is required. Pass --dump=/path/to/archive.sql explicitly.');

            return self::FAILURE;
        }

        $dumpPath = $this->resolveDumpPath($dumpOption);

        if (! File::exists($dumpPath)) {
            $this->error('SQL dump file not found: '.$dumpPath);

            return self::FAILURE;
        }

        $this->info('=== Legacy archive data import started ===');
        $this->info('Importing old legacy tbl_* tables from '.$dumpPath);

        try {
            $stats = $importer->import(
                $dumpPath,
                (bool) $this->option('force'),
                fn (string $level, string $message) => $this->{$level}($message),
                confirmed: true
            );

            $this->line('Legacy import: '.$stats['tables_created'].' tables created, '.$stats['tables_skipped'].' skipped, '.$stats['inserts_executed'].' INSERT statements executed.');

            if ($stats['errors'] > 0) {
                $this->error('Legacy import completed with '.$stats['errors'].' error(s).');

                return self::FAILURE;
            }

            $this->info('Migrating old legacy archive data into the current schema...');
            $migrator->migrate(confirmed: true);

            $this->info('Legacy archive data import completed successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Legacy archive data import failed: '.$e->getMessage());
            Log::error('Legacy archive data import failed', ['exception' => $e]);

            return self::FAILURE;
        }
    }

    private function resolveDumpPath(string $dump): string
    {
        $dump = trim($dump);

        if (str_starts_with($dump, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:[\\\\\\/]/', $dump) === 1) {
            return $dump;
        }

        return base_path($dump);
    }
}
