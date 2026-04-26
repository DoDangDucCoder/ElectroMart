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
                            {--dump=shopbanhang.sql : Path to the legacy SQL dump}
                            {--force : Drop and recreate existing legacy tbl_* tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all legacy tables from shopbanhang.sql file';

    /**
     * Execute the console command.
     */
    public function handle(LegacySqlImporter $importer): int
    {
        $dumpPath = $this->resolveDumpPath((string) $this->option('dump'));

        $this->info('Importing legacy database from '.$dumpPath.'...');

        if (! File::exists($dumpPath)) {
            $this->error('SQL dump file not found.');

            return self::FAILURE;
        }

        $stats = $importer->import(
            $dumpPath,
            (bool) $this->option('force'),
            fn (string $level, string $message) => $this->{$level}($message)
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
        $dump = trim($dump) ?: 'shopbanhang.sql';

        if (str_starts_with($dump, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:[\\\\\\/]/', $dump) === 1) {
            return $dump;
        }

        return base_path($dump);
    }
}
