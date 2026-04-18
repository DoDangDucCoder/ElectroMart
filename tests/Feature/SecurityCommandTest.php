<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SecurityCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_report_command_completes_without_security_tables(): void
    {
        Schema::dropIfExists('security_events');
        Schema::dropIfExists('security_alerts');
        Schema::dropIfExists('blocked_ips');

        $this->artisan('security:manage --security-report')->assertExitCode(0);
    }

    public function test_unblock_ip_command_removes_existing_blocked_ip(): void
    {
        if (! Schema::hasTable('blocked_ips')) {
            Schema::create('blocked_ips', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address')->unique();
                $table->string('reason')->nullable();
                $table->timestamp('blocked_at')->nullable();
                $table->timestamps();
            });
        }

        \DB::table('blocked_ips')->insert([
            'ip_address' => '203.0.113.10',
            'reason' => 'Fixture',
            'blocked_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('security:manage --unblock-ip')
            ->expectsQuestion('Enter IP address to unblock', '203.0.113.10')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('blocked_ips', [
            'ip_address' => '203.0.113.10',
        ]);
    }
}
