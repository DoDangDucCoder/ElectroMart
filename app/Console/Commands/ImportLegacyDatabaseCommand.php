<?php

namespace App\Console\Commands;

use App\Application\DataMigration\Services\LegacySqlImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportLegacyDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:legacy-database
                            {--dump= : Explicit path to an old legacy SQL dump}
                            {--force : Drop and recreate existing legacy tbl_* tables}
                            {--confirm-legacy-archive : Confirm this is old archive data and not current production data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import old legacy archive tables from an explicitly provided SQL dump file';

    /**
     * Execute the console command.
     */
    public function handle(LegacySqlImporter $importer): int
    {
        if (app()->environment('production')) {
            $this->error('This legacy archive import command is disabled in production.');

            return self::FAILURE;
        }

        if (! $this->option('confirm-legacy-archive')) {
            $this->warn('This command imports old legacy archive tables, not current production data.');
            $this->line('Use --confirm-legacy-archive only after backing up the database and confirming the archive is intentionally needed.');

            return self::FAILURE;
        }

        $dumpOption = trim((string) $this->option('dump'));

        if ($dumpOption === '') {
            $this->error('A legacy archive dump path is required. Pass --dump=/path/to/archive.sql explicitly.');

            return self::FAILURE;
        }

        $dumpPath = $this->resolveDumpPath($dumpOption);

        $this->info('Importing old legacy archive database from '.$dumpPath.'...');

        if (! File::exists($dumpPath)) {
            $this->error('SQL dump file not found.');

            return self::FAILURE;
        }

        $stats = $importer->import(
            $dumpPath,
            (bool) $this->option('force'),
            fn (string $level, string $message) => $this->{$level}($message),
            confirmed: true
        );

        $this->info('Import completed.');
        $this->line('Statements executed: '.$stats['statements_executed']);
        $this->line('Tables created: '.$stats['tables_created']);
        $this->line('Tables skipped: '.$stats['tables_skipped']);
        $this->line('INSERT statements executed: '.$stats['inserts_executed']);

        return $stats['errors'] > 0 ? self::FAILURE : self::SUCCESS;
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
