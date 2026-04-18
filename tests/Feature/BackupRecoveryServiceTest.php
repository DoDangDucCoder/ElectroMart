<?php

namespace Tests\Feature;

use App\Application\Shared\Services\BackupRecoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackupRecoveryServiceTest extends TestCase
{
    use RefreshDatabase;

    private array $createdArtifacts = [];

    protected function tearDown(): void
    {
        foreach ($this->createdArtifacts as $artifact) {
            $this->deletePath($artifact);
            $this->deletePath($artifact . '.tar.gz');
            $this->deletePath($artifact . '.enc');
            $this->deletePath($artifact . '.tar.gz.enc');
        }

        parent::tearDown();
    }

    public function test_portable_backup_creation_listing_and_restore_work_without_mysql_cli(): void
    {
        $service = $this->backupServiceWithPortableConfig();

        $result = $service->createBackup();

        $this->assertTrue($result['success']);

        $backupId = $result['backup_id'];
        $backupPath = storage_path('app/backups/' . $backupId);
        $this->createdArtifacts[] = $backupPath;

        $manifestPath = $backupPath . '/manifest.json';
        $databasePath = $backupPath . '/database.sql';

        $this->assertFileExists($manifestPath);
        $this->assertFileExists($databasePath);

        $manifest = json_decode((string) file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame($backupId, $manifest['backup_id']);
        $this->assertArrayHasKey('completed_at', $manifest);
        $this->assertArrayHasKey('duration_seconds', $manifest);
        $this->assertTrue(collect($service->listBackups())->contains(fn (array $backup) => $backup['backup_id'] === $backupId));
        $this->assertTrue($service->restoreBackup($backupId)['success']);
    }

    public function test_list_backups_normalizes_compressed_and_encrypted_artifact_names(): void
    {
        $backupId = 'backup_' . now()->format('Y-m-d_H-i-s') . '_artifact';
        $artifact = storage_path('app/backups/' . $backupId . '.tar.gz.enc');

        if (! is_dir(dirname($artifact))) {
            mkdir(dirname($artifact), 0755, true);
        }

        file_put_contents($artifact, 'encrypted-backup');
        $this->createdArtifacts[] = storage_path('app/backups/' . $backupId);

        $service = $this->backupServiceWithPortableConfig();
        $match = collect($service->listBackups())->firstWhere('backup_id', $backupId);

        $this->assertNotNull($match);
        $this->assertSame('encrypted_archive', $match['type']);
        $this->assertSame(basename($artifact), $match['artifact_name']);
    }

    private function backupServiceWithPortableConfig(): BackupRecoveryService
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

        $this->app->forgetInstance(BackupRecoveryService::class);

        return $this->app->make(BackupRecoveryService::class);
    }

    private function deletePath(string $path): void
    {
        if (is_dir($path)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $item) {
                $item->isDir() ? @rmdir($item->getRealPath()) : @unlink($item->getRealPath());
            }

            @rmdir($path);
        } elseif (is_file($path)) {
            @unlink($path);
        }
    }
}
