<?php

namespace App\Support;

class AssetUrl
{
    public const PRODUCT_FALLBACK = 'uploads/product/no-image.svg';

    public const PRODUCT_IMAGE_VARIANTS = [
        'thumb' => 360,
        'medium' => 800,
        'large' => 1200,
    ];

    public static function product(?string $path = null): string
    {
        return self::resolve($path, self::PRODUCT_FALLBACK);
    }

    public static function productVariant(?string $path = null, string $variant = 'thumb'): string
    {
        $optimizedPath = self::optimizedProductPath($path, $variant);

        if ($optimizedPath !== null && file_exists(public_path($optimizedPath))) {
            return asset($optimizedPath);
        }

        return self::product($path);
    }

    public static function optimizedProductPath(?string $path, string $variant = 'thumb'): ?string
    {
        $path = trim((string) $path);
        $variant = array_key_exists($variant, self::PRODUCT_IMAGE_VARIANTS) ? $variant : 'thumb';

        if ($path === '' || self::isAbsoluteUrl($path)) {
            return null;
        }

        $localPath = str_replace('\\', '/', ltrim($path, '/\\'));

        if (str_contains($localPath, '/_optimized/')) {
            return $localPath;
        }

        $extension = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return null;
        }

        $directory = trim(str_replace('\\', '/', pathinfo($localPath, PATHINFO_DIRNAME)), '.');
        $filename = pathinfo($localPath, PATHINFO_FILENAME);
        $optimized = '_optimized/'.$filename.'-'.$variant.'.webp';

        return $directory !== '' ? $directory.'/'.$optimized : $optimized;
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
