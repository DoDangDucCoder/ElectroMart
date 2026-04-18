<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionDeploymentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_option_returns_success_without_interactive_confirmation(): void
    {
        $this->artisan('deploy:production --status')->assertExitCode(0);
    }
}
