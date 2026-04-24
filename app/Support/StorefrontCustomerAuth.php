<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StorefrontCustomerAuth
{
    public const SESSION_REDIRECT_KEY = 'storefront_customer_redirect';
    public const SESSION_REDIRECT_URL_KEY = 'storefront_customer_redirect_url';
    public const SESSION_NAME_KEY = 'storefront_customer_name';
    public const SESSION_EMAIL_KEY = 'storefront_customer_email';
    public const SESSION_PICTURE_KEY = 'storefront_customer_picture';
    protected const BLOCKED_PREFIXES = [
        '/admin',
        '/api',
        '/sanctum',
        '/broadcasting',
        '/_debugbar',
        '/telescope',
    ];

    protected const DESTINATIONS = [
        'login' => '/dang-nhap',
        'cart' => '/gio-hang',
        'checkout' => '/checkout',
        'payment' => '/payment',
        'history' => '/history',
    ];

    public static function fromRequest(Request $request, string $default = 'checkout'): string
    {
        return static::normalizeRedirectKey(
            $request->query('redirect')
                ?? $request->input('redirect_target')
                ?? Session::get(static::SESSION_REDIRECT_KEY),
            $default
        );
    }

    public static function rememberIntendedDestination(Request $request, string $default = 'checkout'): array
    {
        $key = static::rememberIntendedRedirect(
            static::fromRequest($request, $default),
            $default
        );

        $url = static::normalizeInternalUrl(
            $request->query('redirect_to')
                ?? $request->input('redirect_to')
                ?? $request->headers->get('referer')
                ?? Session::get(static::SESSION_REDIRECT_URL_KEY),
            $key
        );

        Session::put(static::SESSION_REDIRECT_URL_KEY, $url);

        return [
            'key' => $key,
            'url' => $url,
        ];
    }

    public static function rememberIntendedRedirect(?string $candidate, string $default = 'checkout'): string
    {
        $key = static::normalizeRedirectKey($candidate, $default);
        Session::put(static::SESSION_REDIRECT_KEY, $key);

        return $key;
    }

    public static function pullIntendedRedirect(string $default = 'checkout'): string
    {
        return static::normalizeRedirectKey(Session::pull(static::SESSION_REDIRECT_KEY), $default);
    }

    public static function pullIntendedUrl(string $default = 'checkout'): ?string
    {
        $stored = Session::pull(static::SESSION_REDIRECT_URL_KEY);

        if (trim((string) $stored) === '') {
            return null;
        }

        return static::normalizeInternalUrl($stored, $default);
    }

    public static function forgetIntendedRedirect(): void
    {
        Session::forget([
            static::SESSION_REDIRECT_KEY,
            static::SESSION_REDIRECT_URL_KEY,
        ]);
    }

    public static function destinationUrl(?string $candidate, string $default = 'checkout'): string
    {
        $key = static::normalizeRedirectKey($candidate, $default);

        return url(static::DESTINATIONS[$key]);
    }

    public static function normalizeRedirectKey(?string $candidate, string $default = 'checkout'): string
    {
        $candidate = strtolower(trim((string) $candidate));

        if ($candidate === '' || ! array_key_exists($candidate, static::DESTINATIONS)) {
            return array_key_exists($default, static::DESTINATIONS) ? $default : 'checkout';
        }

        return $candidate;
    }

    public static function normalizeInternalUrl(?string $candidate, string $default = 'checkout'): string
    {
        $fallback = static::destinationUrl($default, $default);
        $candidate = trim((string) $candidate);

        if ($candidate === '') {
            return $fallback;
        }

        $host = parse_url(url('/'), PHP_URL_HOST);
        $scheme = parse_url(url('/'), PHP_URL_SCHEME);
        $pathWithQuery = null;

        if (str_starts_with($candidate, '/')) {
            $pathWithQuery = $candidate;
        } else {
            $candidateHost = parse_url($candidate, PHP_URL_HOST);
            $candidateScheme = parse_url($candidate, PHP_URL_SCHEME);

            if (
                $candidateHost
                && $host
                && strcasecmp((string) $candidateHost, (string) $host) === 0
                && (!$candidateScheme || strcasecmp((string) $candidateScheme, (string) $scheme) === 0)
            ) {
                $path = parse_url($candidate, PHP_URL_PATH) ?: '/';
                $query = parse_url($candidate, PHP_URL_QUERY);
                $pathWithQuery = $query ? ($path . '?' . $query) : $path;
            }
        }

        if (! $pathWithQuery) {
            return $fallback;
        }

        $normalizedPath = strtok($pathWithQuery, '?') ?: '/';
        $blockedPaths = [
            '/dang-nhap',
            '/dang-ky',
            '/login-customer',
            '/add-customer',
            '/dang-xuat-khach-hang',
            '/logout-checkout',
            '/quen-mat-khau',
            '/update-new-pass',
            '/login-facebook-customer',
            '/customer/facebook/callback',
            '/login-customer-google',
            '/customer/google/callback',
        ];

        if (in_array($normalizedPath, $blockedPaths, true)) {
            return $fallback;
        }

        foreach (static::BLOCKED_PREFIXES as $blockedPrefix) {
            if ($normalizedPath === $blockedPrefix || str_starts_with($normalizedPath, $blockedPrefix . '/')) {
                return $fallback;
            }
        }

        return url($pathWithQuery);
    }

    public static function storeDisplaySession(?string $name, ?string $email, ?string $picture = null): void
    {
        static::putOrForget(static::SESSION_NAME_KEY, $name);
        static::putOrForget(static::SESSION_EMAIL_KEY, $email);
        static::putOrForget(static::SESSION_PICTURE_KEY, $picture);
    }

    public static function clearDisplaySession(): void
    {
        Session::forget([
            static::SESSION_NAME_KEY,
            static::SESSION_EMAIL_KEY,
            static::SESSION_PICTURE_KEY,
        ]);
    }

    protected static function putOrForget(string $key, ?string $value): void
    {
        $value = trim((string) $value);

        if ($value === '') {
            Session::forget($key);

            return;
        }

        Session::put($key, $value);
    }
}
