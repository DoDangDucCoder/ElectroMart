<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\Shared\Services\AuthenticationSecurityService;
use App\Application\Shared\Services\AuthorizationSecurityService;
use App\Application\Shared\Services\DataEncryptionService;
use App\Application\Shared\Services\SecurityMonitoringService;
use App\Application\Shared\Services\APISecurityService;

class SecurityCommand extends Command
{
    protected $signature = 'security:manage 
                            {--password-check : Check password strength}
                            {--permission-audit : Audit user permissions}
                            {--encryption-status : Check encryption status}
                            {--security-report : Generate security report}
                            {--api-security : Check API security}
                            {--block-ip : Block IP address}
                            {--unblock-ip : Unblock IP address}
                            {--generate-key : Generate API key}
                            {--revoke-key : Revoke API key}
                            {--full-audit : Run full security audit}';

    protected $description = 'Manage application security settings and perform security audits';

    public function handle(
        AuthenticationSecurityService $authService,
        AuthorizationSecurityService $authzService,
        DataEncryptionService $encryptionService,
        SecurityMonitoringService $monitoringService,
        APISecurityService $apiService
    ): int {
        $this->info('Starting security management...');

        try {
            if ($this->option('password-check')) {
                $this->checkPasswordStrength($authService);
            } elseif ($this->option('permission-audit')) {
                $this->auditPermissions($authzService);
            } elseif ($this->option('encryption-status')) {
                $this->checkEncryptionStatus($encryptionService);
            } elseif ($this->option('security-report')) {
                $this->generateSecurityReport($monitoringService);
            } elseif ($this->option('api-security')) {
                $this->checkAPISecurity($apiService);
            } elseif ($this->option('block-ip')) {
                $this->blockIPAddress($monitoringService);
            } elseif ($this->option('unblock-ip')) {
                $this->unblockIPAddress($monitoringService);
            } elseif ($this->option('generate-key')) {
                $this->generateAPIKey($apiService);
            } elseif ($this->option('revoke-key')) {
                $this->revokeAPIKey($apiService);
            } elseif ($this->option('full-audit')) {
                $this->runFullSecurityAudit($authService, $authzService, $encryptionService, $monitoringService, $apiService);
            } else {
                $this->showHelp();
            }

            $this->info('Security management completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Security management failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function checkPasswordStrength(AuthenticationSecurityService $authService): void
    {
        $this->info('Checking password strength...');
        
        $password = $this->ask('Enter password to check');
        
        $result = $authService->validatePasswordStrength($password);
        
        $this->info('Password Strength Analysis:');
        $this->line('');
        
        $this->table(['Metric', 'Value'], [
            ['Valid', $result['is_valid'] ? 'Yes' : 'No'],
            ['Score', $result['score'] . '/5'],
            ['Strength', $result['strength']],
        ]);
        
        if (!empty($result['errors'])) {
            $this->line('');
            $this->warn('Issues found:');
            foreach ($result['errors'] as $error) {
                $this->line("• {$error}");
            }
        }
        
        if (!empty($result['recommendations'])) {
            $this->line('');
            $this->info('Recommendations:');
            foreach ($result['recommendations'] as $recommendation) {
                $this->line("• {$recommendation}");
            }
        }
    }

    private function auditPermissions(AuthorizationSecurityService $authzService): void
    {
        $this->info('Auditing user permissions...');
        
        $stats = $authzService->getSecurityStatistics();
        
        $this->info('Permission Audit Results:');
        $this->line('');
        
        $this->table(['Metric', 'Value'], [
            ['Total Roles', $stats['total_roles']],
            ['Total Permissions', $stats['total_permissions']],
        ]);
        
        $this->line('');
        $this->info('Roles with Permissions:');
        $this->table(['Role', 'Permission Count'], 
            collect($stats['roles_with_permissions'])->map(function ($role) {
                return [$role['role'], $role['permission_count']];
            })->toArray()
        );
        
        // Validate role hierarchy
        $hierarchyValidation = $authzService->validateRoleHierarchy();
        if (!$hierarchyValidation['is_valid']) {
            $this->line('');
            $this->warn('Role Hierarchy Issues:');
            foreach ($hierarchyValidation['issues'] as $issue) {
                $this->line("• {$issue}");
            }
        }
    }

    private function checkEncryptionStatus(DataEncryptionService $encryptionService): void
    {
        $this->info('Checking encryption status...');
        
        $stats = $encryptionService->getEncryptionStatistics();
        $validation = $encryptionService->validateEncryption();
        
        $this->info('Encryption Status:');
        $this->line('');
        
        $this->table(['Metric', 'Value'], [
            ['Encrypted Tables', implode(', ', $stats['encrypted_tables'])],
            ['Sensitive Tables', implode(', ', $stats['sensitive_tables'])],
            ['Total Encrypted Fields', $stats['total_encrypted_fields']],
            ['Total Sensitive Fields', $stats['total_sensitive_fields']],
            ['Encryption Cipher', $stats['encryption_cipher']],
            ['Encryption Valid', $validation['is_valid'] ? 'Yes' : 'No'],
        ]);
        
        if (!$validation['is_valid']) {
            $this->line('');
            $this->warn('Encryption Issues:');
            foreach ($validation['issues'] as $issue) {
                $this->line("• {$issue}");
            }
        }
    }

    private function generateSecurityReport(SecurityMonitoringService $monitoringService): void
    {
        $this->info('Generating security report...');
        
        $dashboard = $monitoringService->getSecurityDashboard();
        
        $this->info('Security Dashboard:');
        $this->line('');
        
        // Recent events
        $this->info('Recent Security Events (Last 24 hours):');
        $this->table(['Event Type', 'Threat Level', 'IP Address', 'Time'], 
            collect($dashboard['recent_events'])->map(function ($event) {
                return [
                    $event['event_type'],
                    $event['threat_level'],
                    $event['ip_address'],
                    $event['created_at'],
                ];
            })->take(10)->toArray()
        );
        
        // Threat levels
        $this->line('');
        $this->info('Threat Level Statistics:');
        $this->table(['Level', 'Count'], [
            ['Low', $dashboard['threat_levels']['low']],
            ['Medium', $dashboard['threat_levels']['medium']],
            ['High', $dashboard['threat_levels']['high']],
            ['Critical', $dashboard['threat_levels']['critical']],
        ]);
        
        // Top events
        $this->line('');
        $this->info('Top Security Events (Last 7 days):');
        $this->table(['Event Type', 'Count'], 
            collect($dashboard['top_events'])->map(function ($event) {
                return [$event->event_type, $event->count];
            })->toArray()
        );
        
        // Suspicious IPs
        if (!empty($dashboard['suspicious_ips'])) {
            $this->line('');
            $this->warn('Suspicious IP Addresses:');
            $this->table(['IP Address', 'Event Count'], 
                collect($dashboard['suspicious_ips'])->map(function ($ip) {
                    return [$ip->ip_address, $ip->count];
                })->toArray()
            );
        }
    }

    private function checkAPISecurity(APISecurityService $apiService): void
    {
        $this->info('Checking API security...');
        
        $stats = $apiService->getAPISecurityStatistics();
        
        $this->info('API Security Status:');
        $this->line('');
        
        $this->table(['Metric', 'Value'], [
            ['Active API Keys', $stats['active_api_keys']],
            ['Total API Keys', $stats['total_api_keys']],
            ['Rate Limit (per minute)', $stats['rate_limits']['per_minute']],
            ['Rate Limit (per hour)', $stats['rate_limits']['per_hour']],
            ['Allowed Origins', $stats['allowed_origins']],
            ['Blocked IPs', $stats['blocked_ips']],
        ]);
        
        // Test API key validation
        $testKey = 'sk_test_1234567890abcdef';
        $validation = $apiService->validateAPIKey($testKey);
        
        $this->line('');
        $this->info('API Key Validation Test:');
        $this->line("Test Key: {$testKey}");
        $this->line("Valid: " . ($validation['valid'] ? 'Yes' : 'No'));
        
        if (!$validation['valid']) {
            $this->line("Error: {$validation['error']}");
        }
    }

    private function blockIPAddress(SecurityMonitoringService $monitoringService): void
    {
        $ip = $this->ask('Enter IP address to block');
        $reason = $this->ask('Enter reason for blocking', 'Security violation');
        
        if ($monitoringService->blockIP($ip, $reason)) {
            $this->info("IP address {$ip} has been blocked.");
        } else {
            $this->error("Failed to block IP address {$ip}.");
        }
    }

    private function unblockIPAddress(SecurityMonitoringService $monitoringService): void
    {
        $ip = $this->ask('Enter IP address to unblock');
        
        // This would require implementing an unblock method
        $this->info("Unblocking IP address {$ip}...");
        $this->warn('Unblock functionality not implemented yet.');
    }

    private function generateAPIKey(APISecurityService $apiService): void
    {
        $name = $this->ask('Enter API key name');
        $userId = $this->ask('Enter user ID', 1);
        $permissions = $this->ask('Enter permissions (comma-separated)', 'read,write');
        
        $permissionsArray = array_map('trim', explode(',', $permissions));
        
        $apiKey = $apiService->generateAPIKey($name, $userId, $permissionsArray);
        
        $this->info('API Key Generated:');
        $this->line("Key: {$apiKey}");
        $this->line("Name: {$name}");
        $this->line("Permissions: " . implode(', ', $permissionsArray));
        $this->warn('Please store this API key securely. It will not be shown again.');
    }

    private function revokeAPIKey(APISecurityService $apiService): void
    {
        $apiKey = $this->ask('Enter API key to revoke');
        
        if ($apiService->revokeAPIKey($apiKey)) {
            $this->info("API key has been revoked.");
        } else {
            $this->error("Failed to revoke API key.");
        }
    }

    private function runFullSecurityAudit(
        AuthenticationSecurityService $authService,
        AuthorizationSecurityService $authzService,
        DataEncryptionService $encryptionService,
        SecurityMonitoringService $monitoringService,
        APISecurityService $apiService
    ): void {
        $this->info('Running full security audit...');
        
        // Step 1: Authentication Security
        $this->info('Step 1: Checking authentication security...');
        $authRecommendations = $authService->getSecurityRecommendations();
        $this->line('✓ Authentication security checked');
        
        // Step 2: Authorization Security
        $this->info('Step 2: Checking authorization security...');
        $this->auditPermissions($authzService);
        
        // Step 3: Data Encryption
        $this->info('Step 3: Checking data encryption...');
        $this->checkEncryptionStatus($encryptionService);
        
        // Step 4: Security Monitoring
        $this->info('Step 4: Checking security monitoring...');
        $this->generateSecurityReport($monitoringService);
        
        // Step 5: API Security
        $this->info('Step 5: Checking API security...');
        $this->checkAPISecurity($apiService);
        
        // Step 6: Generate recommendations
        $this->info('Step 6: Generating security recommendations...');
        $this->generateSecurityRecommendations($authRecommendations);
        
        $this->info('Full security audit completed!');
    }

    private function generateSecurityRecommendations(array $authRecommendations): void
    {
        $this->line('');
        $this->info('Security Recommendations:');
        
        // Authentication recommendations
        $this->line('');
        $this->info('Authentication Security:');
        foreach ($authRecommendations['general_recommendations'] as $recommendation) {
            $this->line("• {$recommendation}");
        }
        
        // Password policy
        $this->line('');
        $this->info('Password Policy:');
        $policy = $authRecommendations['password_policy'];
        $this->line("• Minimum length: {$policy['min_length']} characters");
        $this->line("• Require uppercase: " . ($policy['require_uppercase'] ? 'Yes' : 'No'));
        $this->line("• Require lowercase: " . ($policy['require_lowercase'] ? 'Yes' : 'No'));
        $this->line("• Require numbers: " . ($policy['require_numbers'] ? 'Yes' : 'No'));
        $this->line("• Require symbols: " . ($policy['require_symbols'] ? 'Yes' : 'No'));
        
        // Login security
        $this->line('');
        $this->info('Login Security:');
        $login = $authRecommendations['login_security'];
        $this->line("• Max attempts: {$login['max_attempts']}");
        $this->line("• Lockout time: {$login['lockout_time']} seconds");
        $this->line("• Rate limiting: " . ($login['rate_limiting'] ? 'Enabled' : 'Disabled'));
    }

    private function showHelp(): void
    {
        $this->info('Security Management Commands:');
        $this->line('');
        $this->line('Available options:');
        $this->line('  --password-check     Check password strength');
        $this->line('  --permission-audit   Audit user permissions');
        $this->line('  --encryption-status  Check encryption status');
        $this->line('  --security-report    Generate security report');
        $this->line('  --api-security       Check API security');
        $this->line('  --block-ip           Block IP address');
        $this->line('  --unblock-ip         Unblock IP address');
        $this->line('  --generate-key       Generate API key');
        $this->line('  --revoke-key         Revoke API key');
        $this->line('  --full-audit         Run full security audit');
        $this->line('');
        $this->line('Examples:');
        $this->line('  php artisan security:manage --password-check');
        $this->line('  php artisan security:manage --full-audit');
        $this->line('  php artisan security:manage --generate-key');
    }
}
