<?php

namespace Tests\Feature;

use App\Application\Shared\Services\APITestingService;
use App\Application\Shared\Services\BackupRecoveryService;
use App\Application\Shared\Services\DatabaseTestingService;
use App\Application\Shared\Services\PerformanceTestingService;
use App\Application\Shared\Services\SecurityTestingService;
use App\Console\Commands\IntegrationTestingCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class IntegrationTestingCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_only_integration_command_completes_without_runtime_errors(): void
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
        $this->app->forgetInstance(DatabaseTestingService::class);

        $this->artisan('integration:test --database')->assertExitCode(0);
    }

    public function test_integration_report_summary_reads_security_vulnerabilities_from_nested_summary(): void
    {
        $before = glob(storage_path('app/reports/integration_test_report_*.json')) ?: [];

        $api = Mockery::mock(APITestingService::class);
        $api->shouldReceive('runAPITests')->once()->andReturn([
            'public_endpoints' => [
                'GET /api/v1/products' => ['success' => true, 'message' => 'ok'],
            ],
        ]);
        $api->shouldReceive('generateTestReport')->once()->andReturn([
            'summary' => [
                'total_tests' => 1,
                'passed_tests' => 1,
                'failed_tests' => 0,
                'success_rate' => 100,
            ],
            'recommendations' => [],
        ]);

        $security = Mockery::mock(SecurityTestingService::class);
        $security->shouldReceive('runSecurityTests')->once()->andReturn([
            'input_validation_tests' => [
                'sql_injection' => [
                    ['success' => false, 'vulnerable' => true, 'message' => 'Potential SQL injection vulnerability'],
                ],
            ],
        ]);
        $security->shouldReceive('generateSecurityReport')->once()->andReturn([
            'summary' => [
                'total_tests' => 2,
                'passed_tests' => 1,
                'failed_tests' => 1,
                'success_rate' => 50,
                'vulnerabilities_found' => 3,
            ],
            'vulnerabilities' => ['Potential SQL injection vulnerability'],
            'recommendations' => [],
            'security_score' => 40,
        ]);

        $this->app->instance(APITestingService::class, $api);
        $this->app->instance(SecurityTestingService::class, $security);
        $this->app->forgetInstance(IntegrationTestingCommand::class);

        $this->artisan('integration:test --api --security --report')->assertExitCode(0);

        $after = glob(storage_path('app/reports/integration_test_report_*.json')) ?: [];
        $newFiles = array_values(array_diff($after, $before));

        $this->assertNotEmpty($newFiles);

        $report = json_decode((string) file_get_contents(end($newFiles)), true);

        $this->assertSame(3, $report['summary']['vulnerabilities_found']);
        $this->assertSame(40, $report['summary']['security_score']);
        $this->assertSame(2, $report['summary']['successful_categories']);
    }

    public function test_integration_csv_export_flattens_nested_results(): void
    {
        $before = glob(storage_path('app/exports/integration_test_results_*.csv')) ?: [];

        $api = Mockery::mock(APITestingService::class);
        $api->shouldReceive('runAPITests')->once()->andReturn([
            'security_tests' => [
                'sql_injection' => [
                    ['success' => false, 'message' => 'Potential SQL injection vulnerability'],
                ],
            ],
            'validation_tests' => [
                'formats' => [
                    ['success' => true, 'message' => 'Properly validated'],
                ],
            ],
        ]);
        $api->shouldReceive('generateTestReport')->once()->andReturn([
            'summary' => [
                'total_tests' => 2,
                'passed_tests' => 1,
                'failed_tests' => 1,
                'success_rate' => 50,
            ],
            'recommendations' => [],
        ]);

        $this->app->instance(APITestingService::class, $api);
        $this->app->forgetInstance(IntegrationTestingCommand::class);

        $this->artisan('integration:test --api --export=csv')->assertExitCode(0);

        $after = glob(storage_path('app/exports/integration_test_results_*.csv')) ?: [];
        $newFiles = array_values(array_diff($after, $before));

        $this->assertNotEmpty($newFiles);

        $csv = (string) file_get_contents(end($newFiles));

        $this->assertStringContainsString('api,"security_tests > sql_injection > 0",FAIL,"Potential SQL injection vulnerability"', $csv);
        $this->assertStringContainsString('api,"validation_tests > formats > 0",PASS,"Properly validated"', $csv);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
