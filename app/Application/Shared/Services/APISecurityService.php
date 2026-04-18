<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class APISecurityService
{
    private const RUNTIME_CACHE_KEY = 'api_security_runtime_keys';

    private array $apiKeys = [];
    private int $rateLimitPerMinute = 60;
    private int $rateLimitPerHour = 1000;
    private array $allowedOrigins = [];
    private array $blockedIPs = [];

    public function __construct()
    {
        $this->initializeAPISecurity();
    }

    private function initializeAPISecurity(): void
    {
        $configuredOrigins = (array) (
            config('security.api.allowed_origins')
            ?? config('services.api_security.allowed_origins')
            ?? []
        );

        if (empty($configuredOrigins)) {
            $configuredOrigins = array_filter([
                config('app.url'),
                app()->environment(['local', 'testing']) ? 'http://localhost:3000' : null,
            ]);
        }

        $this->allowedOrigins = array_values(array_unique(array_filter(array_map(
            fn ($origin) => is_string($origin) ? trim($origin) : null,
            $configuredOrigins
        ))));

        $this->blockedIPs = [
            // Add known malicious IPs
        ];
    }

    public function validateAPIKey(string $apiKey): array
    {
        if (empty($apiKey)) {
            return [
                'valid' => false,
                'error' => 'API key is required',
            ];
        }

        // Check if API key exists and is active
        $keyData = $this->getAPIKeyData($apiKey);
        if (!$keyData) {
            $this->logSecurityEvent('invalid_api_key', [
                'api_key' => substr($apiKey, 0, 8) . '...',
                'ip' => request()->ip(),
            ]);

            return [
                'valid' => false,
                'error' => 'Invalid API key',
            ];
        }

        // Check if API key is active
        if (!$keyData['is_active']) {
            return [
                'valid' => false,
                'error' => 'API key is inactive',
            ];
        }

        // Check if API key has expired
        if ($keyData['expires_at'] && now()->isAfter($keyData['expires_at'])) {
            return [
                'valid' => false,
                'error' => 'API key has expired',
            ];
        }

        // Check rate limits
        if (!$this->checkRateLimit($apiKey)) {
            return [
                'valid' => false,
                'error' => 'Rate limit exceeded',
            ];
        }

        return [
            'valid' => true,
            'key_data' => $keyData,
        ];
    }

    private function getAPIKeyData(string $apiKey): ?array
    {
        $cacheKey = "api_key_data_" . md5($apiKey);
        
        return Cache::remember($cacheKey, 3600, function () use ($apiKey) {
            // In production, this would query the database
            $keys = $this->getStoredAPIKeys();
            return $keys[$apiKey] ?? null;
        });
    }

    private function getStoredAPIKeys(): array
    {
        return array_merge($this->configuredApiKeys(), $this->runtimeApiKeys());
    }

    public function checkRateLimit(string $apiKey): bool
    {
        $minuteKey = "api_rate_limit_minute_{$apiKey}";
        $hourKey = "api_rate_limit_hour_{$apiKey}";

        // Check minute rate limit
        if (RateLimiter::tooManyAttempts($minuteKey, $this->rateLimitPerMinute)) {
            $this->logSecurityEvent('rate_limit_exceeded', [
                'api_key' => substr($apiKey, 0, 8) . '...',
                'limit_type' => 'minute',
                'limit' => $this->rateLimitPerMinute,
                'ip' => request()->ip(),
            ]);

            return false;
        }

        // Check hour rate limit
        if (RateLimiter::tooManyAttempts($hourKey, $this->rateLimitPerHour)) {
            $this->logSecurityEvent('rate_limit_exceeded', [
                'api_key' => substr($apiKey, 0, 8) . '...',
                'limit_type' => 'hour',
                'limit' => $this->rateLimitPerHour,
                'ip' => request()->ip(),
            ]);

            return false;
        }

        // Increment counters
        RateLimiter::hit($minuteKey, 60);
        RateLimiter::hit($hourKey, 3600);

        return true;
    }

    public function validateCORS(string $origin): bool
    {
        if (empty($origin)) {
            return false;
        }

        $isAllowed = in_array($origin, $this->allowedOrigins);
        
        if (!$isAllowed) {
            $this->logSecurityEvent('cors_violation', [
                'origin' => $origin,
                'ip' => request()->ip(),
            ]);
        }

        return $isAllowed;
    }

    public function validateIPAddress(string $ip): bool
    {
        // Check if IP is blocked
        if (in_array($ip, $this->blockedIPs)) {
            $this->logSecurityEvent('blocked_ip_access', [
                'ip' => $ip,
            ]);
            return false;
        }

        // Check if IP is in whitelist (if configured)
        $whitelist = $this->getIPWhitelist();
        if (!empty($whitelist) && !in_array($ip, $whitelist)) {
            $this->logSecurityEvent('ip_not_whitelisted', [
                'ip' => $ip,
            ]);
            return false;
        }

        return true;
    }

    private function getIPWhitelist(): array
    {
        // In production, load from configuration or database
        return [];
    }

    public function validateRequestSignature(string $signature, string $payload, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        
        if (!hash_equals($expectedSignature, $signature)) {
            $this->logSecurityEvent('invalid_signature', [
                'provided_signature' => substr($signature, 0, 8) . '...',
                'ip' => request()->ip(),
            ]);
            return false;
        }

        return true;
    }

    public function generateAPIKey(string $name, int $userId, array $permissions = [], $expiresAt = null): string
    {
        $prefix = 'sk_live_';
        $key = $prefix . Str::random(32);
        
        $keyData = [
            'id' => uniqid(),
            'name' => $name,
            'user_id' => $userId,
            'is_active' => true,
            'permissions' => $permissions,
            'expires_at' => $expiresAt,
            'created_at' => now(),
        ];

        // Store API key (in production, save to database)
        $this->apiKeys[$key] = $keyData;
        $runtimeKeys = $this->runtimeApiKeys();
        $runtimeKeys[$key] = $keyData;
        Cache::forever(self::RUNTIME_CACHE_KEY, $runtimeKeys);
        Cache::forget($this->apiKeyCacheKey($key));

        Log::info('API key generated', [
            'name' => $name,
            'user_id' => $userId,
            'permissions' => $permissions,
            'expires_at' => $expiresAt,
        ]);

        return $key;
    }

    public function revokeAPIKey(string $apiKey): bool
    {
        $runtimeKeys = $this->runtimeApiKeys();

        if (isset($runtimeKeys[$apiKey])) {
            $runtimeKeys[$apiKey]['is_active'] = false;
            $this->apiKeys[$apiKey] = $runtimeKeys[$apiKey];
            Cache::forever(self::RUNTIME_CACHE_KEY, $runtimeKeys);
            Cache::forget($this->apiKeyCacheKey($apiKey));
            
            Log::info('API key revoked', [
                'api_key' => substr($apiKey, 0, 8) . '...',
            ]);

            return true;
        }

        return false;
    }

    public function checkPermission(string $apiKey, string $permission): bool
    {
        $keyData = $this->getAPIKeyData($apiKey);
        if (!$keyData) {
            return false;
        }

        return in_array($permission, $keyData['permissions']);
    }

    public function logAPIRequest(string $apiKey, string $endpoint, array $requestData = []): void
    {
        $keyData = $this->getAPIKeyData($apiKey);
        
        Log::info('API request', [
            'api_key_id' => $keyData['id'] ?? 'unknown',
            'endpoint' => $endpoint,
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_size' => strlen(json_encode($requestData)),
            'timestamp' => now(),
        ]);

        // Update last used timestamp
        if ($keyData) {
            $this->updateLastUsed($apiKey);
        }
    }

    private function updateLastUsed(string $apiKey): void
    {
        $runtimeKeys = $this->runtimeApiKeys();

        if (isset($runtimeKeys[$apiKey])) {
            $runtimeKeys[$apiKey]['last_used_at'] = now();
            $this->apiKeys[$apiKey] = $runtimeKeys[$apiKey];
            Cache::forever(self::RUNTIME_CACHE_KEY, $runtimeKeys);
            Cache::forget($this->apiKeyCacheKey($apiKey));
        }
    }

    public function validateRequestSize(int $maxSize = 1048576): bool // 1MB default
    {
        $contentLength = request()->header('Content-Length', 0);
        
        if ($contentLength > $maxSize) {
            $this->logSecurityEvent('request_too_large', [
                'content_length' => $contentLength,
                'max_size' => $maxSize,
                'ip' => request()->ip(),
            ]);
            return false;
        }

        return true;
    }

    public function validateRequestHeaders(): array
    {
        $errors = [];
        $requiredHeaders = [
            'User-Agent',
            'Accept',
        ];

        foreach ($requiredHeaders as $header) {
            if (!request()->hasHeader($header)) {
                $errors[] = "Missing required header: {$header}";
            }
        }

        // Check for suspicious headers
        $suspiciousHeaders = [
            'X-Forwarded-For' => '/^[0-9.,\s]+$/',
            'X-Real-IP' => '/^[0-9.]+$/',
        ];

        foreach ($suspiciousHeaders as $header => $pattern) {
            $value = request()->header($header);
            if ($value && !preg_match($pattern, $value)) {
                $errors[] = "Suspicious header value: {$header}";
            }
        }

        if (!empty($errors)) {
            $this->logSecurityEvent('invalid_headers', [
                'errors' => $errors,
                'ip' => request()->ip(),
            ]);
        }

        return $errors;
    }

    public function sanitizeInput(array $input): array
    {
        $sanitized = [];
        
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                // Remove null bytes
                $value = str_replace("\0", '', $value);
                
                // Trim whitespace
                $value = trim($value);
                
                // Escape special characters
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
            
            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    public function validateJSONPayload(string $json): array
    {
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'valid' => false,
                'error' => 'Invalid JSON: ' . json_last_error_msg(),
            ];
        }

        // Check for JSON depth
        if ($this->getJSONDepth($data) > 10) {
            return [
                'valid' => false,
                'error' => 'JSON depth too deep',
            ];
        }

        return [
            'valid' => true,
            'data' => $data,
        ];
    }

    private function getJSONDepth(array $data, int $depth = 0): int
    {
        $maxDepth = $depth;
        
        foreach ($data as $value) {
            if (is_array($value)) {
                $currentDepth = $this->getJSONDepth($value, $depth + 1);
                $maxDepth = max($maxDepth, $currentDepth);
            }
        }

        return $maxDepth;
    }

    public function generateNonce(): string
    {
        return Str::random(32);
    }

    public function validateNonce(string $nonce, int $ttl = 300): bool // 5 minutes default
    {
        if (trim($nonce) === '') {
            return false;
        }

        $cacheKey = "api_nonce_{$nonce}";
        
        if (Cache::has($cacheKey)) {
            return false; // Nonce already used
        }

        Cache::put($cacheKey, true, $ttl);
        return true;
    }

    public function getAPISecurityStatistics(): array
    {
        $keys = $this->getStoredAPIKeys();

        return [
            'active_api_keys' => count(array_filter($keys, fn($key) => ($key['is_active'] ?? false) === true)),
            'total_api_keys' => count($keys),
            'rate_limits' => [
                'per_minute' => $this->rateLimitPerMinute,
                'per_hour' => $this->rateLimitPerHour,
            ],
            'allowed_origins' => count($this->allowedOrigins),
            'blocked_ips' => count($this->blockedIPs),
        ];
    }

    private function logSecurityEvent(string $event, array $data): void
    {
        Log::warning('API Security Event', [
            'event' => $event,
            'data' => $data,
            'timestamp' => now(),
        ]);
    }

    public function createSecurityHeaders(): array
    {
        return [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Content-Security-Policy' => "default-src 'self'",
            'X-API-Version' => '1.0',
            'X-Rate-Limit-Limit' => $this->rateLimitPerMinute,
            'X-Rate-Limit-Remaining' => $this->getRemainingRateLimit(),
        ];
    }

    private function getRemainingRateLimit(): int
    {
        $apiKey = request()->header('X-API-Key');

        if (! is_string($apiKey) || trim($apiKey) === '') {
            $authorization = (string) request()->header('Authorization', '');

            if (str_starts_with($authorization, 'Bearer ')) {
                $apiKey = trim(substr($authorization, 7));
            }
        }

        if (! is_string($apiKey) || trim($apiKey) === '') {
            return $this->rateLimitPerMinute;
        }

        return max(0, RateLimiter::remaining("api_rate_limit_minute_{$apiKey}", $this->rateLimitPerMinute));
    }

    private function configuredApiKeys(): array
    {
        $configured = config('security.api.keys') ?? config('services.api_security.keys') ?? [];

        if (! is_array($configured)) {
            return [];
        }

        return $configured;
    }

    private function runtimeApiKeys(): array
    {
        $keys = Cache::get(self::RUNTIME_CACHE_KEY, []);

        return is_array($keys) ? $keys : [];
    }

    private function apiKeyCacheKey(string $apiKey): string
    {
        return 'api_key_data_' . md5($apiKey);
    }
}
