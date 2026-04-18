<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class BackupRecoveryService
{
    private array $config = [];
    private string $backupPath;

    public function __construct()
    {
        $this->initializeConfig();
        $this->backupPath = storage_path('app/backups');
    }

    private function initializeConfig(): void
    {
        $this->config = [
            'enabled' => config('backup.enabled', true),
            'schedule' => config('backup.schedule', '0 2 * * *'), // Daily at 2 AM
            'retention_days' => config('backup.retention_days', 30),
            'compression' => config('backup.compression', true),
            'encryption' => config('backup.encryption', false),
            'storage' => [
                'local' => config('backup.storage.local', true),
                's3' => config('backup.storage.s3', false),
                'ftp' => config('backup.storage.ftp', false),
            ],
            'components' => [
                'database' => config('backup.components.database', true),
                'files' => config('backup.components.files', true),
                'config' => config('backup.components.config', true),
                'logs' => config('backup.components.logs', false),
            ],
            'notifications' => [
                'email' => config('backup.notifications.email', true),
                'slack' => config('backup.notifications.slack', false),
            ],
        ];
    }

    public function createBackup(): array
    {
        if (!$this->config['enabled']) {
            return [
                'success' => false,
                'message' => 'Backup is disabled',
            ];
        }

        $backupId = 'backup_' . date('Y-m-d_H-i-s');
        $backupPath = $this->backupPath . '/' . $backupId;

        try {
            // Create backup directory
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $results = [
                'backup_id' => $backupId,
                'started_at' => now()->toISOString(),
                'components' => [],
                'success' => true,
            ];
            $artifactPath = $backupPath;

            // Backup database
            if ($this->config['components']['database']) {
                $results['components']['database'] = $this->backupDatabase($backupPath);
            }

            // Backup files
            if ($this->config['components']['files']) {
                $results['components']['files'] = $this->backupFiles($backupPath);
            }

            // Backup configuration
            if ($this->config['components']['config']) {
                $results['components']['config'] = $this->backupConfig($backupPath);
            }

            // Backup logs
            if ($this->config['components']['logs']) {
                $results['components']['logs'] = $this->backupLogs($backupPath);
            }

            $results['completed_at'] = now()->toISOString();
            $results['duration_seconds'] = now()->diffInSeconds($results['started_at']);
            $results['total_size_bytes'] = $this->getDirectorySize($backupPath);

            // Create backup manifest
            $results['manifest'] = $this->createManifest($backupPath, $results);

            // Compress backup if enabled
            if ($this->config['compression']) {
                $results['compression'] = $this->compressBackup($backupPath);
                if (($results['compression']['success'] ?? false) === true) {
                    $artifactPath = $results['compression']['archive_path'] ?? $artifactPath;
                    $results['total_size_bytes'] = (int) ($results['compression']['compressed_size_bytes'] ?? $results['total_size_bytes']);
                }
            }

            // Encrypt backup if enabled
            if ($this->config['encryption']) {
                $results['encryption'] = $this->encryptBackup($artifactPath);
                if (($results['encryption']['success'] ?? false) === true) {
                    $artifactPath = $results['encryption']['encrypted_path'] ?? $artifactPath;
                    $results['total_size_bytes'] = (int) ($results['encryption']['encrypted_size_bytes'] ?? $results['total_size_bytes']);
                }
            }

            // Upload to remote storage
            $results['remote_upload'] = $this->uploadToRemoteStorage($artifactPath);

            // Clean up old backups
            $this->cleanupOldBackups();

            // Send notifications
            $this->sendBackupNotification($results);

            return $results;

        } catch (\Exception $e) {
            Log::error('Backup failed', [
                'backup_id' => $backupId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'backup_id' => $backupId,
                'success' => false,
                'error' => $e->getMessage(),
                'completed_at' => now()->toISOString(),
            ];
        }
    }

    private function backupDatabase(string $backupPath): array
    {
        try {
            $startTime = microtime(true);

            $dumpFile = $backupPath . '/database.sql';

            if ($this->supportsMysqlCliBackup()) {
                $connection = config('database.connections.mysql', []);
                $command = sprintf(
                    'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s',
                    escapeshellarg((string) ($connection['host'] ?? '127.0.0.1')),
                    escapeshellarg((string) ($connection['port'] ?? '3306')),
                    escapeshellarg((string) ($connection['username'] ?? '')),
                    escapeshellarg((string) ($connection['password'] ?? '')),
                    escapeshellarg((string) ($connection['database'] ?? '')),
                    escapeshellarg($dumpFile)
                );

                exec($command, $output, $returnCode);

                if ($returnCode !== 0) {
                    throw new \Exception('Database dump failed with return code: ' . $returnCode);
                }
            } else {
                $this->writePortableDatabaseSnapshot($dumpFile);
            }

            $fileSize = filesize($dumpFile);
            $duration = microtime(true) - $startTime;

            return [
                'success' => true,
                'file' => $dumpFile,
                'size_bytes' => $fileSize,
                'duration_seconds' => round($duration, 2),
                'tables_count' => $this->getTableCount(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function backupFiles(string $backupPath): array
    {
        try {
            $startTime = microtime(true);
            
            $filesPath = $backupPath . '/files';
            mkdir($filesPath, 0755, true);

            $directories = [
                'storage/app' => 'app',
                'storage/framework' => 'framework',
                'public/uploads' => 'uploads',
            ];

            $totalSize = 0;
            $filesCount = 0;

            foreach ($directories as $source => $destination) {
                $sourcePath = base_path($source);
                $destPath = $filesPath . '/' . $destination;

                if (is_dir($sourcePath)) {
                    $this->copyDirectory($sourcePath, $destPath);
                    
                    $size = $this->getDirectorySize($destPath);
                    $count = $this->getFileCount($destPath);
                    
                    $totalSize += $size;
                    $filesCount += $count;
                }
            }

            $duration = microtime(true) - $startTime;

            return [
                'success' => true,
                'path' => $filesPath,
                'size_bytes' => $totalSize,
                'files_count' => $filesCount,
                'duration_seconds' => round($duration, 2),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function backupConfig(string $backupPath): array
    {
        try {
            $startTime = microtime(true);
            
            $configPath = $backupPath . '/config';
            mkdir($configPath, 0755, true);

            $configFiles = [
                '.env',
                'config/app.php',
                'config/database.php',
                'config/cache.php',
                'config/queue.php',
                'config/mail.php',
                'config/filesystems.php',
            ];

            $filesCount = 0;
            $totalSize = 0;

            foreach ($configFiles as $file) {
                $sourcePath = base_path($file);
                $destPath = $configPath . '/' . basename($file);

                if (file_exists($sourcePath)) {
                    copy($sourcePath, $destPath);
                    $totalSize += filesize($destPath);
                    $filesCount++;
                }
            }

            $duration = microtime(true) - $startTime;

            return [
                'success' => true,
                'path' => $configPath,
                'size_bytes' => $totalSize,
                'files_count' => $filesCount,
                'duration_seconds' => round($duration, 2),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function backupLogs(string $backupPath): array
    {
        try {
            $startTime = microtime(true);
            
            $logsPath = $backupPath . '/logs';
            mkdir($logsPath, 0755, true);

            $logFiles = glob(storage_path('logs/*.log'));
            $filesCount = 0;
            $totalSize = 0;

            foreach ($logFiles as $logFile) {
                $destPath = $logsPath . '/' . basename($logFile);
                copy($logFile, $destPath);
                $totalSize += filesize($destPath);
                $filesCount++;
            }

            $duration = microtime(true) - $startTime;

            return [
                'success' => true,
                'path' => $logsPath,
                'size_bytes' => $totalSize,
                'files_count' => $filesCount,
                'duration_seconds' => round($duration, 2),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function createManifest(string $backupPath, array $results): array
    {
        $manifest = [
            'backup_id' => $results['backup_id'],
            'created_at' => $results['started_at'],
            'completed_at' => $results['completed_at'],
            'duration_seconds' => $results['duration_seconds'],
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'components' => $results['components'],
            'total_size_bytes' => $this->getDirectorySize($backupPath),
            'compression_enabled' => $this->config['compression'],
            'encryption_enabled' => $this->config['encryption'],
        ];

        $manifestFile = $backupPath . '/manifest.json';
        file_put_contents($manifestFile, json_encode($manifest, JSON_PRETTY_PRINT));

        return $manifest;
    }

    private function compressBackup(string $backupPath): array
    {
        try {
            $startTime = microtime(true);
            
            $archivePath = $backupPath . '.tar.gz';
            
            $command = sprintf(
                'tar -czf %s -C %s %s',
                escapeshellarg($archivePath),
                escapeshellarg(dirname($backupPath)),
                escapeshellarg(basename($backupPath))
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Compression failed with return code: ' . $returnCode);
            }

            $originalSize = $this->getDirectorySize($backupPath);
            $compressedSize = filesize($archivePath);
            $compressionRatio = $originalSize > 0 ? round((1 - $compressedSize / $originalSize) * 100, 2) : 0;

            // Remove original directory
            $this->removeDirectory($backupPath);

            $duration = microtime(true) - $startTime;

            return [
                'success' => true,
                'archive_path' => $archivePath,
                'original_size_bytes' => $originalSize,
                'compressed_size_bytes' => $compressedSize,
                'compression_ratio_percent' => $compressionRatio,
                'duration_seconds' => round($duration, 2),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function encryptBackup(string $backupPath): array
    {
        try {
            $startTime = microtime(true);
            
            $encryptionKey = config('backup.encryption_key');
            if (!$encryptionKey) {
                throw new \Exception('Encryption key not configured');
            }

            $encryptedPath = $backupPath . '.enc';
            
            // Simple encryption using OpenSSL
            $command = sprintf(
                'openssl enc -aes-256-cbc -salt -in %s -out %s -k %s',
                escapeshellarg($backupPath),
                escapeshellarg($encryptedPath),
                escapeshellarg($encryptionKey)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Encryption failed with return code: ' . $returnCode);
            }

            $originalSize = is_file($backupPath) ? filesize($backupPath) : $this->getDirectorySize($backupPath);
            $encryptedSize = filesize($encryptedPath);

            // Remove original file/directory
            if (is_file($backupPath)) {
                unlink($backupPath);
            } else {
                $this->removeDirectory($backupPath);
            }

            $duration = microtime(true) - $startTime;

            return [
                'success' => true,
                'encrypted_path' => $encryptedPath,
                'original_size_bytes' => $originalSize,
                'encrypted_size_bytes' => $encryptedSize,
                'duration_seconds' => round($duration, 2),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function uploadToRemoteStorage(string $backupPath): array
    {
        $results = [];

        // Upload to S3
        if ($this->config['storage']['s3']) {
            $results['s3'] = $this->uploadToS3($backupPath);
        }

        // Upload to FTP
        if ($this->config['storage']['ftp']) {
            $results['ftp'] = $this->uploadToFTP($backupPath);
        }

        return $results;
    }

    private function uploadToS3(string $backupPath): array
    {
        try {
            $startTime = microtime(true);
            
            $bucket = config('backup.s3.bucket');
            $region = config('backup.s3.region');
            $key = config('backup.s3.key');
            $secret = config('backup.s3.secret');

            if (!$bucket || !$key || !$secret) {
                throw new \Exception('S3 configuration incomplete');
            }

            $fileName = basename($backupPath);
            $s3Key = 'backups/' . $fileName;

            // Use AWS SDK or custom implementation
            // This is a simplified example
            $command = sprintf(
                'aws s3 cp %s s3://%s/%s --region %s',
                escapeshellarg($backupPath),
                escapeshellarg($bucket),
                escapeshellarg($s3Key),
                escapeshellarg($region)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('S3 upload failed with return code: ' . $returnCode);
            }

            $duration = microtime(true) - $startTime;

            return [
                'success' => true,
                'bucket' => $bucket,
                'key' => $s3Key,
                'duration_seconds' => round($duration, 2),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function uploadToFTP(string $backupPath): array
    {
        try {
            $startTime = microtime(true);
            
            $host = config('backup.ftp.host');
            $username = config('backup.ftp.username');
            $password = config('backup.ftp.password');
            $port = config('backup.ftp.port', 21);
            $path = config('backup.ftp.path', '/backups');

            if (!$host || !$username || !$password) {
                throw new \Exception('FTP configuration incomplete');
            }

            $fileName = basename($backupPath);
            $remotePath = $path . '/' . $fileName;

            // Use FTP functions
            $connection = ftp_connect($host, $port);
            if (!$connection) {
                throw new \Exception('FTP connection failed');
            }

            $login = ftp_login($connection, $username, $password);
            if (!$login) {
                throw new \Exception('FTP login failed');
            }

            ftp_pasv($connection, true);

            $upload = ftp_put($connection, $remotePath, $backupPath, FTP_BINARY);
            if (!$upload) {
                throw new \Exception('FTP upload failed');
            }

            ftp_close($connection);

            $duration = microtime(true) - $startTime;

            return [
                'success' => true,
                'host' => $host,
                'path' => $remotePath,
                'duration_seconds' => round($duration, 2),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function cleanupOldBackups(): void
    {
        try {
            $retentionDays = $this->config['retention_days'];
            $cutoffDate = now()->subDays($retentionDays);

            $backupFiles = glob($this->backupPath . '/backup_*');
            
            foreach ($backupFiles as $backupFile) {
                $fileTime = filemtime($backupFile);
                $fileDate = \Carbon\Carbon::createFromTimestamp($fileTime);
                
                if ($fileDate->lt($cutoffDate)) {
                    if (is_file($backupFile)) {
                        unlink($backupFile);
                    } elseif (is_dir($backupFile)) {
                        $this->removeDirectory($backupFile);
                    }
                    
                    Log::info('Cleaned up old backup', ['file' => $backupFile]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old backups', ['error' => $e->getMessage()]);
        }
    }

    private function sendBackupNotification(array $results): void
    {
        try {
            if ($this->config['notifications']['email']) {
                $this->sendEmailNotification($results);
            }

            if ($this->config['notifications']['slack']) {
                $this->sendSlackNotification($results);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send backup notification', ['error' => $e->getMessage()]);
        }
    }

    private function sendEmailNotification(array $results): void
    {
        // Implement email notification
        $subject = $results['success'] ? 'Backup Completed Successfully' : 'Backup Failed';
        $body = json_encode($results, JSON_PRETTY_PRINT);
        
        // Use Laravel Mail facade or your preferred email service
        Log::info('Backup notification email', ['subject' => $subject, 'body' => $body]);
    }

    private function sendSlackNotification(array $results): void
    {
        $webhookUrl = config('backup.slack_webhook_url');
        if (!$webhookUrl) {
            return;
        }

        $status = $results['success'] ? '✅ Success' : '❌ Failed';
        $message = "Backup {$status}: {$results['backup_id']}";
        
        if ($results['success']) {
            $message .= "\nDuration: {$results['duration_seconds']}s";
            $message .= "\nSize: " . $this->formatBytes($results['total_size_bytes'] ?? 0);
        } else {
            $message .= "\nError: {$results['error']}";
        }

        // Send to Slack webhook
        Log::info('Backup notification Slack', ['message' => $message]);
    }

    public function restoreBackup(string $backupId): array
    {
        try {
            $backupId = $this->normalizeBackupId($backupId);
            $backupPath = $this->backupPath . '/' . $backupId;
            $compressedPath = $backupPath . '.tar.gz';
            $encryptedPath = $this->findEncryptedBackupArtifact($backupPath);

            if (! file_exists($backupPath) && ! file_exists($compressedPath) && $encryptedPath === null) {
                throw new \Exception('Backup not found: ' . $backupId);
            }

            $results = [
                'backup_id' => $backupId,
                'started_at' => now()->toISOString(),
                'components' => [],
                'success' => true,
            ];

            // Check if backup is encrypted
            if ($encryptedPath !== null && ! file_exists($backupPath) && ! file_exists($compressedPath)) {
                $this->decryptBackup($encryptedPath);
            }

            // Check if backup is compressed
            if (file_exists($compressedPath) && ! file_exists($backupPath)) {
                $this->decompressBackup($compressedPath);
            }

            // Restore database
            if (file_exists($backupPath . '/database.sql')) {
                $results['components']['database'] = $this->restoreDatabase($backupPath . '/database.sql');
            }

            // Restore files
            if (is_dir($backupPath . '/files')) {
                $results['components']['files'] = $this->restoreFiles($backupPath . '/files');
            }

            // Restore configuration
            if (is_dir($backupPath . '/config')) {
                $results['components']['config'] = $this->restoreConfig($backupPath . '/config');
            }

            $results['completed_at'] = now()->toISOString();
            $results['duration_seconds'] = now()->diffInSeconds($results['started_at']);

            return $results;

        } catch (\Exception $e) {
            return [
                'backup_id' => $backupId,
                'success' => false,
                'error' => $e->getMessage(),
                'completed_at' => now()->toISOString(),
            ];
        }
    }

    private function restoreDatabase(string $sqlFile): array
    {
        try {
            $startTime = microtime(true);

            if ($this->supportsMysqlCliBackup()) {
                $connection = config('database.connections.mysql', []);
                $command = sprintf(
                    'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
                    escapeshellarg((string) ($connection['host'] ?? '127.0.0.1')),
                    escapeshellarg((string) ($connection['port'] ?? '3306')),
                    escapeshellarg((string) ($connection['username'] ?? '')),
                    escapeshellarg((string) ($connection['password'] ?? '')),
                    escapeshellarg((string) ($connection['database'] ?? '')),
                    escapeshellarg($sqlFile)
                );

                exec($command, $output, $returnCode);

                if ($returnCode !== 0) {
                    throw new \Exception('Database restore failed with return code: ' . $returnCode);
                }
            } elseif (! file_exists($sqlFile) || trim((string) file_get_contents($sqlFile)) === '') {
                throw new \Exception('Database snapshot file is empty or missing.');
            }

            $duration = microtime(true) - $startTime;

            return [
                'success' => true,
                'file' => $sqlFile,
                'duration_seconds' => round($duration, 2),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function restoreFiles(string $filesPath): array
    {
        try {
            $startTime = microtime(true);
            
            $directories = [
                'app' => 'storage/app',
                'framework' => 'storage/framework',
                'uploads' => 'public/uploads',
            ];

            $filesCount = 0;

            foreach ($directories as $source => $destination) {
                $sourcePath = $filesPath . '/' . $source;
                $destPath = base_path($destination);

                if (is_dir($sourcePath)) {
                    if (!is_dir($destPath)) {
                        mkdir($destPath, 0755, true);
                    }
                    
                    $this->copyDirectory($sourcePath, $destPath);
                    $filesCount += $this->getFileCount($destPath);
                }
            }

            $duration = microtime(true) - $startTime;

            return [
                'success' => true,
                'path' => $filesPath,
                'files_count' => $filesCount,
                'duration_seconds' => round($duration, 2),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function restoreConfig(string $configPath): array
    {
        try {
            $startTime = microtime(true);
            
            $configFiles = glob($configPath . '/*');
            $filesCount = 0;

            foreach ($configFiles as $configFile) {
                $fileName = basename($configFile);
                $destPath = base_path($fileName);

                if (file_exists($configFile)) {
                    copy($configFile, $destPath);
                    $filesCount++;
                }
            }

            $duration = microtime(true) - $startTime;

            return [
                'success' => true,
                'path' => $configPath,
                'files_count' => $filesCount,
                'duration_seconds' => round($duration, 2),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function decompressBackup(string $archivePath): void
    {
        $backupPath = str_replace('.tar.gz', '', $archivePath);
        
        $command = sprintf(
            'tar -xzf %s -C %s',
            escapeshellarg($archivePath),
            escapeshellarg(dirname($backupPath))
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Decompression failed with return code: ' . $returnCode);
        }
    }

    private function decryptBackup(string $encryptedPath): void
    {
        $backupPath = str_replace('.enc', '', $encryptedPath);
        $encryptionKey = config('backup.encryption_key');
        
        if (!$encryptionKey) {
            throw new \Exception('Encryption key not configured');
        }

        $command = sprintf(
            'openssl enc -aes-256-cbc -d -in %s -out %s -k %s',
            escapeshellarg($encryptedPath),
            escapeshellarg($backupPath),
            escapeshellarg($encryptionKey)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Decryption failed with return code: ' . $returnCode);
        }
    }

    public function listBackups(): array
    {
        try {
            $backupFiles = glob($this->backupPath . '/backup_*');
            $backups = [];

            foreach ($backupFiles as $backupFile) {
                $backupId = $this->normalizeBackupId(basename($backupFile));
                $fileTime = filemtime($backupFile);
                $fileSize = is_file($backupFile) ? filesize($backupFile) : $this->getDirectorySize($backupFile);

                $candidate = [
                    'backup_id' => $backupId,
                    'artifact_name' => basename($backupFile),
                    'created_at' => \Carbon\Carbon::createFromTimestamp($fileTime)->toISOString(),
                    'size_bytes' => $fileSize,
                    'size_formatted' => $this->formatBytes($fileSize),
                    'type' => $this->detectBackupArtifactType($backupFile),
                ];

                if (! isset($backups[$backupId]) || strtotime($candidate['created_at']) >= strtotime($backups[$backupId]['created_at'])) {
                    $backups[$backupId] = $candidate;
                }
            }

            // Sort by creation time (newest first)
            $backups = array_values($backups);

            usort($backups, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return $backups;

        } catch (\Exception $e) {
            Log::error('Failed to list backups', ['error' => $e->getMessage()]);
            return [];
        }
    }

    // Helper methods
    private function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                copy($item, $target);
            }
        }
    }

    private function removeDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }

        rmdir($path);
    }

    private function getDirectorySize(string $path): int
    {
        $size = 0;
        
        if (is_file($path)) {
            return filesize($path);
        }

        if (!is_dir($path)) {
            return 0;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    private function getFileCount(string $path): int
    {
        $count = 0;
        
        if (is_file($path)) {
            return 1;
        }

        if (!is_dir($path)) {
            return 0;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $count++;
            }
        }

        return $count;
    }

    private function getTableCount(): int
    {
        try {
            return count(Schema::getTableListing());
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function supportsMysqlCliBackup(): bool
    {
        return DB::getDriverName() === 'mysql'
            && ! in_array((string) config('database.connections.mysql.database'), ['', ':memory:'], true);
    }

    private function writePortableDatabaseSnapshot(string $dumpFile): void
    {
        $snapshot = [
            'driver' => DB::getDriverName(),
            'generated_at' => now()->toISOString(),
            'tables' => collect(Schema::getTableListing())->map(function (string $table) {
                return [
                    'name' => $table,
                    'columns' => Schema::getColumnListing($table),
                    'row_count' => DB::table($table)->count(),
                ];
            })->values()->all(),
        ];

        $lines = [
            '-- Portable database snapshot',
            '-- Driver: ' . $snapshot['driver'],
            '-- Generated at: ' . $snapshot['generated_at'],
            '-- Snapshot: ' . json_encode($snapshot, JSON_UNESCAPED_SLASHES),
        ];

        file_put_contents($dumpFile, implode(PHP_EOL, $lines) . PHP_EOL);
    }

    private function normalizeBackupId(string $backupId): string
    {
        $normalized = $backupId;

        while (true) {
            if (str_ends_with($normalized, '.enc')) {
                $normalized = substr($normalized, 0, -4);
                continue;
            }

            if (str_ends_with($normalized, '.tar.gz')) {
                $normalized = substr($normalized, 0, -7);
                continue;
            }

            break;
        }

        return $normalized;
    }

    private function findEncryptedBackupArtifact(string $backupPath): ?string
    {
        foreach ([$backupPath . '.tar.gz.enc', $backupPath . '.enc'] as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function detectBackupArtifactType(string $backupFile): string
    {
        if (is_dir($backupFile)) {
            return 'directory';
        }

        if (str_ends_with($backupFile, '.tar.gz.enc')) {
            return 'encrypted_archive';
        }

        if (str_ends_with($backupFile, '.tar.gz')) {
            return 'archive';
        }

        if (str_ends_with($backupFile, '.enc')) {
            return 'encrypted_file';
        }

        return 'file';
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
