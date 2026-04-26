<?php

$ipWhitelist = array_values(array_filter(array_map(
    static fn (string $ip): string => trim($ip),
    explode(',', (string) env('IP_WHITELIST', ''))
)));

$apiAllowedOrigins = array_values(array_filter(array_map(
    static fn (string $origin): string => trim($origin),
    explode(',', (string) env('API_ALLOWED_ORIGINS', ''))
)));

$apiKeys = array_values(array_filter(array_map(
    static fn (string $key): string => trim($key),
    explode(',', (string) env('API_SECURITY_KEYS', ''))
)));

return [
    'force_https' => filter_var(
        env('FORCE_HTTPS', env('APP_ENV', 'production') === 'production'),
        FILTER_VALIDATE_BOOL
    ),

    'ip_whitelist' => [
        'enabled' => filter_var(env('IP_WHITELIST_ENABLED', false), FILTER_VALIDATE_BOOL),
        'allowed' => $ipWhitelist,
    ],

    'api' => [
        'allowed_origins' => $apiAllowedOrigins,
        'keys' => $apiKeys,
    ],
];
