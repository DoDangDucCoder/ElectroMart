<?php

namespace Tests\Feature;

use App\Console\Commands\ProductionDeploymentCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class ProductionDeploymentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_option_returns_success_without_interactive_confirmation(): void
    {
        $this->artisan('deploy:production --status')->assertExitCode(0);
    }

    public function test_environment_config_check_uses_cached_laravel_config_values(): void
    {
        config([
            'app.name' => 'Laravel Shop',
            'app.env' => 'production',
            'app.key' => 'base64:testing-key',
            'app.url' => 'https://shop.test',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'cache.default' => 'array',
            'queue.default' => 'sync',
        ]);

        $command = app(ProductionDeploymentCommand::class);
        $method = new ReflectionMethod($command, 'checkEnvironmentConfig');
        $method->setAccessible(true);

        $result = $method->invoke($command);

        $this->assertTrue($result['success']);
        $this->assertSame('All required configuration values are set', $result['message']);
    }
}
