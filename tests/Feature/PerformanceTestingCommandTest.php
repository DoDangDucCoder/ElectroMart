<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceTestingCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_performance_optimize_performance_test_generates_a_real_report(): void
    {
        $this->artisan('performance:optimize --performance-test')
            ->expectsOutputToContain('Overall Score:')
            ->doesntExpectOutputToContain('Failed to generate report')
            ->doesntExpectOutputToContain('Report generation failed')
            ->assertExitCode(0);
    }
}
