<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthenticationSecurityService
{
    private int $maxLoginAttempts = 5;
    private int $lockoutTime = 900; // 15 minutes
    private int $passwordMinLength = 8;
    private array $passwordRequirements = [
        'uppercase' => true,
        'lowercase' => true,
        'numbers' => true,
        'symbols' => true,
    ];

    public function validatePasswordStrength(string $password): array
    {
        $errors = [];
        $score = 0;

        // Length check
        if (strlen($password) < $this->passwordMinLength) {
            $errors[] = "Password must be at least {$this->passwordMinLength} characters long";
        } else {
            $score += 1;
        }

        // Uppercase check
        if ($this->passwordRequirements['uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        } else {
            $score += 1;
        }

        // Lowercase check
        if ($this->passwordRequirements['lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        } else {
            $score += 1;
        }

        // Numbers check
        if ($this->passwordRequirements['numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        } else {
            $score += 1;
        }

        // Symbols check
        if ($this->passwordRequirements['symbols'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        } else {
            $score += 1;
        }

        // Common password check
        if ($this->isCommonPassword($password)) {
            $errors[] = 'Password is too common and easily guessable';
            $score -= 2;
        }

        // Sequential characters check
        if ($this->hasSequentialCharacters($password)) {
            $errors[] = 'Password contains sequential characters';
            $score -= 1;
        }

        // Repeated characters check
        if ($this->hasRepeatedCharacters($password)) {
            $errors[] = 'Password contains too many repeated characters';
            $score -= 1;
        }

        $strength = $this->calculatePasswordStrength($score);

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'score' => max(0, $score),
            'strength' => $strength,
            'recommendations' => $this->getPasswordRecommendations($password),
        ];
    }

    private function isCommonPassword(string $password): bool
    {
        $commonPasswords = [
            'password', '123456', '123456789', 'qwerty', 'abc123',
            'password123', 'admin', 'letmein', 'welcome', 'monkey',
            '1234567890', 'password1', 'qwerty123', 'dragon', 'master',
        ];

        return in_array(strtolower($password), $commonPasswords);
    }

    private function hasSequentialCharacters(string $password): bool
    {
        $sequences = ['123', '234', '345', '456', '567', '678', '789', '890'];
        $password = strtolower($password);

        foreach ($sequences as $sequence) {
            if (strpos($password, $sequence) !== false) {
                return true;
            }
        }

        return false;
    }

    private function hasRepeatedCharacters(string $password): bool
    {
        $chars = str_split($password);
        $charCounts = array_count_values($chars);
        
        foreach ($charCounts as $count) {
            if ($count > 2) {
                return true;
            }
        }

        return false;
    }

    private function calculatePasswordStrength(int $score): string
    {
        if ($score >= 5) return 'Very Strong';
        if ($score >= 4) return 'Strong';
        if ($score >= 3) return 'Medium';
        if ($score >= 2) return 'Weak';
        return 'Very Weak';
    }

    private function getPasswordRecommendations(string $password): array
    {
        $recommendations = [];

        if (strlen($password) < 12) {
            $recommendations[] = 'Use at least 12 characters for better security';
        }

        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $recommendations[] = 'Include special characters like !@#$%^&*';
        }

        if (preg_match('/\b\w{1,3}\b/', $password)) {
            $recommendations[] = 'Avoid short words or common patterns';
        }

        return $recommendations;
    }

    public function checkLoginAttempts(string $identifier): array
    {
        $key = $this->getLoginAttemptKey($identifier);
        $attempts = RateLimiter::attempts($key);
        $remaining = RateLimiter::remaining($key, $this->maxLoginAttempts);
        $lockoutTime = RateLimiter::availableIn($key);

        return [
            'attempts' => $attempts,
            'remaining' => $remaining,
            'is_locked' => $attempts >= $this->maxLoginAttempts,
            'lockout_time' => $lockoutTime,
            'lockout_minutes' => ceil($lockoutTime / 60),
        ];
    }

    public function recordLoginAttempt(string $identifier, bool $successful, string $ip = null): void
    {
        $key = $this->getLoginAttemptKey($identifier);
        
        if ($successful) {
            RateLimiter::clear($key);
            Log::info('Successful login', [
                'identifier' => $identifier,
                'ip' => $ip,
                'timestamp' => now(),
            ]);
        } else {
            RateLimiter::hit($key, $this->lockoutTime);
            
            Log::warning('Failed login attempt', [
                'identifier' => $identifier,
                'ip' => $ip,
                'attempts' => RateLimiter::attempts($key),
                'timestamp' => now(),
            ]);

            // Check if account should be locked
            if (RateLimiter::attempts($key) >= $this->maxLoginAttempts) {
                $this->lockAccount($identifier, $ip);
            }
        }
    }

    private function lockAccount(string $identifier, string $ip = null): void
    {
        $lockKey = "account_locked_{$identifier}";
        Cache::put($lockKey, true, $this->lockoutTime);

        Log::critical('Account locked due to multiple failed attempts', [
            'identifier' => $identifier,
            'ip' => $ip,
            'lockout_duration' => $this->lockoutTime,
            'timestamp' => now(),
        ]);
    }

    public function isAccountLocked(string $identifier): bool
    {
        $lockKey = "account_locked_{$identifier}";
        return Cache::has($lockKey);
    }

    public function unlockAccount(string $identifier): void
    {
        $lockKey = "account_locked_{$identifier}";
        Cache::forget($lockKey);
        
        $attemptKey = $this->getLoginAttemptKey($identifier);
        RateLimiter::clear($attemptKey);

        Log::info('Account unlocked', [
            'identifier' => $identifier,
            'timestamp' => now(),
        ]);
    }

    private function getLoginAttemptKey(string $identifier): string
    {
        return "login_attempts_" . md5($identifier);
    }

    public function generateSecureToken(int $length = 32): string
    {
        return Str::random($length);
    }

    public function generateSecurePassword(int $length = 16): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        $all = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }

    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    public function needsRehash(string $hash): bool
    {
        return Hash::needsRehash($hash);
    }

    public function validateEmail(string $email): array
    {
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        // Check for disposable email domains
        if ($this->isDisposableEmail($email)) {
            $errors[] = 'Disposable email addresses are not allowed';
        }

        // Check for suspicious patterns
        if ($this->hasSuspiciousEmailPattern($email)) {
            $errors[] = 'Email contains suspicious patterns';
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    private function isDisposableEmail(string $email): bool
    {
        $domain = substr(strrchr($email, "@"), 1);
        $disposableDomains = [
            '10minutemail.com', 'tempmail.org', 'guerrillamail.com',
            'mailinator.com', 'temp-mail.org', 'throwaway.email',
        ];

        return in_array($domain, $disposableDomains);
    }

    private function hasSuspiciousEmailPattern(string $email): bool
    {
        $suspiciousPatterns = [
            '/\d{4,}/', // Multiple consecutive numbers
            '/[^a-zA-Z0-9@._-]/', // Special characters
            '/\.{2,}/', // Multiple consecutive dots
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $email)) {
                return true;
            }
        }

        return false;
    }

    public function detectSuspiciousActivity(string $identifier, array $activity): array
    {
        $suspicious = [];

        // Check for rapid login attempts
        if (isset($activity['rapid_attempts']) && $activity['rapid_attempts'] > 10) {
            $suspicious[] = 'Rapid login attempts detected';
        }

        // Check for unusual IP addresses
        if (isset($activity['ip_changes']) && $activity['ip_changes'] > 5) {
            $suspicious[] = 'Multiple IP address changes detected';
        }

        // Check for unusual time patterns
        if (isset($activity['unusual_hours']) && $activity['unusual_hours']) {
            $suspicious[] = 'Login attempts during unusual hours';
        }

        return [
            'is_suspicious' => !empty($suspicious),
            'flags' => $suspicious,
            'risk_level' => $this->calculateRiskLevel($suspicious),
        ];
    }

    private function calculateRiskLevel(array $suspicious): string
    {
        $count = count($suspicious);
        
        if ($count >= 3) return 'High';
        if ($count >= 2) return 'Medium';
        if ($count >= 1) return 'Low';
        return 'None';
    }

    public function getSecurityRecommendations(): array
    {
        return [
            'password_policy' => [
                'min_length' => $this->passwordMinLength,
                'require_uppercase' => $this->passwordRequirements['uppercase'],
                'require_lowercase' => $this->passwordRequirements['lowercase'],
                'require_numbers' => $this->passwordRequirements['numbers'],
                'require_symbols' => $this->passwordRequirements['symbols'],
            ],
            'login_security' => [
                'max_attempts' => $this->maxLoginAttempts,
                'lockout_time' => $this->lockoutTime,
                'rate_limiting' => true,
            ],
            'general_recommendations' => [
                'Enable two-factor authentication',
                'Use HTTPS for all communications',
                'Implement session timeout',
                'Regular security audits',
                'Monitor failed login attempts',
            ],
        ];
    }
}
