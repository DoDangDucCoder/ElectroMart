<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\Shared\Services\MonitoringLoggingService;
use App\Application\Shared\Services\BackupRecoveryService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ProductionDeploymentCommand extends Command
{
    protected $signature = 'deploy:production 
                            {--backup : Create backup before deployment}
                            {--migrate : Run database migrations}
                            {--optimize : Optimize application for production}
                            {--monitor : Enable monitoring and logging}
                            {--health-check : Run health checks after deployment}
                            {--rollback : Rollback to previous version}
                            {--status : Check deployment status}';

    protected $description = 'Deploy Laravel Shop Website to production environment';

    private MonitoringLoggingService $monitoringService;
    private BackupRecoveryService $backupService;

    public function __construct(
        MonitoringLoggingService $monitoringService,
        BackupRecoveryService $backupService
    ) {
        parent::__construct();
        $this->monitoringService = $monitoringService;
        $this->backupService = $backupService;
    }

    public function handle(): int
    {
        $this->info('🚀 Starting Production Deployment...');
        $this->newLine();

        try {
            if ($this->option('status')) {
                return $this->displayDeploymentStatus();
            }

            // Check if we're in production environment
            if (app()->environment() !== 'production') {
                $this->warn('⚠️ Warning: Not in production environment');
                if (!$this->confirm('Continue with deployment?')) {
                    return Command::FAILURE;
                }
            }

            $startTime = microtime(true);

            // Pre-deployment checks
            $this->runPreDeploymentChecks();

            // Create backup if requested
            if ($this->option('backup')) {
                $this->createBackup();
            }

            // Run database migrations
            if ($this->option('migrate')) {
                $this->runMigrations();
            }

            // Optimize application
            if ($this->option('optimize')) {
                $this->optimizeApplication();
            }

            // Enable monitoring
            if ($this->option('monitor')) {
                $this->enableMonitoring();
            }

            // Run health checks
            if ($this->option('health-check')) {
                $this->runHealthChecks();
            }

            $endTime = microtime(true);
            $duration = ($endTime - $startTime) * 1000;

            $this->newLine();
            $this->info("✅ Production deployment completed successfully in " . round($duration, 2) . "ms");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Production deployment failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            
            // Attempt rollback if requested
            if ($this->option('rollback')) {
                $this->rollbackDeployment();
            }
            
            return Command::FAILURE;
        }
    }

    private function displayDeploymentStatus(): int
    {
        $healthStatus = $this->monitoringService->getHealthStatus();
        $latestBackup = $this->backupService->listBackups()[0]['backup_id'] ?? 'No backups found';

        $this->info('📋 Deployment Status');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Environment', app()->environment()],
                ['Application URL', config('app.url') ?: 'Not configured'],
                ['Database Driver', (string) config('database.default')],
                ['Cache Driver', (string) config('cache.default')],
                ['Queue Driver', (string) config('queue.default')],
                ['Overall Health', $healthStatus['overall_status'] ?? 'unknown'],
                ['Latest Backup', $latestBackup],
            ]
        );

        return Command::SUCCESS;
    }

    private function runPreDeploymentChecks(): void
    {
        $this->info('🔍 Running pre-deployment checks...');

        $checks = [
            'Environment Configuration' => $this->checkEnvironmentConfig(),
            'Database Connection' => $this->checkDatabaseConnection(),
            'Cache Connection' => $this->checkCacheConnection(),
            'Queue Connection' => $this->checkQueueConnection(),
            'Storage Permissions' => $this->checkStoragePermissions(),
            'Required Services' => $this->checkRequiredServices(),
        ];

        $this->displayCheckResults($checks);

        $failedChecks = array_filter($checks, fn($result) => !$result['success']);
        
        if (!empty($failedChecks)) {
            $this->error('❌ Pre-deployment checks failed!');
            foreach ($failedChecks as $check => $result) {
                $this->line("  • {$check}: {$result['message']}");
            }
            throw new \Exception('Pre-deployment checks failed');
        }

        $this->info('✅ All pre-deployment checks passed');
    }

    private function checkEnvironmentConfig(): array
    {
        try {
            $requiredVars = [
                'APP_NAME', 'APP_ENV', 'APP_KEY', 'APP_URL',
                'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE',
                'CACHE_DRIVER', 'QUEUE_CONNECTION',
            ];

            $missingVars = [];
            foreach ($requiredVars as $var) {
                if (empty(env($var))) {
                    $missingVars[] = $var;
                }
            }

            if (!empty($missingVars)) {
                return [
                    'success' => false,
                    'message' => 'Missing environment variables: ' . implode(', ', $missingVars),
                ];
            }

            return [
                'success' => true,
                'message' => 'All required environment variables are set',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error checking environment: ' . $e->getMessage(),
            ];
        }
    }

    private function checkDatabaseConnection(): array
    {
        try {
            DB::connection()->getPdo();
            return [
                'success' => true,
                'message' => 'Database connection successful',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    private function checkCacheConnection(): array
    {
        try {
            Cache::put('deployment_check', 'test', 60);
            $value = Cache::get('deployment_check');
            Cache::forget('deployment_check');
            
            if ($value === 'test') {
                return [
                    'success' => true,
                    'message' => 'Cache connection successful',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Cache read/write test failed',
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Cache connection failed: ' . $e->getMessage(),
            ];
        }
    }

    private function checkQueueConnection(): array
    {
        try {
            $queueDriver = config('queue.default');
            
            if ($queueDriver === 'redis') {
                Redis::ping();
                return [
                    'success' => true,
                    'message' => 'Queue connection (Redis) successful',
                ];
            }

            return [
                'success' => true,
                'message' => 'Queue driver: ' . $queueDriver,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Queue connection failed: ' . $e->getMessage(),
            ];
        }
    }

    private function checkStoragePermissions(): array
    {
        try {
            $directories = [
                storage_path('app'),
                storage_path('framework/cache'),
                storage_path('framework/sessions'),
                storage_path('framework/views'),
                storage_path('logs'),
                bootstrap_path('cache'),
            ];

            $permissionIssues = [];
            foreach ($directories as $directory) {
                if (!is_writable($directory)) {
                    $permissionIssues[] = $directory;
                }
            }

            if (!empty($permissionIssues)) {
                return [
                    'success' => false,
                    'message' => 'Permission issues: ' . implode(', ', $permissionIssues),
                ];
            }

            return [
                'success' => true,
                'message' => 'All storage directories are writable',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error checking permissions: ' . $e->getMessage(),
            ];
        }
    }

    private function checkRequiredServices(): array
    {
        try {
            $services = [
                'PHP' => PHP_VERSION,
                'Laravel' => app()->version(),
                'MySQL' => $this->getMySQLVersion(),
                'Redis' => $this->getRedisVersion(),
            ];

            return [
                'success' => true,
                'message' => 'Required services available: ' . implode(', ', array_keys($services)),
                'services' => $services,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error checking services: ' . $e->getMessage(),
            ];
        }
    }

    private function displayCheckResults(array $checks): void
    {
        foreach ($checks as $check => $result) {
            $status = $result['success'] ? '✅' : '❌';
            $this->line("  {$status} {$check}: {$result['message']}");
        }
    }

    private function createBackup(): void
    {
        $this->info('💾 Creating backup before deployment...');
        
        try {
            $backupResult = $this->backupService->createBackup();
            
            if ($backupResult['success']) {
                $this->info("✅ Backup created successfully: {$backupResult['backup_id']}");
                $this->line("  Duration: {$backupResult['duration_seconds']}s");
                $this->line("  Size: " . $this->formatBytes($backupResult['total_size_bytes'] ?? 0));
            } else {
                throw new \Exception('Backup creation failed: ' . ($backupResult['error'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->error('❌ Backup creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function runMigrations(): void
    {
        $this->info('🗄️ Running database migrations...');
        
        try {
            $this->call('migrate', ['--force' => true]);
            $this->info('✅ Database migrations completed');
        } catch (\Exception $e) {
            $this->error('❌ Database migrations failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function optimizeApplication(): void
    {
        $this->info('⚡ Optimizing application for production...');
        
        try {
            // Clear and cache configuration
            $this->call('config:clear');
            $this->call('config:cache');
            
            // Clear and cache routes
            $this->call('route:clear');
            $this->call('route:cache');
            
            // Clear and cache views
            $this->call('view:clear');
            $this->call('view:cache');
            
            // Optimize autoloader
            $this->call('optimize');
            
            // Clear application cache
            $this->call('cache:clear');
            
            $this->info('✅ Application optimization completed');
        } catch (\Exception $e) {
            $this->error('❌ Application optimization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function enableMonitoring(): void
    {
        $this->info('📊 Enabling monitoring and logging...');
        
        try {
            // Collect initial metrics
            $metrics = $this->monitoringService->collectMetrics();
            
            // Run health checks
            $healthStatus = $this->monitoringService->getHealthStatus();
            
            $this->info('✅ Monitoring enabled');
            $this->line("  Overall health: {$healthStatus['overall_status']}");
            
            // Display health check results
            foreach ($healthStatus['checks'] as $check => $result) {
                $status = $result['status'] === 'healthy' ? '✅' : '❌';
                $this->line("    {$status} {$check}: {$result['status']}");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Monitoring setup failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function runHealthChecks(): void
    {
        $this->info('🏥 Running post-deployment health checks...');
        
        try {
            $healthStatus = $this->monitoringService->getHealthStatus();
            
            $this->displayHealthCheckResults($healthStatus);
            
            if ($healthStatus['overall_status'] !== 'healthy') {
                $this->warn('⚠️ Health checks indicate issues');
                $this->line('Please review the health check results above');
            } else {
                $this->info('✅ All health checks passed');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Health checks failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function displayHealthCheckResults(array $healthStatus): void
    {
        $this->line("Overall Status: {$healthStatus['overall_status']}");
        $this->newLine();
        
        foreach ($healthStatus['checks'] as $check => $result) {
            $status = $result['status'] === 'healthy' ? '✅' : '❌';
            $this->line("  {$status} {$check}: {$result['status']}");
            
            if (isset($result['response_time_ms'])) {
                $this->line("    Response time: {$result['response_time_ms']}ms");
            }
            
            if (isset($result['error'])) {
                $this->line("    Error: {$result['error']}");
            }
        }
    }

    private function rollbackDeployment(): void
    {
        $this->info('🔄 Rolling back deployment...');
        
        try {
            // List available backups
            $backups = $this->backupService->listBackups();
            
            if (empty($backups)) {
                throw new \Exception('No backups available for rollback');
            }
            
            // Use the most recent backup
            $latestBackup = $backups[0];
            $this->line("Rolling back to: {$latestBackup['backup_id']}");
            
            // Restore backup
            $restoreResult = $this->backupService->restoreBackup($latestBackup['backup_id']);
            
            if ($restoreResult['success']) {
                $this->info('✅ Rollback completed successfully');
            } else {
                throw new \Exception('Rollback failed: ' . ($restoreResult['error'] ?? 'Unknown error'));
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Rollback failed: ' . $e->getMessage());
        }
    }

    private function getMySQLVersion(): string
    {
        try {
            $result = DB::select('SELECT VERSION() as version');
            return $result[0]->version ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function getRedisVersion(): string
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info();
            return $info['redis_version'] ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
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
