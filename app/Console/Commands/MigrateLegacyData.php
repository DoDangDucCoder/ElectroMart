<?php

namespace App\Console\Commands;

use App\Application\DataMigration\Services\LegacyDataMigrator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MigrateLegacyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate-legacy
                            {--dry-run : Chạy thử không ghi dữ liệu mới}
                            {--confirm-legacy-migration : Confirm this legacy tbl_* migration is intentionally needed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chuyển dữ liệu từ schema cũ (tbl_*) sang schema mới';

    public function __construct(private readonly LegacyDataMigrator $migrator)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('This legacy data migration command is disabled in production.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        $this->info('=== Legacy data migration started ===');
        $this->info($dryRun ? 'Dry-run mode: không ghi dữ liệu mới.' : 'Chế độ thường: dữ liệu sẽ được ghi.');

        try {
            if ($dryRun) {
                $this->warn('Dry-run chưa được triển khai. Vui lòng bỏ --dry-run để chạy thật.');
                return self::SUCCESS;
            }

            if (! $this->option('confirm-legacy-migration')) {
                $this->warn('This command migrates legacy tbl_* data into the current schema.');
                $this->line('Use --confirm-legacy-migration only after backing up the database and confirming this one-time migration is intentionally needed.');

                return self::FAILURE;
            }

            $this->migrator->migrate(confirmed: true);

            $this->info('Legacy data migration completed successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Migration failed: '.$e->getMessage());
            Log::error('Legacy data migration failed', ['exception' => $e]);

            return self::FAILURE;
        }
    }
}
