<?php

namespace Tests\Feature;

use App\Application\Shared\Services\DatabaseOptimizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OptimizeDatabaseCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_optimize_database_command_options_complete_without_runtime_errors(): void
    {
        $this->artisan('db:optimize --indexes')->assertExitCode(0);
        $this->artisan('db:optimize --stats')->assertExitCode(0);
        $this->artisan('db:optimize --slow-queries')->assertExitCode(0);
        $this->artisan('db:optimize --optimize')->assertExitCode(0);
    }

    public function test_optimize_database_command_renders_driver_summary_without_table_rows(): void
    {
        $service = Mockery::mock(DatabaseOptimizationService::class);
        $service->shouldReceive('optimizeTables')
            ->once()
            ->andReturn([
                '__summary' => [
                    'operation' => 'optimize',
                    'status' => 'skipped',
                    'driver' => 'sqlite',
                    'table_count' => 3,
                    'message' => 'Optimize table is not supported on this database driver.',
                ],
            ]);

        $this->app->instance(DatabaseOptimizationService::class, $service);

        $this->artisan('db:optimize --optimize')
            ->expectsOutputToContain('Database optimize skipped for sqlite driver across 3 tables')
            ->doesntExpectOutputToContain('__summary')
            ->assertExitCode(0);
    }

    public function test_analyze_database_command_renders_generic_driver_summary(): void
    {
        $service = Mockery::mock(DatabaseOptimizationService::class);
        $service->shouldReceive('analyzeTables')
            ->once()
            ->andReturn([
                '__summary' => [
                    'operation' => 'analyze',
                    'status' => 'ok',
                    'driver' => 'sqlite',
                    'table_count' => 3,
                    'message' => 'Analyze completed successfully.',
                ],
            ]);

        $this->app->instance(DatabaseOptimizationService::class, $service);

        $this->artisan('db:optimize --analyze')
            ->expectsOutputToContain('Database analyze ok for sqlite driver across 3 tables')
            ->doesntExpectOutputToContain('__summary')
            ->assertExitCode(0);
    }
}
