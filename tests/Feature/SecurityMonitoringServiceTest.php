<?php

namespace Tests\Feature;

use App\Application\Shared\Services\SecurityMonitoringService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SecurityMonitoringServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_monitoring_service_falls_back_cleanly_when_security_tables_are_missing(): void
    {
        Schema::dropIfExists('security_events');
        Schema::dropIfExists('security_alerts');
        Schema::dropIfExists('blocked_ips');

        $service = $this->app->make(SecurityMonitoringService::class);
        $service->logSecurityEvent('failed_login', ['email' => 'test@example.com'], 'high');

        $dashboard = $service->getSecurityDashboard();
        $report = $service->generateSecurityReport(now()->subDay(), now());

        $this->assertSame([], $dashboard['recent_events']);
        $this->assertSame([], $dashboard['alerts']);
        $this->assertSame(0, $dashboard['threat_levels']['critical']);
        $this->assertSame(0, $report['summary']['total_events']);
        $this->assertSame(0, $report['summary']['total_alerts']);
        $this->assertFalse($service->isIPBlocked('203.0.113.10'));
    }

    public function test_security_monitoring_service_can_block_and_unblock_ip_when_table_exists(): void
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

        $service = $this->app->make(SecurityMonitoringService::class);

        $this->assertTrue($service->blockIP('203.0.113.10', 'Test block'));
        $this->assertTrue($service->isIPBlocked('203.0.113.10'));
        $this->assertTrue($service->unblockIP('203.0.113.10'));
        $this->assertFalse($service->isIPBlocked('203.0.113.10'));
    }

    public function test_security_report_summary_respects_end_date_window(): void
    {
        if (! Schema::hasTable('security_events')) {
            Schema::create('security_events', function (Blueprint $table) {
                $table->id();
                $table->string('event_type');
                $table->string('threat_level')->default('medium');
                $table->json('data')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('security_alerts')) {
            Schema::create('security_alerts', function (Blueprint $table) {
                $table->id();
                $table->string('alert_type');
                $table->string('severity');
                $table->json('event_data')->nullable();
                $table->timestamps();
            });
        }

        DB::table('security_events')->insert([
            [
                'event_type' => 'failed_login',
                'threat_level' => 'high',
                'data' => json_encode(['email' => 'inside@example.com']),
                'ip_address' => '203.0.113.10',
                'user_agent' => 'UnitTest',
                'user_id' => null,
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
            [
                'event_type' => 'failed_login',
                'threat_level' => 'critical',
                'data' => json_encode(['email' => 'outside@example.com']),
                'ip_address' => '203.0.113.11',
                'user_agent' => 'UnitTest',
                'user_id' => null,
                'created_at' => now()->addHours(2),
                'updated_at' => now()->addHours(2),
            ],
        ]);

        DB::table('security_alerts')->insert([
            [
                'alert_type' => 'threshold_exceeded',
                'severity' => 'high',
                'event_data' => json_encode(['event' => 'failed_login']),
                'created_at' => now()->subHours(11),
                'updated_at' => now()->subHours(11),
            ],
            [
                'alert_type' => 'threshold_exceeded',
                'severity' => 'critical',
                'event_data' => json_encode(['event' => 'failed_login']),
                'created_at' => now()->addHours(3),
                'updated_at' => now()->addHours(3),
            ],
        ]);

        $service = $this->app->make(SecurityMonitoringService::class);
        $report = $service->generateSecurityReport(now()->subDay(), now());

        $this->assertSame(1, $report['summary']['total_events']);
        $this->assertSame(1, $report['summary']['total_alerts']);
        $this->assertSame(1, $report['summary']['threat_levels']['high']);
        $this->assertSame(0, $report['summary']['threat_levels']['critical']);
        $this->assertCount(1, $report['summary']['top_events']);
        $this->assertSame('failed_login', $report['summary']['top_events'][0]->event_type);
        $this->assertCount(1, $report['events']);
        $this->assertCount(1, $report['alerts']);
    }
}
