<?php

namespace App\Application\Shared\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DatabaseTestingService
{
    private array $testConfig = [];
    private array $testResults = [];
    private array $testTables = [];
    private ?string $latestBackupId = null;

    public function __construct()
    {
        $this->testConfig = [
            'connection_tests' => ['test_connection' => true, 'test_transactions' => true, 'test_rollback' => true],
            'schema_tests' => ['test_table_structure' => true, 'test_indexes' => true, 'test_foreign_keys' => true, 'test_constraints' => true],
            'data_tests' => ['test_crud_operations' => true, 'test_data_integrity' => true, 'test_relationships' => true, 'test_data_validation' => true],
            'performance_tests' => ['test_query_performance' => true, 'test_bulk_operations' => true, 'test_concurrent_access' => true],
            'backup_tests' => ['test_backup_creation' => true, 'test_backup_restoration' => true, 'test_data_consistency' => true],
        ];

        $this->testTables = [
            'products' => [
                'columns' => ['id', 'name', 'slug', 'price', 'brand_id', 'primary_category_id', 'is_active'],
                'foreign_keys' => ['brand_id' => 'brands', 'primary_category_id' => 'product_categories'],
                'indexed_columns' => ['id', 'brand_id', 'primary_category_id', 'is_active', 'display_order'],
            ],
            'product_categories' => [
                'columns' => ['id', 'name', 'slug', 'parent_id', 'is_active'],
                'foreign_keys' => ['parent_id' => 'product_categories'],
                'indexed_columns' => ['id', 'slug', 'parent_id', 'display_order', 'is_active'],
            ],
            'brands' => [
                'columns' => ['id', 'name', 'slug', 'is_active'],
                'foreign_keys' => [],
                'indexed_columns' => ['id', 'slug'],
            ],
            'customers' => [
                'columns' => ['id', 'name', 'email', 'password', 'is_active'],
                'foreign_keys' => [],
                'indexed_columns' => ['id', 'email', 'phone', 'is_active', 'created_at'],
            ],
            'orders' => [
                'columns' => ['id', 'code', 'customer_id', 'status', 'total', 'created_at'],
                'foreign_keys' => ['customer_id' => 'customers'],
                'indexed_columns' => ['id', 'code', 'customer_id', 'status', 'placed_at', 'created_at'],
            ],
            'order_items' => [
                'columns' => ['id', 'order_id', 'product_id', 'product_name', 'price', 'quantity', 'subtotal'],
                'foreign_keys' => ['order_id' => 'orders', 'product_id' => 'products'],
                'indexed_columns' => ['id', 'order_id'],
            ],
        ];
    }

    public function runDatabaseTests(): array
    {
        Log::info('Starting database tests...');

        $this->ensureScratchTables();
        $this->latestBackupId = null;

        $this->testResults = [
            'connection_tests' => $this->wrap([
                'basic_connection' => $this->testBasicConnection(),
                'transaction_support' => $this->testTransactionSupport(),
                'rollback_functionality' => $this->testRollbackFunctionality(),
                'connection_pooling' => $this->testConnectionPooling(),
            ], 'Connection tests completed.'),
            'schema_tests' => $this->wrap([
                'table_structure' => $this->testTableStructure(),
                'indexes' => $this->testIndexes(),
                'foreign_keys' => $this->testForeignKeys(),
                'constraints' => $this->testConstraints(),
            ], 'Schema tests completed.'),
            'data_tests' => $this->wrap([
                'crud_operations' => $this->testCrudOperations(),
                'data_integrity' => $this->testDataIntegrity(),
                'relationships' => $this->testRelationships(),
                'data_validation' => $this->testDataValidation(),
            ], 'Data tests completed.'),
            'performance_tests' => $this->wrap([
                'query_performance' => $this->testQueryPerformance(),
                'bulk_operations' => $this->testBulkOperations(),
                'concurrent_access' => $this->testConcurrentAccess(),
            ], 'Performance tests completed.'),
            'backup_tests' => $this->wrap([
                'backup_creation' => $this->testBackupCreation(),
                'backup_restoration' => $this->testBackupRestoration(),
                'data_consistency' => $this->testDataConsistencyAfterBackup(),
            ], 'Backup tests completed.'),
        ];

        Log::info('Database tests completed', ['summary' => $this->generateTestReport()['summary']]);

        return $this->testResults;
    }

    public function generateTestReport(): array
    {
        $total = 0;
        $passed = 0;

        foreach ($this->testResults as $tests) {
            foreach ($tests as $result) {
                if (! is_array($result) || ! array_key_exists('success', $result)) {
                    continue;
                }

                $total++;
                $passed += ($result['success'] ?? false) ? 1 : 0;
            }
        }

        return [
            'summary' => [
                'total_tests' => $total,
                'passed_tests' => $passed,
                'failed_tests' => max($total - $passed, 0),
                'success_rate' => $total > 0 ? round(($passed / $total) * 100, 2) : 0,
            ],
            'categories' => array_keys($this->testResults),
            'recommendations' => $this->generateRecommendations(),
        ];
    }

    public function getTestStatistics(): array
    {
        return [
            'config' => $this->testConfig,
            'test_tables' => array_keys($this->testTables),
            'database_driver' => DB::getDriverName(),
            'total_test_categories' => count($this->testConfig),
        ];
    }

    private function wrap(array $tests, string $message): array
    {
        $leaves = array_filter($tests, fn ($value) => is_array($value) && array_key_exists('success', $value));
        $tests['success'] = $leaves !== [] && collect($leaves)->every(fn (array $result) => ($result['success'] ?? false) === true);
        $tests['message'] = $message;

        return $tests;
    }

    private function ensureScratchTables(): void
    {
        $tables = [
            'test_transaction' => fn (Blueprint $table) => [$table->id(), $table->string('name'), $table->string('email')->nullable(), $table->timestamps()],
            'test_rollback' => fn (Blueprint $table) => [$table->id(), $table->string('name'), $table->timestamps()],
            'test_crud' => fn (Blueprint $table) => [$table->id(), $table->string('name'), $table->boolean('is_active')->default(true), $table->json('meta')->nullable(), $table->timestamps()],
            'test_bulk' => fn (Blueprint $table) => [$table->id(), $table->string('name'), $table->boolean('is_active')->default(true), $table->timestamps()],
        ];

        foreach ($tables as $name => $definition) {
            if (Schema::hasTable($name)) {
                continue;
            }

            Schema::create($name, function (Blueprint $table) use ($definition) {
                $definition($table);
            });
        }
    }

    private function testBasicConnection(): array
    {
        return $this->attempt(function () {
            $start = microtime(true);
            $result = DB::select('SELECT 1 as test');

            return [
                'success' => (int) ($result[0]->test ?? 0) === 1,
                'response_time_ms' => round((microtime(true) - $start) * 1000, 2),
                'message' => 'Database connection is working.',
            ];
        }, 'Database connection failed.');
    }

    private function testTransactionSupport(): array
    {
        return $this->attempt(function () {
            $id = null;
            DB::beginTransaction();

            $id = DB::table('test_transaction')->insertGetId([
                'name' => 'transaction_' . uniqid(),
                'email' => uniqid() . '@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inside = DB::table('test_transaction')->where('id', $id)->exists();
            DB::rollBack();
            $after = DB::table('test_transaction')->where('id', $id)->exists();

            return [
                'success' => $inside && ! $after,
                'message' => 'Transaction support check completed.',
            ];
        }, 'Transaction support test failed.', true);
    }

    private function testRollbackFunctionality(): array
    {
        return $this->attempt(function () {
            $initial = DB::table('test_rollback')->count();
            DB::beginTransaction();
            DB::table('test_rollback')->insert([
                'name' => 'rollback_' . uniqid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $afterInsert = DB::table('test_rollback')->count();
            DB::rollBack();
            $afterRollback = DB::table('test_rollback')->count();

            return [
                'success' => $afterInsert === $initial + 1 && $afterRollback === $initial,
                'message' => 'Rollback functionality check completed.',
            ];
        }, 'Rollback functionality test failed.', true);
    }

    private function testConnectionPooling(): array
    {
        return $this->attempt(function () {
            $queries = 5;
            $start = microtime(true);
            for ($i = 0; $i < $queries; $i++) {
                DB::connection()->select('SELECT 1 as test');
            }

            return [
                'success' => true,
                'average_time_per_query_ms' => round(((microtime(true) - $start) * 1000) / $queries, 2),
                'message' => 'Repeated connection queries completed.',
            ];
        }, 'Connection pooling simulation failed.');
    }

    private function testTableStructure(): array
    {
        $details = [];
        $success = true;

        foreach ($this->testTables as $table => $config) {
            $columns = Schema::hasTable($table) ? Schema::getColumnListing($table) : [];
            $missing = Schema::hasTable($table) ? array_values(array_diff($config['columns'], $columns)) : $config['columns'];
            $details[$table] = ['success' => Schema::hasTable($table) && $missing === [], 'missing_columns' => $missing];
            $success = $success && $details[$table]['success'];
        }

        return ['success' => $success, 'tables' => $details, 'message' => $success ? 'Core table structures match the current schema.' : 'Some core tables are missing required columns.'];
    }

    private function testIndexes(): array
    {
        $details = [];
        $success = true;

        foreach ($this->testTables as $table => $config) {
            $indexed = Schema::hasTable($table) ? $this->getIndexedColumns($table) : [];
            $missing = Schema::hasTable($table) ? array_values(array_diff($config['indexed_columns'], $indexed)) : $config['indexed_columns'];
            $details[$table] = ['success' => Schema::hasTable($table) && $missing === [], 'missing_indexed_columns' => $missing];
            $success = $success && $details[$table]['success'];
        }

        return ['success' => $success, 'tables' => $details, 'message' => $success ? 'Index checks completed successfully.' : 'Index coverage is incomplete for one or more core tables.'];
    }

    private function testForeignKeys(): array
    {
        $details = [];
        $success = true;

        foreach ($this->testTables as $table => $config) {
            $foreignKeys = Schema::hasTable($table) ? $this->getTableForeignKeys($table) : [];
            $missing = [];
            foreach ($config['foreign_keys'] as $column => $referencedTable) {
                if (! $this->hasForeignKey($foreignKeys, $column, $referencedTable)) {
                    $missing[] = $column . ' -> ' . $referencedTable;
                }
            }
            $details[$table] = ['success' => Schema::hasTable($table) && $missing === [], 'missing_foreign_keys' => $missing];
            $success = $success && $details[$table]['success'];
        }

        return ['success' => $success, 'tables' => $details, 'message' => $success ? 'Foreign key checks completed successfully.' : 'Foreign key validation failed for one or more tables.'];
    }

    private function testConstraints(): array
    {
        return $this->wrap([
            'not_null_constraints' => $this->attempt(function () {
                $checks = ['products' => ['name', 'slug', 'price'], 'product_categories' => ['name', 'slug'], 'customers' => ['name', 'email', 'password'], 'orders' => ['code', 'status', 'total']];
                foreach ($checks as $table => $columns) {
                    if (! Schema::hasTable($table)) {
                        return ['success' => false, 'message' => 'Constraint table missing.'];
                    }
                    foreach ($columns as $column) {
                        if (! Schema::hasColumn($table, $column) || DB::table($table)->whereNull($column)->exists()) {
                            return ['success' => false, 'message' => 'NOT NULL issues found.'];
                        }
                    }
                }
                return ['success' => true, 'message' => 'NOT NULL checks passed.'];
            }, 'NOT NULL checks failed.'),
            'unique_constraints' => $this->attempt(function () {
                $checks = ['product_categories' => ['slug'], 'brands' => ['slug'], 'customers' => ['email'], 'orders' => ['code']];
                foreach ($checks as $table => $columns) {
                    if (! Schema::hasTable($table)) {
                        return ['success' => false, 'message' => 'Constraint table missing.'];
                    }
                    foreach ($columns as $column) {
                        $duplicates = DB::table($table)->whereNotNull($column)->select($column)->groupBy($column)->havingRaw('COUNT(*) > 1')->count();
                        if ($duplicates > 0) {
                            return ['success' => false, 'message' => 'Unique constraint issues found.'];
                        }
                    }
                }
                return ['success' => true, 'message' => 'Unique checks passed.'];
            }, 'Unique checks failed.'),
            'check_constraints' => $this->attempt(function () {
                $negativePrices = Schema::hasTable('products') ? DB::table('products')->where('price', '<', 0)->count() : 0;
                $negativeTotals = Schema::hasTable('orders') ? DB::table('orders')->where('total', '<', 0)->count() : 0;
                return ['success' => $negativePrices === 0 && $negativeTotals === 0, 'message' => $negativePrices === 0 && $negativeTotals === 0 ? 'Value checks passed.' : 'Negative values were found.'];
            }, 'Check constraints failed.'),
        ], 'Constraint checks completed.');
    }

    private function testCrudOperations(): array
    {
        return $this->attempt(function () {
            $id = DB::table('test_crud')->insertGetId([
                'name' => 'crud_' . uniqid(),
                'is_active' => true,
                'meta' => json_encode(['stage' => 'created']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $updated = DB::table('test_crud')->where('id', $id)->update(['is_active' => false, 'updated_at' => now()]);
            $deleted = DB::table('test_crud')->where('id', $id)->delete();

            return ['success' => $updated === 1 && $deleted === 1, 'message' => 'CRUD operations completed.'];
        }, 'CRUD operations test failed.');
    }

    private function testDataIntegrity(): array
    {
        return $this->attempt(function () {
            $orphanedOrders = DB::table('orders')->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')->whereNotNull('orders.customer_id')->whereNull('customers.id')->count();
            $orphanedOrderItems = DB::table('order_items')->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')->whereNull('orders.id')->count();
            $orphanedProductLinks = DB::table('order_items')->leftJoin('products', 'order_items.product_id', '=', 'products.id')->whereNotNull('order_items.product_id')->whereNull('products.id')->count();
            return ['success' => $orphanedOrders === 0 && $orphanedOrderItems === 0 && $orphanedProductLinks === 0, 'message' => 'Referential integrity checks completed.'];
        }, 'Data integrity test failed.');
    }

    private function testRelationships(): array
    {
        return $this->attempt(function () {
            DB::table('products')->leftJoin('product_categories', 'products.primary_category_id', '=', 'product_categories.id')->count();
            DB::table('products')->leftJoin('brands', 'products.brand_id', '=', 'brands.id')->count();
            DB::table('orders')->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')->count();
            return ['success' => true, 'message' => 'Relationship queries executed successfully.'];
        }, 'Relationship test failed.');
    }

    private function testDataValidation(): array
    {
        return $this->attempt(function () {
            $invalidEmails = DB::table('customers')->whereNotNull('email')->pluck('email')->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL) === false)->count();
            $invalidPrices = DB::table('products')->where('price', '<', 0)->count();
            return ['success' => $invalidEmails === 0 && $invalidPrices === 0, 'message' => 'Validation checks completed.'];
        }, 'Data validation test failed.');
    }

    private function testQueryPerformance(): array
    {
        return $this->attempt(function () {
            $queries = [
                fn () => DB::table('products')->limit(100)->get(),
                fn () => DB::table('products')->leftJoin('product_categories', 'products.primary_category_id', '=', 'product_categories.id')->select('products.id')->limit(100)->get(),
                fn () => DB::table('products')->selectRaw('primary_category_id, COUNT(*) as items_count, AVG(price) as avg_price')->groupBy('primary_category_id')->get(),
                fn () => DB::table('orders')->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')->selectRaw('orders.id, COALESCE(SUM(order_items.subtotal), 0) as items_total')->groupBy('orders.id')->limit(100)->get(),
            ];

            $ratings = [];
            foreach ($queries as $query) {
                $start = microtime(true);
                $query();
                $ratings[] = $this->getPerformanceRating((microtime(true) - $start) * 1000);
            }

            return ['success' => true, 'ratings' => $ratings, 'message' => 'Query performance checks completed.'];
        }, 'One or more performance queries failed.');
    }

    private function testBulkOperations(): array
    {
        return $this->attempt(function () {
            $prefix = 'bulk_' . uniqid();
            $rows = [];
            for ($i = 0; $i < 200; $i++) {
                $rows[] = ['name' => $prefix . '_' . $i, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()];
            }

            $insertStart = microtime(true);
            DB::table('test_bulk')->insert($rows);
            $insert = (microtime(true) - $insertStart) * 1000;

            $updateStart = microtime(true);
            $updated = DB::table('test_bulk')->where('name', 'like', $prefix . '%')->update(['is_active' => false, 'updated_at' => now()]);
            $update = (microtime(true) - $updateStart) * 1000;

            $deleteStart = microtime(true);
            $deleted = DB::table('test_bulk')->where('name', 'like', $prefix . '%')->delete();
            $delete = (microtime(true) - $deleteStart) * 1000;

            return [
                'success' => $updated === 200 && $deleted === 200,
                'performance_rating' => $this->getPerformanceRating(($insert + $update + $delete) / 3),
                'message' => 'Bulk operations completed.',
            ];
        }, 'Bulk operations test failed.');
    }

    private function testConcurrentAccess(): array
    {
        return $this->attempt(function () {
            for ($i = 0; $i < 5; $i++) {
                DB::table('products')->where('id', '>=', 0)->limit(10)->get();
            }

            return ['success' => true, 'message' => 'Concurrent access simulation completed.'];
        }, 'Concurrent access test failed.');
    }

    private function testBackupCreation(): array
    {
        return $this->attempt(function () {
            $backupService = $this->resolvePortableBackupService();
            $result = $backupService->createBackup();
            $backupId = $result['backup_id'] ?? null;
            $manifestPath = $backupId ? storage_path('app/backups/' . $backupId . '/manifest.json') : null;
            $databasePath = $backupId ? storage_path('app/backups/' . $backupId . '/database.sql') : null;
            $this->latestBackupId = ($result['success'] ?? false) ? $backupId : null;

            return [
                'success' => ($result['success'] ?? false) && $manifestPath && file_exists($manifestPath) && $databasePath && file_exists($databasePath),
                'backup_id' => $backupId,
                'message' => ($result['success'] ?? false) ? 'Portable backup creation succeeded.' : ($result['error'] ?? 'Backup creation failed.'),
            ];
        }, 'Backup creation test failed.');
    }

    private function testBackupRestoration(): array
    {
        if ($this->latestBackupId === null) {
            return ['success' => false, 'message' => 'Backup creation did not produce a restorable backup.'];
        }

        return $this->attempt(function () {
            $result = $this->resolvePortableBackupService()->restoreBackup($this->latestBackupId);
            return ['success' => $result['success'] ?? false, 'message' => ($result['success'] ?? false) ? 'Portable backup restoration succeeded.' : ($result['error'] ?? 'Backup restoration failed.')];
        }, 'Backup restoration test failed.', false, true);
    }

    private function testDataConsistencyAfterBackup(): array
    {
        return $this->attempt(function () {
            $counts = [];
            foreach (array_keys($this->testTables) as $table) {
                $counts[$table] = DB::table($table)->count();
            }
            return ['success' => true, 'table_counts' => $counts, 'message' => 'Data consistency snapshot collected.'];
        }, 'Data consistency after backup test failed.');
    }

    private function resolvePortableBackupService(): BackupRecoveryService
    {
        config([
            'backup.enabled' => true,
            'backup.compression' => false,
            'backup.encryption' => false,
            'backup.storage.local' => true,
            'backup.storage.s3' => false,
            'backup.storage.ftp' => false,
            'backup.components.database' => true,
            'backup.components.files' => false,
            'backup.components.config' => false,
            'backup.components.logs' => false,
            'backup.notifications.email' => false,
            'backup.notifications.slack' => false,
        ]);

        app()->forgetInstance(BackupRecoveryService::class);

        return app(BackupRecoveryService::class);
    }

    private function attempt(callable $callback, string $fallbackMessage, bool $rollback = false, bool $cleanupBackup = false): array
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            if ($rollback) {
                while (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
            }

            return ['success' => false, 'error' => $e->getMessage(), 'message' => $fallbackMessage];
        } finally {
            if ($cleanupBackup) {
                $this->cleanupBackupArtifacts($this->latestBackupId);
            }
        }
    }

    private function cleanupBackupArtifacts(?string $backupId): void
    {
        if ($backupId === null) {
            return;
        }

        foreach ([
            storage_path('app/backups/' . $backupId),
            storage_path('app/backups/' . $backupId . '.tar.gz'),
            storage_path('app/backups/' . $backupId . '.enc'),
            storage_path('app/backups/' . $backupId . '.tar.gz.enc'),
        ] as $path) {
            if (is_dir($path)) {
                $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($iterator as $item) {
                    $item->isDir() ? @rmdir($item->getRealPath()) : @unlink($item->getRealPath());
                }
                @rmdir($path);
            } elseif (is_file($path)) {
                @unlink($path);
            }
        }
    }

    private function getIndexedColumns(string $table): array
    {
        try {
            if (DB::getDriverName() === 'mysql') {
                return array_values(array_unique(array_map(fn ($row) => $row->Column_name, DB::select('SHOW INDEX FROM `' . str_replace('`', '``', $table) . '`'))));
            }

            if (DB::getDriverName() === 'sqlite') {
                $columns = array_map(fn ($row) => $row->name, array_filter(DB::select('PRAGMA table_info("' . str_replace('"', '""', $table) . '")'), fn ($row) => (int) ($row->pk ?? 0) > 0));
                foreach (DB::select('PRAGMA index_list("' . str_replace('"', '""', $table) . '")') as $index) {
                    foreach (DB::select('PRAGMA index_info("' . str_replace('"', '""', $index->name) . '")') as $column) {
                        $columns[] = $column->name;
                    }
                }
                return array_values(array_unique($columns));
            }
        } catch (\Throwable $e) {
            return [];
        }

        return [];
    }

    private function getTableForeignKeys(string $table): array
    {
        try {
            if (DB::getDriverName() === 'mysql') {
                return array_map(fn ($row) => ['column' => $row->COLUMN_NAME, 'referenced_table' => $row->REFERENCED_TABLE_NAME], DB::select('SELECT COLUMN_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL', [DB::getDatabaseName(), $table]));
            }

            if (DB::getDriverName() === 'sqlite') {
                return array_map(fn ($row) => ['column' => $row->from, 'referenced_table' => $row->table], DB::select('PRAGMA foreign_key_list("' . str_replace('"', '""', $table) . '")'));
            }
        } catch (\Throwable $e) {
            return [];
        }

        return [];
    }

    private function hasForeignKey(array $foreignKeys, string $column, string $referencedTable): bool
    {
        foreach ($foreignKeys as $foreignKey) {
            if (($foreignKey['column'] ?? null) === $column && ($foreignKey['referenced_table'] ?? null) === $referencedTable) {
                return true;
            }
        }

        return false;
    }

    private function getPerformanceRating(float $executionTime): string
    {
        if ($executionTime < 100) {
            return 'Excellent';
        }
        if ($executionTime < 500) {
            return 'Good';
        }
        if ($executionTime < 1000) {
            return 'Fair';
        }
        return 'Poor';
    }

    private function generateRecommendations(): array
    {
        $recommendations = [];

        foreach ($this->testResults as $category => $tests) {
            foreach ($tests as $test => $result) {
                if (is_array($result) && array_key_exists('success', $result) && ($result['success'] ?? false) === false) {
                    $recommendations[] = 'Investigate ' . $category . '.' . $test . ': ' . ($result['message'] ?? 'Test failed.');
                }
            }
        }

        return $recommendations === [] ? ['Database tests are passing well. Continue monitoring.'] : $recommendations;
    }
}
