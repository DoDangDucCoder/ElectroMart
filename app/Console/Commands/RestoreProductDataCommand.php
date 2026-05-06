<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;
use ZipArchive;

class RestoreProductDataCommand extends Command
{
    protected $signature = 'products:restore-product-data
        {--backup= : Backup directory to restore from}
        {--dry-run : Report planned restore only}
        {--commit : Write database rows and copy files}';

    protected $description = 'Safely restore product thumbnails and product_media from a scoped product data backup.';

    private const REPORT_HEADERS = [
        'product_id',
        'product_name',
        'product_slug',
        'product_row_action',
        'product_media_action',
        'current_media_count',
        'backup_media_count',
        'product_media_rows_to_restore',
        'files_to_copy',
        'missing_file_sources',
        'status',
    ];

    private array $backupRows = [];

    private array $auditRows = [];

    private array $errors = [];

    private array $zipEntryCache = [];

    private array $summary = [
        'dry_run' => true,
        'commit' => false,
        'backup_path' => null,
        'backup_format' => [],
        'current_state_backup_path' => null,
        'tables_available' => [],
        'tables_restored' => [],
        'products_affected' => 0,
        'product_rows_to_restore' => 0,
        'product_media_products_to_restore' => 0,
        'product_media_rows_to_restore' => 0,
        'brands_to_restore' => 0,
        'categories_to_restore' => 0,
        'files_to_copy' => 0,
        'files_copied' => 0,
        'products_committed' => 0,
        'product_media_rows_committed' => 0,
        'errors' => 0,
        'affected_products' => [],
        'reports' => [],
    ];

    public function handle(): int
    {
        if (! $this->validateMode()) {
            return self::FAILURE;
        }

        if (! $this->validateSchema()) {
            return self::FAILURE;
        }

        try {
            $backupPath = $this->resolveBackupPath((string) $this->option('backup'));
            $this->summary['backup_path'] = $backupPath;
            $this->summary['backup_format'] = $this->detectBackupFormat($backupPath);
            $this->backupRows = $this->loadBackupRows($backupPath);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $plan = $this->buildRestorePlan();
        $this->summarizePlan($plan);
        $this->buildAuditRows($plan);

        if ((bool) $this->option('commit')) {
            if ($this->hasBlockingFileErrors($plan)) {
                $this->writeReports();
                $this->line(json_encode($this->summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

                return self::FAILURE;
            }

            $this->summary['current_state_backup_path'] = $this->backupCurrentState();
            $this->applyRestorePlan($plan);
        }

        $this->writeReports();
        $this->summary['errors'] = count($this->errors);

        $this->line(json_encode($this->summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return (bool) $this->option('commit') && $this->errors !== []
            ? self::FAILURE
            : self::SUCCESS;
    }

    private function validateMode(): bool
    {
        $commit = (bool) $this->option('commit');
        $dryRun = (bool) $this->option('dry-run');

        if ($commit && $dryRun) {
            $this->error('Use either --dry-run or --commit, not both.');

            return false;
        }

        $this->summary['commit'] = $commit;
        $this->summary['dry_run'] = ! $commit;

        if ($this->option('backup') === null || trim((string) $this->option('backup')) === '') {
            $this->error('Missing required --backup option.');

            return false;
        }

        return true;
    }

    private function validateSchema(): bool
    {
        foreach (['products', 'product_media'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->error("Missing required table: {$table}");

                return false;
            }
        }

        foreach (['id', 'name', 'slug', 'brand_id', 'primary_category_id', 'thumbnail_path'] as $column) {
            if (! Schema::hasColumn('products', $column)) {
                $this->error("Missing required products column: {$column}");

                return false;
            }
        }

        foreach (['product_id', 'file_path', 'disk', 'collection', 'display_order', 'meta'] as $column) {
            if (! Schema::hasColumn('product_media', $column)) {
                $this->error("Missing required product_media column: {$column}");

                return false;
            }
        }

        return true;
    }

    private function resolveBackupPath(string $backup): string
    {
        $candidates = [
            $backup,
            base_path($backup),
            dirname(base_path()).DIRECTORY_SEPARATOR.$backup,
        ];

        foreach ($candidates as $candidate) {
            if (File::isDirectory($candidate)) {
                return realpath($candidate) ?: $candidate;
            }
        }

        throw new RuntimeException("Backup directory not found: {$backup}");
    }

    private function detectBackupFormat(string $backupPath): array
    {
        $files = collect(File::files($backupPath))->map(fn ($file) => $file->getFilename())->values()->all();

        return [
            'has_sql' => File::exists($backupPath.DIRECTORY_SEPARATOR.'database.sql'),
            'json_files' => array_values(array_filter($files, fn (string $file): bool => str_ends_with($file, '.json'))),
            'csv_files' => array_values(array_filter($files, fn (string $file): bool => str_ends_with($file, '.csv'))),
            'zip_files' => array_values(array_filter($files, fn (string $file): bool => str_ends_with($file, '.zip'))),
            'has_source_zip' => File::exists($backupPath.DIRECTORY_SEPARATOR.'source.zip'),
            'has_files_zip' => File::exists($backupPath.DIRECTORY_SEPARATOR.'files.zip'),
        ];
    }

    private function loadBackupRows(string $backupPath): array
    {
        $rows = [
            'products' => [],
            'product_media' => [],
            'brands' => [],
            'product_categories' => [],
            'categories' => [],
        ];

        foreach (array_keys($rows) as $table) {
            $jsonFile = $backupPath.DIRECTORY_SEPARATOR.$table.'.json';
            if (File::exists($jsonFile)) {
                $rows[$table] = $this->loadJsonRows($jsonFile);
            }
        }

        $sqlFile = $backupPath.DIRECTORY_SEPARATOR.'database.sql';
        if (File::exists($sqlFile)) {
            $sqlRows = $this->loadSqlRows($sqlFile, array_keys($rows));
            foreach ($sqlRows as $table => $tableRows) {
                if ($tableRows !== []) {
                    $rows[$table] = $tableRows;
                }
            }
        }

        if ($rows['products'] === [] && $rows['product_media'] === []) {
            throw new RuntimeException('Backup does not contain products or product_media rows.');
        }

        $this->summary['tables_available'] = array_values(array_filter(
            array_keys($rows),
            fn (string $table): bool => $rows[$table] !== []
        ));

        return $rows;
    }

    private function loadJsonRows(string $path): array
    {
        $decoded = json_decode((string) File::get($path), true);
        if (! is_array($decoded)) {
            throw new RuntimeException("Invalid JSON backup file: {$path}");
        }

        return array_values(array_filter($decoded, 'is_array'));
    }

    private function loadSqlRows(string $path, array $tables): array
    {
        $wanted = array_fill_keys($tables, true);
        $rows = array_fill_keys($tables, []);
        $file = new \SplFileObject($path, 'rb');

        while (! $file->eof()) {
            $line = trim((string) $file->fgets());
            if (! str_starts_with($line, 'INSERT INTO `')) {
                continue;
            }

            if (! preg_match('/^INSERT INTO `([^`]+)` \((.+)\) VALUES \((.*)\);$/s', $line, $matches)) {
                continue;
            }

            $table = $matches[1];
            if (! isset($wanted[$table])) {
                continue;
            }

            $columns = array_map(
                fn (string $column): string => trim($column, " `\t\n\r\0\x0B"),
                explode(',', $matches[2])
            );
            $values = $this->parseSqlValues($matches[3]);

            if (count($columns) !== count($values)) {
                throw new RuntimeException("Column/value mismatch while parsing {$table} in {$path}");
            }

            $rows[$table][] = array_combine($columns, $values);
        }

        return $rows;
    }

    private function parseSqlValues(string $input): array
    {
        $values = [];
        $length = strlen($input);
        $index = 0;

        while ($index < $length) {
            while ($index < $length && ctype_space($input[$index])) {
                $index++;
            }

            if ($index >= $length) {
                break;
            }

            if ($input[$index] === "'") {
                $index++;
                $value = '';

                while ($index < $length) {
                    $char = $input[$index];

                    if ($char === '\\') {
                        $index++;
                        if ($index >= $length) {
                            break;
                        }

                        $value .= $this->decodeSqlEscape($input[$index]);
                        $index++;

                        continue;
                    }

                    if ($char === "'") {
                        $index++;
                        break;
                    }

                    $value .= $char;
                    $index++;
                }

                $values[] = $value;
            } else {
                $start = $index;
                while ($index < $length && $input[$index] !== ',') {
                    $index++;
                }

                $raw = trim(substr($input, $start, $index - $start));
                $values[] = strtoupper($raw) === 'NULL' ? null : $raw;
            }

            while ($index < $length && ctype_space($input[$index])) {
                $index++;
            }

            if ($index < $length && $input[$index] === ',') {
                $index++;
            }
        }

        return $values;
    }

    private function decodeSqlEscape(string $char): string
    {
        return match ($char) {
            '0' => "\0",
            'b' => "\b",
            'n' => "\n",
            'r' => "\r",
            't' => "\t",
            'Z' => chr(26),
            default => $char,
        };
    }

    private function buildRestorePlan(): array
    {
        $backupProducts = $this->keyRows($this->backupRows['products'] ?? [], 'id');
        $backupMediaByProduct = $this->groupRows($this->backupRows['product_media'] ?? [], 'product_id');

        $productActions = $this->buildProductActions($backupProducts);
        $mediaActions = $this->buildMediaActions($backupMediaByProduct);
        $affectedProductIds = array_values(array_unique(array_merge(
            array_keys($productActions),
            array_keys($mediaActions)
        )));
        sort($affectedProductIds, SORT_NUMERIC);

        $referenceActions = $this->buildReferenceActions($affectedProductIds, $backupProducts);
        $fileActions = $this->buildFileActions($productActions, $mediaActions);

        return [
            'backup_products' => $backupProducts,
            'product_actions' => $productActions,
            'media_actions' => $mediaActions,
            'reference_actions' => $referenceActions,
            'file_actions' => $fileActions,
            'affected_product_ids' => $affectedProductIds,
        ];
    }

    private function buildProductActions(array $backupProducts): array
    {
        if ($backupProducts === []) {
            return [];
        }

        $currentProducts = DB::table('products')
            ->whereIn('id', array_map('intval', array_keys($backupProducts)))
            ->get()
            ->map(fn ($row): array => (array) $row)
            ->keyBy(fn (array $row): string => (string) $row['id'])
            ->all();

        $actions = [];

        foreach ($backupProducts as $productId => $backupProduct) {
            $currentProduct = $currentProducts[(string) $productId] ?? null;

            if ($currentProduct === null) {
                $actions[(int) $productId] = [
                    'action' => 'create',
                    'backup' => $backupProduct,
                    'current' => null,
                ];

                continue;
            }

            if ($this->normalizePublicPath($currentProduct['thumbnail_path'] ?? null) !== $this->normalizePublicPath($backupProduct['thumbnail_path'] ?? null)) {
                $actions[(int) $productId] = [
                    'action' => 'update_thumbnail',
                    'backup' => $backupProduct,
                    'current' => $currentProduct,
                ];
            }
        }

        return $actions;
    }

    private function buildMediaActions(array $backupMediaByProduct): array
    {
        if ($backupMediaByProduct === []) {
            return [];
        }

        $productIds = array_map('intval', array_keys($backupMediaByProduct));
        $currentMediaByProduct = DB::table('product_media')
            ->whereIn('product_id', $productIds)
            ->get()
            ->map(fn ($row): array => (array) $row)
            ->groupBy(fn (array $row): string => (string) $row['product_id'])
            ->map(fn ($rows): array => $rows->values()->all())
            ->all();

        $actions = [];

        foreach ($backupMediaByProduct as $productId => $backupRows) {
            $currentRows = $currentMediaByProduct[(string) $productId] ?? [];

            if ($this->mediaSignature($currentRows) === $this->mediaSignature($backupRows)) {
                continue;
            }

            $actions[(int) $productId] = [
                'backup_rows' => array_values($backupRows),
                'current_rows' => array_values($currentRows),
            ];
        }

        return $actions;
    }

    private function buildReferenceActions(array $affectedProductIds, array $backupProducts): array
    {
        $actions = [
            'brands' => [],
            'product_categories' => [],
            'categories' => [],
        ];

        foreach ($affectedProductIds as $productId) {
            $product = $backupProducts[(string) $productId] ?? null;
            if (! $product) {
                continue;
            }

            if (isset($product['brand_id'])) {
                $this->maybeAddReferenceAction($actions, 'brands', $product['brand_id']);
            }

            if (isset($product['primary_category_id'])) {
                $this->maybeAddReferenceAction($actions, 'product_categories', $product['primary_category_id']);
            }
        }

        return $actions;
    }

    private function maybeAddReferenceAction(array &$actions, string $table, mixed $id): void
    {
        if (! Schema::hasTable($table) || ! isset($this->backupRows[$table])) {
            return;
        }

        $backupRows = $this->keyRows($this->backupRows[$table], 'id');
        $backupRow = $backupRows[(string) $id] ?? null;
        if (! $backupRow) {
            return;
        }

        $currentRow = DB::table($table)->where('id', $id)->first();
        if ($currentRow === null) {
            $actions[$table][(int) $id] = [
                'action' => 'create',
                'backup' => $backupRow,
                'current' => null,
            ];

            return;
        }

        $current = (array) $currentRow;
        foreach (['name', 'slug', 'code', 'thumbnail_path', 'logo_path'] as $column) {
            if (array_key_exists($column, $backupRow) && array_key_exists($column, $current) && (string) ($backupRow[$column] ?? '') !== (string) ($current[$column] ?? '')) {
                $actions[$table][(int) $id] = [
                    'action' => 'update',
                    'backup' => $backupRow,
                    'current' => $current,
                ];

                return;
            }
        }
    }

    private function buildFileActions(array $productActions, array $mediaActions): array
    {
        $paths = [];

        foreach ($productActions as $action) {
            $path = $this->normalizePublicPath($action['backup']['thumbnail_path'] ?? null);
            if ($path !== null) {
                $paths[$path] = $path;
            }
        }

        foreach ($mediaActions as $action) {
            foreach ($action['backup_rows'] as $row) {
                $path = $this->normalizePublicPath($row['file_path'] ?? null);
                if ($path !== null) {
                    $paths[$path] = $path;
                }
            }
        }

        $fileActions = [];
        foreach (array_values($paths) as $path) {
            $absolutePath = public_path($path);
            $source = $this->findBackupFileSource($path);
            $exists = File::isFile($absolutePath) && File::size($absolutePath) > 0;
            $needsCopy = false;
            $missingSource = false;

            if ($source !== null) {
                $sourceSize = (int) $source['size'];
                $needsCopy = ! $exists || (File::isFile($absolutePath) && File::size($absolutePath) !== $sourceSize);
            } elseif (! $exists) {
                $missingSource = true;
                $this->errors[] = [
                    'type' => 'missing_file_source',
                    'file_path' => $path,
                    'message' => 'File is missing locally and backup has no source.zip/files.zip entry.',
                ];
            }

            $fileActions[$path] = [
                'path' => $path,
                'absolute_path' => $absolutePath,
                'exists' => $exists,
                'copy' => $needsCopy,
                'missing_source' => $missingSource,
                'source' => $source,
            ];
        }

        return $fileActions;
    }

    private function summarizePlan(array $plan): void
    {
        $tables = [];
        if ($plan['product_actions'] !== []) {
            $tables[] = 'products';
        }

        if ($plan['media_actions'] !== []) {
            $tables[] = 'product_media';
        }

        foreach (['brands', 'product_categories', 'categories'] as $table) {
            if (($plan['reference_actions'][$table] ?? []) !== []) {
                $tables[] = $table;
            }
        }

        $mediaRows = 0;
        foreach ($plan['media_actions'] as $action) {
            $mediaRows += count($action['backup_rows']);
        }

        $this->summary['tables_restored'] = $tables;
        $this->summary['products_affected'] = count($plan['affected_product_ids']);
        $this->summary['product_rows_to_restore'] = count($plan['product_actions']);
        $this->summary['product_media_products_to_restore'] = count($plan['media_actions']);
        $this->summary['product_media_rows_to_restore'] = $mediaRows;
        $this->summary['brands_to_restore'] = count($plan['reference_actions']['brands'] ?? []);
        $this->summary['categories_to_restore'] = count($plan['reference_actions']['product_categories'] ?? []) + count($plan['reference_actions']['categories'] ?? []);
        $this->summary['files_to_copy'] = count(array_filter($plan['file_actions'], fn (array $action): bool => $action['copy']));
        $this->summary['errors'] = count($this->errors);
        $this->summary['affected_products'] = $this->affectedProductNames($plan['affected_product_ids'], $plan['backup_products']);
    }

    private function buildAuditRows(array $plan): void
    {
        foreach ($plan['affected_product_ids'] as $productId) {
            $backupProduct = $plan['backup_products'][(string) $productId] ?? [];
            $productAction = $plan['product_actions'][$productId]['action'] ?? 'none';
            $mediaAction = isset($plan['media_actions'][$productId]) ? 'replace_product_media' : 'none';
            $currentMediaCount = isset($plan['media_actions'][$productId])
                ? count($plan['media_actions'][$productId]['current_rows'])
                : DB::table('product_media')->where('product_id', $productId)->count();
            $backupMediaCount = isset($plan['media_actions'][$productId])
                ? count($plan['media_actions'][$productId]['backup_rows'])
                : count($this->groupRows($this->backupRows['product_media'] ?? [], 'product_id')[(string) $productId] ?? []);
            $paths = $this->filePathsForProduct($productId, $plan);
            $filesToCopy = count(array_filter($paths, fn (string $path): bool => ($plan['file_actions'][$path]['copy'] ?? false) === true));
            $missingSources = count(array_filter($paths, fn (string $path): bool => ($plan['file_actions'][$path]['missing_source'] ?? false) === true));

            $this->auditRows[] = [
                'product_id' => $productId,
                'product_name' => $backupProduct['name'] ?? '',
                'product_slug' => $backupProduct['slug'] ?? '',
                'product_row_action' => $productAction,
                'product_media_action' => $mediaAction,
                'current_media_count' => $currentMediaCount,
                'backup_media_count' => $backupMediaCount,
                'product_media_rows_to_restore' => isset($plan['media_actions'][$productId]) ? count($plan['media_actions'][$productId]['backup_rows']) : 0,
                'files_to_copy' => $filesToCopy,
                'missing_file_sources' => $missingSources,
                'status' => (bool) $this->option('commit') ? 'PENDING' : 'DRY_RUN_PLANNED',
            ];
        }
    }

    private function hasBlockingFileErrors(array $plan): bool
    {
        foreach ($plan['file_actions'] as $action) {
            if ($action['missing_source']) {
                return true;
            }
        }

        return false;
    }

    private function backupCurrentState(): string
    {
        $timestamp = Carbon::now()->format('Ymd-His');
        $backupPath = dirname(base_path()).DIRECTORY_SEPARATOR.'_backups'.DIRECTORY_SEPARATOR.'Laravel11_Shop_Website_Full'.DIRECTORY_SEPARATOR.$timestamp.'-current-broken-before-restore';

        File::ensureDirectoryExists($backupPath);

        foreach (['products', 'product_media', 'brands', 'product_categories', 'categories'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            File::put(
                $backupPath.DIRECTORY_SEPARATOR.$table.'.json',
                json_encode(DB::table($table)->get(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        }

        $this->backupCurrentFiles($backupPath);

        File::put(
            $backupPath.DIRECTORY_SEPARATOR.'README-current-broken-before-restore.txt',
            "Backup before products:restore-product-data --commit.\nContains JSON exports for product-related tables and files.zip with current referenced product images.\n"
        );

        return $backupPath;
    }

    private function backupCurrentFiles(string $backupPath): void
    {
        $paths = [];

        foreach (DB::table('products')->pluck('thumbnail_path') as $path) {
            $normalized = $this->normalizePublicPath($path);
            if ($normalized !== null) {
                $paths[$normalized] = $normalized;
            }
        }

        foreach (DB::table('product_media')->pluck('file_path') as $path) {
            $normalized = $this->normalizePublicPath($path);
            if ($normalized !== null) {
                $paths[$normalized] = $normalized;
            }
        }

        $zip = new ZipArchive;
        $zipPath = $backupPath.DIRECTORY_SEPARATOR.'files.zip';
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->errors[] = [
                'type' => 'backup_files_zip_failed',
                'message' => "Could not create {$zipPath}",
            ];

            return;
        }

        $added = 0;
        foreach ($paths as $path) {
            $absolutePath = public_path($path);
            if (! File::isFile($absolutePath)) {
                continue;
            }

            $zip->addFile($absolutePath, 'public/'.str_replace('\\', '/', $path));
            $added++;
        }

        $zip->close();
        $this->summary['current_state_backup_files'] = $added;
    }

    private function applyRestorePlan(array $plan): void
    {
        foreach ($plan['file_actions'] as $fileAction) {
            if (! $fileAction['copy']) {
                continue;
            }

            try {
                $this->copyBackupFile($fileAction);
                $this->summary['files_copied']++;
            } catch (Throwable $exception) {
                $this->errors[] = [
                    'type' => 'file_copy_failed',
                    'file_path' => $fileAction['path'],
                    'message' => $exception->getMessage(),
                ];
            }
        }

        if ($this->errors !== []) {
            return;
        }

        foreach ($plan['affected_product_ids'] as $productId) {
            try {
                DB::transaction(function () use ($plan, $productId): void {
                    if (isset($plan['reference_actions']['brands'])) {
                        foreach ($plan['reference_actions']['brands'] as $action) {
                            $this->applyReferenceAction('brands', $action);
                        }
                    }

                    if (isset($plan['reference_actions']['product_categories'])) {
                        foreach ($plan['reference_actions']['product_categories'] as $action) {
                            $this->applyReferenceAction('product_categories', $action);
                        }
                    }

                    if (isset($plan['product_actions'][$productId])) {
                        $this->applyProductAction($plan['product_actions'][$productId]);
                    }

                    if (isset($plan['media_actions'][$productId])) {
                        $rows = $plan['media_actions'][$productId]['backup_rows'];
                        DB::table('product_media')->where('product_id', $productId)->delete();

                        foreach ($rows as $row) {
                            unset($row['id']);
                            DB::table('product_media')->insert($row);
                        }

                        $this->summary['product_media_rows_committed'] += count($rows);
                    }
                });

                $this->summary['products_committed']++;
                $this->markAuditStatus($productId, 'RESTORED');
            } catch (Throwable $exception) {
                $this->errors[] = [
                    'type' => 'product_restore_failed',
                    'product_id' => $productId,
                    'message' => $exception->getMessage(),
                ];
                $this->markAuditStatus($productId, 'ERROR');
            }
        }

        $this->summary['errors'] = count($this->errors);
    }

    private function applyReferenceAction(string $table, array $action): void
    {
        $backup = $action['backup'];
        if ($action['action'] === 'create') {
            DB::table($table)->insert($backup);

            return;
        }

        $id = $backup['id'];
        unset($backup['id']);
        DB::table($table)->where('id', $id)->update($backup);
    }

    private function applyProductAction(array $action): void
    {
        $backup = $action['backup'];

        if ($action['action'] === 'create') {
            DB::table('products')->insert($backup);

            return;
        }

        DB::table('products')
            ->where('id', $backup['id'])
            ->update([
                'thumbnail_path' => $backup['thumbnail_path'] ?? null,
            ]);
    }

    private function copyBackupFile(array $fileAction): void
    {
        $source = $fileAction['source'];
        if ($source === null) {
            throw new RuntimeException('Missing backup source entry.');
        }

        $zip = new ZipArchive;
        if ($zip->open($source['zip_path']) !== true) {
            throw new RuntimeException("Could not open zip: {$source['zip_path']}");
        }

        $content = $zip->getFromName($source['entry']);
        $zip->close();

        if ($content === false) {
            throw new RuntimeException("Could not read zip entry: {$source['entry']}");
        }

        File::ensureDirectoryExists(dirname($fileAction['absolute_path']));
        File::put($fileAction['absolute_path'], $content);
    }

    private function writeReports(): void
    {
        $dir = base_path('docs/import');
        File::ensureDirectoryExists($dir);

        $auditCsv = $dir.DIRECTORY_SEPARATOR.'restore-product-data-audit.csv';
        $summaryJson = $dir.DIRECTORY_SEPARATOR.'restore-product-data-summary.json';
        $errorsJson = $dir.DIRECTORY_SEPARATOR.'restore-product-data-errors.json';

        $handle = fopen($auditCsv, 'wb');
        fputcsv($handle, self::REPORT_HEADERS);
        foreach ($this->auditRows as $row) {
            fputcsv($handle, array_map(fn (string $header): mixed => $row[$header] ?? '', self::REPORT_HEADERS));
        }
        fclose($handle);

        $this->summary['reports'] = [
            'audit_csv' => $auditCsv,
            'summary_json' => $summaryJson,
            'errors_json' => $errorsJson,
        ];

        File::put($summaryJson, json_encode($this->summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        File::put($errorsJson, json_encode($this->errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function keyRows(array $rows, string $key): array
    {
        $keyed = [];
        foreach ($rows as $row) {
            if (! is_array($row) || ! array_key_exists($key, $row)) {
                continue;
            }

            $keyed[(string) $row[$key]] = $row;
        }

        return $keyed;
    }

    private function groupRows(array $rows, string $key): array
    {
        $grouped = [];
        foreach ($rows as $row) {
            if (! is_array($row) || ! array_key_exists($key, $row)) {
                continue;
            }

            $grouped[(string) $row[$key]][] = $row;
        }

        return $grouped;
    }

    private function mediaSignature(array $rows): array
    {
        $signature = array_map(function (array $row): array {
            return [
                'product_id' => (string) ($row['product_id'] ?? ''),
                'file_path' => $this->normalizePublicPath($row['file_path'] ?? null) ?? '',
                'disk' => (string) ($row['disk'] ?? ''),
                'collection' => (string) ($row['collection'] ?? ''),
                'display_order' => (string) ($row['display_order'] ?? ''),
                'meta' => $this->canonicalJsonString($row['meta'] ?? null),
            ];
        }, $rows);

        usort($signature, fn (array $left, array $right): int => strcmp(json_encode($left), json_encode($right)));

        return $signature;
    }

    private function canonicalJsonString(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $decoded = json_decode((string) $value, true);
        if (! is_array($decoded)) {
            return (string) $value;
        }

        $this->sortRecursive($decoded);

        return json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function sortRecursive(array &$value): void
    {
        foreach ($value as &$item) {
            if (is_array($item)) {
                $this->sortRecursive($item);
            }
        }

        if (! array_is_list($value)) {
            ksort($value);
        }
    }

    private function normalizePublicPath(mixed $path): ?string
    {
        if ($path === null) {
            return null;
        }

        $path = trim(str_replace('\\', '/', (string) $path));
        if ($path === '') {
            return null;
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        return $path;
    }

    private function findBackupFileSource(string $path): ?array
    {
        if (isset($this->zipEntryCache[$path])) {
            return $this->zipEntryCache[$path];
        }

        $backupPath = (string) $this->summary['backup_path'];
        $zipFiles = [];
        foreach (['files.zip', 'source.zip'] as $zipFile) {
            $candidate = $backupPath.DIRECTORY_SEPARATOR.$zipFile;
            if (File::exists($candidate)) {
                $zipFiles[] = $candidate;
            }
        }

        $entryCandidates = array_values(array_unique([
            str_replace('\\', '/', $path),
            'public/'.str_replace('\\', '/', $path),
        ]));

        foreach ($zipFiles as $zipPath) {
            $zip = new ZipArchive;
            if ($zip->open($zipPath) !== true) {
                continue;
            }

            foreach ($entryCandidates as $entry) {
                $index = $zip->locateName($entry);
                if ($index === false) {
                    continue;
                }

                $stat = $zip->statIndex($index);
                $zip->close();

                return $this->zipEntryCache[$path] = [
                    'zip_path' => $zipPath,
                    'entry' => $entry,
                    'size' => (int) ($stat['size'] ?? 0),
                ];
            }

            $zip->close();
        }

        return $this->zipEntryCache[$path] = null;
    }

    private function filePathsForProduct(int $productId, array $plan): array
    {
        $paths = [];

        if (isset($plan['product_actions'][$productId])) {
            $path = $this->normalizePublicPath($plan['product_actions'][$productId]['backup']['thumbnail_path'] ?? null);
            if ($path !== null) {
                $paths[$path] = $path;
            }
        }

        if (isset($plan['media_actions'][$productId])) {
            foreach ($plan['media_actions'][$productId]['backup_rows'] as $row) {
                $path = $this->normalizePublicPath($row['file_path'] ?? null);
                if ($path !== null) {
                    $paths[$path] = $path;
                }
            }
        }

        return array_values($paths);
    }

    private function affectedProductNames(array $productIds, array $backupProducts): array
    {
        return array_map(function (int $productId) use ($backupProducts): array {
            $product = $backupProducts[(string) $productId] ?? [];

            return [
                'id' => $productId,
                'name' => $product['name'] ?? '',
                'slug' => $product['slug'] ?? '',
            ];
        }, $productIds);
    }

    private function markAuditStatus(int $productId, string $status): void
    {
        foreach ($this->auditRows as &$row) {
            if ((int) $row['product_id'] === $productId) {
                $row['status'] = $status;
            }
        }
    }
}
