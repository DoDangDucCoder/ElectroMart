<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptimizeApplicationCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_optimize_all_option_completes_without_table_render_errors(): void
    {
        $this->artisan('app:optimize --all')
            ->expectsOutputToContain('Database optimize skipped for sqlite driver across')
            ->doesntExpectOutputToContain('__summary')
            ->doesntExpectOutputToContain('A cell must be a TableCell')
            ->assertExitCode(0);
    }
}
