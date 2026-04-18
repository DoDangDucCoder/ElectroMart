<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class SecurityMonitoringService
{
    private array $securityEvents = [];
    private array $threatLevels = [
        'low' => 1,
        'medium' => 2,
        'high' => 3,
        'critical' => 4,
    ];
    private array $alertThresholds = [
        'failed_logins' => 5,
        'suspicious_ips' => 3,
        'unusual_activity' => 10,
        'data_access' => 100,
    ];

    public function logSecurityEvent(string $event, array $data, string $level = 'medium'): void
    {
        $eventData = [
            'event' => $event,
            'level' => $level,
            'data' => $data,
            'timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
        ];

        $this->securityEvents[] = $eventData;

        // Log to Laravel log
        $this->logToFile($eventData);

        // Store in cache for real-time monitoring
        $this->storeInCache($eventData);

        // Check for alerts
        $this->checkForAlerts($eventData);

        // Store in database for long-term analysis
        $this->storeInDatabase($eventData);
    }

    private function logToFile(array $eventData): void
    {
        $logLevel = $this->getLogLevel($eventData['level']);

        try {
            if (is_array(config('logging.channels.security'))) {
                Log::channel('security')->log($logLevel, 'Security Event', $eventData);
                return;
            }
        } catch (\Throwable $e) {
            Log::warning('Security log channel unavailable', ['error' => $e->getMessage()]);
        }

        Log::log($logLevel, 'Security Event', $eventData);
    }

    private function getLogLevel(string $threatLevel): string
    {
        return match ($threatLevel) {
            'critical' => 'critical',
            'high' => 'error',
            'medium' => 'warning',
            'low' => 'info',
            default => 'info',
        };
    }

    private function storeInCache(array $eventData): void
    {
        $cacheKey = 'security_events_' . now()->format('Y-m-d-H');
        $events = Cache::get($cacheKey, []);
        $events[] = $eventData;
        
        // Keep only last 100 events per hour
        if (count($events) > 100) {
            $events = array_slice($events, -100);
        }
        
        Cache::put($cacheKey, $events, 3600); // 1 hour
    }

    private function storeInDatabase(array $eventData): void
    {
        try {
            if (! $this->schemaHasTable('security_events')) {
                return;
            }

            DB::table('security_events')->insert([
                'event_type' => $eventData['event'],
                'threat_level' => $eventData['level'],
                'data' => json_encode($eventData['data']),
                'ip_address' => $eventData['ip_address'],
                'user_agent' => $eventData['user_agent'],
                'user_id' => $eventData['user_id'],
                'created_at' => $eventData['timestamp'],
                'updated_at' => $eventData['timestamp'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to store security event in database', [
                'error' => $e->getMessage(),
                'event' => $eventData,
            ]);
        }
    }

    private function checkForAlerts(array $eventData): void
    {
        $eventType = $eventData['event'];
        $level = $eventData['level'];

        // Check threshold-based alerts
        if ($this->shouldTriggerAlert($eventType, $level)) {
            $this->triggerAlert($eventData);
        }

        // Check pattern-based alerts
        if ($this->detectSuspiciousPattern($eventData)) {
            $this->triggerAlert($eventData, 'pattern_detected');
        }
    }

    private function shouldTriggerAlert(string $eventType, string $level): bool
    {
        $threshold = $this->alertThresholds[$eventType] ?? null;
        if (!$threshold) {
            return false;
        }

        $count = $this->getEventCount($eventType, now()->subHour());
        return $count >= $threshold;
    }

    private function getEventCount(string $eventType, $since): int
    {
        if (! $this->schemaHasTable('security_events')) {
            return 0;
        }

        $cacheKey = "event_count_{$eventType}_" . $since->format('Y-m-d-H');

        try {
            return Cache::remember($cacheKey, 3600, function () use ($eventType, $since) {
                return DB::table('security_events')
                    ->where('event_type', $eventType)
                    ->where('created_at', '>=', $since)
                    ->count();
            });
        } catch (\Throwable $e) {
            Log::warning('Failed to count security events', [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    private function detectSuspiciousPattern(array $eventData): bool
    {
        $patterns = [
            'rapid_failed_logins' => $this->detectRapidFailedLogins($eventData),
            'unusual_ip_pattern' => $this->detectUnusualIPPattern($eventData),
            'data_exfiltration' => $this->detectDataExfiltration($eventData),
            'privilege_escalation' => $this->detectPrivilegeEscalation($eventData),
        ];

        return in_array(true, $patterns);
    }

    private function detectRapidFailedLogins(array $eventData): bool
    {
        if ($eventData['event'] !== 'failed_login') {
            return false;
        }

        if (! $this->schemaHasTable('security_events')) {
            return false;
        }

        try {
            $recentFailures = DB::table('security_events')
                ->where('event_type', 'failed_login')
                ->where('ip_address', $eventData['ip_address'])
                ->where('created_at', '>=', now()->subMinutes(5))
                ->count();
        } catch (\Throwable $e) {
            return false;
        }

        return $recentFailures >= 10;
    }

    private function detectUnusualIPPattern(array $eventData): bool
    {
        $ip = $eventData['ip_address'];
        
        // Check if IP is from unusual location
        $location = $this->getIPLocation($ip);
        if ($location && $this->isUnusualLocation($location)) {
            return true;
        }

        if (! $this->schemaHasTable('security_events')) {
            return false;
        }

        // Check if IP has been flagged before
        try {
            $previousFlags = DB::table('security_events')
                ->where('ip_address', $ip)
                ->where('threat_level', 'high')
                ->count();
        } catch (\Throwable $e) {
            return false;
        }

        return $previousFlags > 0;
    }

    private function detectDataExfiltration(array $eventData): bool
    {
        if ($eventData['event'] !== 'data_access') {
            return false;
        }

        $data = $eventData['data'];
        $accessCount = $data['access_count'] ?? 0;
        $dataSize = $data['data_size'] ?? 0;

        // Alert if accessing large amounts of data
        return $accessCount > 1000 || $dataSize > 1000000; // 1MB
    }

    private function detectPrivilegeEscalation(array $eventData): bool
    {
        if ($eventData['event'] !== 'permission_granted') {
            return false;
        }

        $data = $eventData['data'];
        $permission = $data['permission'] ?? '';
        
        $sensitivePermissions = [
            'admin_access',
            'user_management',
            'system_settings',
            'data_export',
        ];

        return in_array($permission, $sensitivePermissions);
    }

    private function triggerAlert(array $eventData, string $alertType = 'threshold_exceeded'): void
    {
        $alert = [
            'type' => $alertType,
            'event' => $eventData,
            'timestamp' => now(),
            'severity' => $this->calculateAlertSeverity($eventData),
        ];

        // Store alert
        $this->storeAlert($alert);

        // Send notifications
        $this->sendAlertNotifications($alert);

        Log::critical('Security Alert Triggered', $alert);
    }

    private function calculateAlertSeverity(array $eventData): string
    {
        $level = $eventData['level'];
        $event = $eventData['event'];

        if ($level === 'critical' || $event === 'data_breach') {
            return 'critical';
        }

        if ($level === 'high' || in_array($event, ['failed_login', 'suspicious_activity'])) {
            return 'high';
        }

        return 'medium';
    }

    private function storeAlert(array $alert): void
    {
        try {
            if (! $this->schemaHasTable('security_alerts')) {
                return;
            }

            DB::table('security_alerts')->insert([
                'alert_type' => $alert['type'],
                'severity' => $alert['severity'],
                'event_data' => json_encode($alert['event']),
                'created_at' => $alert['timestamp'],
                'updated_at' => $alert['timestamp'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to store security alert', [
                'error' => $e->getMessage(),
                'alert' => $alert,
            ]);
        }
    }

    private function sendAlertNotifications(array $alert): void
    {
        $severity = $alert['severity'];
        
        // Only send notifications for high and critical alerts
        if (!in_array($severity, ['high', 'critical'])) {
            return;
        }

        $recipients = $this->getAlertRecipients($severity);
        
        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->send(new \App\Mail\SecurityAlertMail($alert));
            } catch (\Exception $e) {
                Log::error('Failed to send security alert email', [
                    'recipient' => $recipient,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function getAlertRecipients(string $severity): array
    {
        $recipients = [];
        $primaryAdminEmail = (string) config('admin-auth.seed_defaults.primary_email', 'admin@shop.com');
        
        if ($severity === 'critical') {
            $recipients = [
                $primaryAdminEmail,
                'security@example.com',
                'cto@example.com',
            ];
        } elseif ($severity === 'high') {
            $recipients = [
                $primaryAdminEmail,
                'security@example.com',
            ];
        }

        return $recipients;
    }

    public function getSecurityDashboard(): array
    {
        $now = now();
        $last24Hours = $now->copy()->subDay();
        $last7Days = $now->copy()->subWeek();

        return [
            'recent_events' => $this->getRecentEvents(24),
            'threat_levels' => $this->getThreatLevelStats($last24Hours),
            'top_events' => $this->getTopEvents($last7Days),
            'suspicious_ips' => $this->getSuspiciousIPs($last24Hours),
            'failed_logins' => $this->getFailedLoginStats($last24Hours),
            'alerts' => $this->getRecentAlerts(24),
        ];
    }

    private function getRecentEvents(int $hours): array
    {
        if (! $this->schemaHasTable('security_events')) {
            return [];
        }

        return DB::table('security_events')
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_type' => $event->event_type,
                    'threat_level' => $event->threat_level,
                    'ip_address' => $event->ip_address,
                    'created_at' => $event->created_at,
                ];
            })
            ->toArray();
    }

    private function getThreatLevelStats($since, $until = null): array
    {
        if (! $this->schemaHasTable('security_events')) {
            return [
                'low' => 0,
                'medium' => 0,
                'high' => 0,
                'critical' => 0,
            ];
        }

        $stats = DB::table('security_events')
            ->where('created_at', '>=', $since)
            ->when($until !== null, fn ($query) => $query->where('created_at', '<=', $until))
            ->select('threat_level', DB::raw('count(*) as count'))
            ->groupBy('threat_level')
            ->get()
            ->pluck('count', 'threat_level')
            ->toArray();

        return [
            'low' => $stats['low'] ?? 0,
            'medium' => $stats['medium'] ?? 0,
            'high' => $stats['high'] ?? 0,
            'critical' => $stats['critical'] ?? 0,
        ];
    }

    private function getTopEvents($since, $until = null): array
    {
        if (! $this->schemaHasTable('security_events')) {
            return [];
        }

        return DB::table('security_events')
            ->where('created_at', '>=', $since)
            ->when($until !== null, fn ($query) => $query->where('created_at', '<=', $until))
            ->select('event_type', DB::raw('count(*) as count'))
            ->groupBy('event_type')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getSuspiciousIPs($since): array
    {
        if (! $this->schemaHasTable('security_events')) {
            return [];
        }

        return DB::table('security_events')
            ->where('created_at', '>=', $since)
            ->whereIn('threat_level', ['high', 'critical'])
            ->select('ip_address', DB::raw('count(*) as count'))
            ->groupBy('ip_address')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getFailedLoginStats($since): array
    {
        if (! $this->schemaHasTable('security_events')) {
            return [];
        }

        $stats = DB::table('security_events')
            ->where('created_at', '>=', $since)
            ->where('event_type', 'failed_login')
            ->select('ip_address', DB::raw('count(*) as count'))
            ->groupBy('ip_address')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        return $stats;
    }

    private function getRecentAlerts(int $hours): array
    {
        if (! $this->schemaHasTable('security_alerts')) {
            return [];
        }

        return DB::table('security_alerts')
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    private function getIPLocation(string $ip): ?array
    {
        // This is a simplified implementation
        // In production, use a proper IP geolocation service
        try {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return null;
            }

            $context = stream_context_create([
                'http' => [
                    'timeout' => 1,
                ],
            ]);

            $response = @file_get_contents("http://ip-api.com/json/{$ip}", false, $context);
            $data = json_decode($response, true);
            
            if ($data && $data['status'] === 'success') {
                return [
                    'country' => $data['country'],
                    'country_code' => $data['countryCode'] ?? null,
                    'region' => $data['regionName'],
                    'city' => $data['city'],
                    'lat' => $data['lat'],
                    'lon' => $data['lon'],
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to get IP location', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    private function isUnusualLocation(array $location): bool
    {
        // Define expected locations (countries where your business operates)
        $expectedCountryCodes = ['US', 'CA', 'GB', 'DE', 'FR'];
        $expectedCountryNames = ['United States', 'Canada', 'United Kingdom', 'Germany', 'France'];

        $countryCode = strtoupper((string) ($location['country_code'] ?? ''));
        $countryName = (string) ($location['country'] ?? '');
        
        return ! in_array($countryCode, $expectedCountryCodes, true)
            && ! in_array($countryName, $expectedCountryNames, true);
    }

    public function blockIP(string $ip, string $reason = 'Security violation'): bool
    {
        try {
            if (! $this->schemaHasTable('blocked_ips')) {
                return false;
            }

            DB::table('blocked_ips')->updateOrInsert([
                'ip_address' => $ip,
            ], [
                'reason' => $reason,
                'blocked_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]);

            Log::info('IP address blocked', [
                'ip' => $ip,
                'reason' => $reason,
                'timestamp' => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to block IP address', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    public function isIPBlocked(string $ip): bool
    {
        if (! $this->schemaHasTable('blocked_ips')) {
            return false;
        }

        return DB::table('blocked_ips')
            ->where('ip_address', $ip)
            ->exists();
    }

    public function unblockIP(string $ip): bool
    {
        try {
            if (! $this->schemaHasTable('blocked_ips')) {
                return false;
            }

            return DB::table('blocked_ips')
                ->where('ip_address', $ip)
                ->delete() > 0;
        } catch (\Throwable $e) {
            Log::error('Failed to unblock IP address', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function generateSecurityReport($startDate, $endDate): array
    {
        $events = $this->schemaHasTable('security_events')
            ? DB::table('security_events')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
            : collect();

        $alerts = $this->schemaHasTable('security_alerts')
            ? DB::table('security_alerts')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
            : collect();

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'summary' => [
                'total_events' => $events->count(),
                'total_alerts' => $alerts->count(),
                'threat_levels' => $this->getThreatLevelStats($startDate, $endDate),
                'top_events' => $this->getTopEvents($startDate, $endDate),
            ],
            'events' => $events->toArray(),
            'alerts' => $alerts->toArray(),
            'recommendations' => $this->generateSecurityRecommendations($events, $alerts),
        ];
    }

    private function schemaHasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function generateSecurityRecommendations($events, $alerts): array
    {
        $recommendations = [];

        $failedLogins = $events->where('event_type', 'failed_login')->count();
        if ($failedLogins > 100) {
            $recommendations[] = 'Consider implementing stronger password policies';
        }

        $suspiciousIPs = $events->whereIn('threat_level', ['high', 'critical'])->pluck('ip_address')->unique()->count();
        if ($suspiciousIPs > 10) {
            $recommendations[] = 'Review and update IP whitelist/blacklist';
        }

        $criticalAlerts = $alerts->where('severity', 'critical')->count();
        if ($criticalAlerts > 0) {
            $recommendations[] = 'Immediate security review required';
        }

        return $recommendations;
    }
}
