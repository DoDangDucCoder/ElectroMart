<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class AuditLegacyRuntimeReferencesCommand extends Command
{
    protected $signature = 'legacy:audit-runtime
        {--fail-on-issues : Return a non-zero exit code when runtime legacy references are found}
        {--include-legacy-tooling : Also scan guarded legacy migration/import tooling}';

    protected $description = 'Audit runtime code for legacy tbl_* references without mutating files or data';

    private const LEGACY_MODELS = [
        'Admin',
        'Attribute',
        'CatePost',
        'CategoryProductModel',
        'City',
        'Comment',
        'Contact',
        'Coupon',
        'Customer',
        'Feeship',
        'Gallery',
        'Icons',
        'Login',
        'Order',
        'OrderDetails',
        'Product',
        'ProductAttribute',
        'Province',
        'Rating',
        'RolesUser',
        'Shipping',
        'Slider',
        'Social',
        'SocialCustomers',
        'Statistic',
        'Video',
        'Visitors',
        'Wards',
    ];

    private const SCAN_PATHS = [
        'app/Application',
        'app/Console',
        'app/Exports',
        'app/Http',
        'app/Imports',
        'app/Infrastructure',
        'app/Mail',
        'app/Notifications',
        'resources',
        'routes',
    ];

    private const EXCLUDED_PATH_FRAGMENTS = [
        'app/Application/DataMigration',
    ];

    private const EXCLUDED_FILES = [
        'app/Console/Commands/AuditLegacyHoldoutDataCommand.php',
        'app/Console/Commands/ImportLegacyDatabaseCommand.php',
        'app/Console/Commands/LoadLegacyArchiveDataCommand.php',
        'app/Console/Commands/MigrateLegacyHoldoutDataCommand.php',
        'app/Console/Commands/MigrateLegacyData.php',
        'app/Console/Commands/MigrateLegacyDataCommand.php',
        'app/Console/Commands/NormalizeAdminAuthCommand.php',
        'app/Console/Commands/SetupLegacyTablesCommand.php',
    ];

    public function handle(): int
    {
        $issues = $this->findIssues();

        if ($issues === []) {
            $this->info('Runtime legacy audit passed.');

            return self::SUCCESS;
        }

        $this->table(['File', 'Line', 'Issue', 'Match'], $issues);
        $this->warn('Runtime legacy audit found '.count($issues).' issue(s).');

        return $this->option('fail-on-issues') ? self::FAILURE : self::SUCCESS;
    }

    private function findIssues(): array
    {
        $issues = [];

        foreach (self::SCAN_PATHS as $relativePath) {
            $path = base_path($relativePath);

            if (! is_dir($path)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

            foreach ($iterator as $file) {
                if (! $file instanceof SplFileInfo || ! $file->isFile()) {
                    continue;
                }

                if ($this->isExcludedPath($file)) {
                    continue;
                }

                if (! $this->shouldScanFile($file)) {
                    continue;
                }

                array_push($issues, ...$this->scanFile($file));
            }
        }

        return $issues;
    }

    private function shouldScanFile(SplFileInfo $file): bool
    {
        return in_array(strtolower($file->getExtension()), ['php', 'blade.php'], true)
            || str_ends_with($file->getFilename(), '.blade.php');
    }

    private function isExcludedPath(SplFileInfo $file): bool
    {
        if ($this->option('include-legacy-tooling')) {
            return false;
        }

        $relativePath = str_replace('\\', '/', str_replace(base_path(DIRECTORY_SEPARATOR), '', $file->getPathname()));

        if (in_array($relativePath, self::EXCLUDED_FILES, true)) {
            return true;
        }

        foreach (self::EXCLUDED_PATH_FRAGMENTS as $fragment) {
            if (str_starts_with($relativePath, $fragment.'/')) {
                return true;
            }
        }

        return false;
    }

    private function scanFile(SplFileInfo $file): array
    {
        $issues = [];
        $lines = file($file->getPathname(), FILE_IGNORE_NEW_LINES);

        if ($lines === false) {
            return $issues;
        }

        foreach ($lines as $index => $line) {
            if (preg_match_all('/\btbl_[A-Za-z0-9_]+\b/', $line, $matches)) {
                foreach ($matches[0] as $match) {
                    $issues[] = $this->issueRow($file, $index + 1, 'legacy_table_reference', $match);
                }
            }

            foreach (self::LEGACY_MODELS as $model) {
                $pattern = '/\bApp\\\\Models\\\\'.preg_quote($model, '/').'\b/';

                if (preg_match($pattern, $line, $matches)) {
                    $issues[] = $this->issueRow($file, $index + 1, 'legacy_model_reference', $matches[0]);
                }
            }
        }

        return $issues;
    }

    private function issueRow(SplFileInfo $file, int $line, string $issue, string $match): array
    {
        return [
            'file' => str_replace(base_path(DIRECTORY_SEPARATOR), '', $file->getPathname()),
            'line' => $line,
            'issue' => $issue,
            'match' => $match,
        ];
    }
}
