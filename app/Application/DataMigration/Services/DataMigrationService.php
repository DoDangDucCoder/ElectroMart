<?php

namespace App\Application\DataMigration\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DataMigrationService
{
    public function migrateAll(): array
    {
        $results = [];
        
        $mappings = DB::table('migration_mappings')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        foreach ($mappings as $mapping) {
            try {
                $result = $this->migrateTable($mapping);
                $results[$mapping->source_table] = $result;
            } catch (\Exception $e) {
                Log::error("Migration failed for {$mapping->source_table}: " . $e->getMessage());
                $results[$mapping->source_table] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'records_migrated' => 0,
                ];
            }
        }

        return $results;
    }

    public function migrateTable($mapping): array
    {
        $migrationId = $this->createMigrationRecord($mapping);
        
        try {
            $this->updateMigrationStatus($migrationId, 'running');
            
            $fieldMappings = json_decode($mapping->field_mappings, true);
            $transformations = json_decode($mapping->transformations, true) ?? [];
            $filters = json_decode($mapping->filters, true) ?? [];

            // Check if source table exists
            if (!Schema::hasTable($mapping->source_table)) {
                throw new \Exception("Source table {$mapping->source_table} does not exist");
            }

            // Check if target table exists
            if (!Schema::hasTable($mapping->target_table)) {
                throw new \Exception("Target table {$mapping->target_table} does not exist");
            }

            // Get source data
            $query = DB::table($mapping->source_table);
            
            // Apply filters if any
            foreach ($filters as $field => $condition) {
                $query->where($field, $condition);
            }

            $sourceData = $query->get();
            $recordsMigrated = 0;

            foreach ($sourceData as $sourceRecord) {
                $targetData = $this->transformRecord($sourceRecord, $fieldMappings, $transformations);
                
                // Check if record already exists
                $existingRecord = $this->findExistingRecord($mapping->target_table, $targetData);
                
                if (!$existingRecord) {
                    DB::table($mapping->target_table)->insert($targetData);
                    $recordsMigrated++;
                }
            }

            $this->updateMigrationStatus($mappingId, 'completed', $recordsMigrated);
            
            return [
                'success' => true,
                'records_migrated' => $recordsMigrated,
                'total_source_records' => $sourceData->count(),
            ];

        } catch (\Exception $e) {
            $this->updateMigrationStatus($mappingId, 'failed', 0, $e->getMessage());
            throw $e;
        }
    }

    private function createMigrationRecord($mapping): int
    {
        return DB::table('data_migrations')->insertGetId([
            'migration_name' => "Migrate {$mapping->source_table} to {$mapping->target_table}",
            'source_table' => $mapping->source_table,
            'target_table' => $mapping->target_table,
            'status' => 'pending',
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function updateMigrationStatus(int $migrationId, string $status, int $recordsMigrated = 0, ?string $errorMessage = null): void
    {
        $updateData = [
            'status' => $status,
            'updated_at' => now(),
        ];

        if ($status === 'completed') {
            $updateData['completed_at'] = now();
            $updateData['records_migrated'] = $recordsMigrated;
        } elseif ($status === 'failed') {
            $updateData['error_message'] = $errorMessage;
        }

        DB::table('data_migrations')->where('id', $migrationId)->update($updateData);
    }

    private function transformRecord($sourceRecord, array $fieldMappings, array $transformations): array
    {
        $targetData = [];

        foreach ($fieldMappings as $sourceField => $targetField) {
            if (isset($sourceRecord->$sourceField)) {
                $value = $sourceRecord->$sourceField;

                // Apply transformations
                if (isset($transformations[$targetField])) {
                    $value = $this->applyTransformation($value, $transformations[$targetField], $sourceRecord);
                }

                $targetData[$targetField] = $value;
            }
        }

        return $targetData;
    }

    private function applyTransformation($value, string $transformation, $sourceRecord)
    {
        switch ($transformation) {
            case 'brand_status == 1':
            case 'category_status == 1':
            case 'cate_post_status == 1':
            case 'comment_status == 1':
                return $value == 1;
            
            case 'product_status == 0':
                return $value == 0;
            
            case 'CAST(product_views AS UNSIGNED)':
                return (int) $value;
            
            case 'CAST(price_cost AS DECIMAL(15,2))':
                return (float) $value;
            
            case 'CASE order_status WHEN "Đã xử lý" THEN "completed" WHEN "Đang xử lý" THEN "processing" ELSE "pending" END':
                return match($value) {
                    'Đã xử lý' => 'completed',
                    'Đang xử lý' => 'processing',
                    default => 'pending'
                };
            
            case 'CASE post_status WHEN 1 THEN "published" ELSE "draft" END':
                return $value == 1 ? 'published' : 'draft';
            
            case 'CASE post_status WHEN 1 THEN created_at ELSE NULL END':
                return $sourceRecord->post_status == 1 ? $sourceRecord->created_at : null;
            
            default:
                return $value;
        }
    }

    private function findExistingRecord(string $targetTable, array $targetData): ?object
    {
        // Try to find existing record by primary key (id)
        if (isset($targetData['id'])) {
            return DB::table($targetTable)->where('id', $targetData['id'])->first();
        }

        // Try to find by unique fields
        $uniqueFields = ['email', 'slug', 'code'];
        foreach ($uniqueFields as $field) {
            if (isset($targetData[$field])) {
                $existing = DB::table($targetTable)->where($field, $targetData[$field])->first();
                if ($existing) {
                    return $existing;
                }
            }
        }

        return null;
    }

    public function getMigrationStatus(): array
    {
        return DB::table('data_migrations')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function rollbackMigration(int $migrationId): bool
    {
        $migration = DB::table('data_migrations')->find($migrationId);
        
        if (!$migration || $migration->status !== 'completed') {
            return false;
        }

        try {
            // This is a simple rollback - in production you might want more sophisticated rollback
            DB::table($migration->target_table)
                ->whereIn('id', function($query) use ($migration) {
                    $query->select('id')
                        ->from($migration->target_table)
                        ->where('created_at', '>=', $migration->started_at);
                })
                ->delete();

            DB::table('data_migrations')
                ->where('id', $migrationId)
                ->update([
                    'status' => 'rolled_back',
                    'updated_at' => now(),
                ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Rollback failed for migration {$migrationId}: " . $e->getMessage());
            return false;
        }
    }
}
