<?php

namespace App\Support;

class AssetUrl
{
    public const PRODUCT_FALLBACK = 'uploads/product/no-image.svg';

    public static function product(?string $path = null): string
    {
        return self::resolve($path, self::PRODUCT_FALLBACK);
    }

    public static function resolve(?string $path, ?string $fallback = null): ?string
    {
        $path = trim((string) $path);

        if (self::isAbsoluteUrl($path)) {
            return $path;
        }

        if ($path !== '') {
            $localPath = ltrim($path, '/\\');

            if (file_exists(public_path($localPath))) {
                return asset($localPath);
            }
        }

        if ($fallback === null) {
            return null;
        }

        if (self::isAbsoluteUrl($fallback)) {
            return $fallback;
        }

        return asset(ltrim($fallback, '/\\'));
    }

    public static function rawOrFallback(?string $path, ?string $fallback = self::PRODUCT_FALLBACK): string
    {
        $path = trim((string) $path);

        return $path !== '' ? $path : (string) $fallback;
    }

    public static function isAbsoluteUrl(?string $path): bool
    {
        return is_string($path) && preg_match('#^https?://#i', trim($path)) === 1;
    }
}
