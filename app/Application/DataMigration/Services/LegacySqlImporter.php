<?php

namespace App\Application\DataMigration\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class LegacySqlImporter
{
    public function __construct(private readonly SqlDumpSplitter $splitter)
    {
    }

    /**
     * @param  callable(string, string): void|null  $logger
     * @return array<string, int>
     */
    public function import(string $dumpPath, bool $force = false, ?callable $logger = null, bool $confirmed = false): array
    {
        if (! $confirmed) {
            throw new RuntimeException('Legacy SQL import requires explicit confirmation.');
        }

        if (! File::exists($dumpPath)) {
            throw new RuntimeException("SQL dump not found: {$dumpPath}");
        }

        $stats = [
            'statements_total' => 0,
            'statements_executed' => 0,
            'statements_skipped' => 0,
            'tables_created' => 0,
            'tables_dropped' => 0,
            'tables_skipped' => 0,
            'inserts_executed' => 0,
            'errors' => 0,
        ];

        $skippedTables = [];
        $droppedTables = [];
        $statements = $this->splitter->split(File::get($dumpPath));
        $stats['statements_total'] = count($statements);

        foreach ($statements as $statement) {
            $tableName = $this->legacyTableName($statement);

            if (! $tableName) {
                $stats['statements_skipped']++;
                continue;
            }

            $type = $this->statementType($statement);

            if ($type === 'create') {
                if (Schema::hasTable($tableName)) {
                    if (! $force) {
                        $skippedTables[$tableName] = true;
                        $stats['tables_skipped']++;
                        $stats['statements_skipped']++;
                        $this->log($logger, 'line', "Skipping {$tableName} (already exists)");
                        continue;
                    }

                    if (! isset($droppedTables[$tableName])) {
                        DB::statement($this->quoteDropTable($tableName));
                        $droppedTables[$tableName] = true;
                        $stats['tables_dropped']++;
                    }
                }
            }

            if (isset($skippedTables[$tableName]) && in_array($type, ['insert', 'alter'], true)) {
                $stats['statements_skipped']++;
                continue;
            }

            if ($type === 'insert' && ! Schema::hasTable($tableName)) {
                $stats['statements_skipped']++;
                $this->log($logger, 'warn', "Skipping INSERT for {$tableName} because the table does not exist");
                continue;
            }

            if ($type === 'alter' && $this->isSqlite()) {
                $stats['statements_skipped']++;
                continue;
            }

            try {
                DB::unprepared($this->normalizeStatementForConnection($statement));
                $stats['statements_executed']++;

                if ($type === 'create') {
                    $stats['tables_created']++;
                    $this->log($logger, 'info', "Created legacy table: {$tableName}");
                } elseif ($type === 'insert') {
                    $stats['inserts_executed']++;
                }
            } catch (Throwable $e) {
                $stats['errors']++;
                $this->log($logger, 'warn', "Failed {$type} for {$tableName}: ".$e->getMessage());
            }
        }

        return $stats;
    }

    private function legacyTableName(string $statement): ?string
    {
        if (preg_match('/\b(?:CREATE\s+TABLE|INSERT\s+INTO|ALTER\s+TABLE|DROP\s+TABLE(?:\s+IF\s+EXISTS)?)\s+`?(tbl_[A-Za-z0-9_]+)`?/i', $statement, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function statementType(string $statement): string
    {
        $statement = $this->withoutLeadingComments($statement);

        return match (true) {
            preg_match('/^CREATE\s+TABLE/i', $statement) === 1 => 'create',
            preg_match('/^INSERT\s+INTO/i', $statement) === 1 => 'insert',
            preg_match('/^ALTER\s+TABLE/i', $statement) === 1 => 'alter',
            preg_match('/^DROP\s+TABLE/i', $statement) === 1 => 'drop',
            default => 'other',
        };
    }

    private function normalizeStatementForConnection(string $statement): string
    {
        $statement = $this->withoutLeadingComments($statement);

        if (! $this->isSqlite() || ! preg_match('/^CREATE\s+TABLE/i', $statement)) {
            return $statement;
        }

        $statement = preg_replace('/\)\s*ENGINE=.*$/is', ')', $statement) ?? $statement;
        $statement = preg_replace('/\s+UNSIGNED\b/i', '', $statement) ?? $statement;
        $statement = preg_replace('/\bint\(\d+\)\b/i', 'integer', $statement) ?? $statement;
        $statement = preg_replace('/\bbigint\(\d+\)\b/i', 'integer', $statement) ?? $statement;
        $statement = preg_replace('/\btinyint\(\d+\)\b/i', 'integer', $statement) ?? $statement;
        $statement = preg_replace('/\bmediumtext\b/i', 'text', $statement) ?? $statement;
        $statement = preg_replace('/\blongtext\b/i', 'text', $statement) ?? $statement;
        $statement = preg_replace('/\s+CHARACTER\s+SET\s+\w+/i', '', $statement) ?? $statement;
        $statement = preg_replace('/\s+COLLATE\s+\w+/i', '', $statement) ?? $statement;
        $statement = str_replace('current_timestamp()', 'CURRENT_TIMESTAMP', $statement);

        return $statement;
    }

    private function withoutLeadingComments(string $statement): string
    {
        do {
            $before = $statement;
            $statement = ltrim($statement);

            if (str_starts_with($statement, '--')) {
                $position = strpos($statement, "\n");
                $statement = $position === false ? '' : substr($statement, $position + 1);
                continue;
            }

            if (str_starts_with($statement, '#')) {
                $position = strpos($statement, "\n");
                $statement = $position === false ? '' : substr($statement, $position + 1);
                continue;
            }

            if (str_starts_with($statement, '/*')) {
                $position = strpos($statement, '*/');
                $statement = $position === false ? '' : substr($statement, $position + 2);
                continue;
            }
        } while ($before !== $statement);

        return trim($statement);
    }

    private function quoteDropTable(string $tableName): string
    {
        if ($this->isSqlite()) {
            return 'DROP TABLE IF EXISTS "'.str_replace('"', '""', $tableName).'"';
        }

        return 'DROP TABLE IF EXISTS `'.str_replace('`', '``', $tableName).'`';
    }

    private function isSqlite(): bool
    {
        return DB::connection()->getDriverName() === 'sqlite';
    }

    /**
     * @param  callable(string, string): void|null  $logger
     */
    private function log(?callable $logger, string $level, string $message): void
    {
        if ($logger) {
            $logger($level, $message);
        }
    }
}
