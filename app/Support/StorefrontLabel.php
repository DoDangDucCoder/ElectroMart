<?php

namespace App\Support;

use Illuminate\Support\Str;

class StorefrontLabel
{
    protected const LABEL_MAP = [
        'android khac' => 'Android khác',
        'bao hanh' => 'Bảo hành',
        'dien thoai' => 'Điện thoại',
        'khuyen mai' => 'Khuyến mãi',
        'laptop van phong' => 'Laptop văn phòng',
        'op lung kinh cuong luc' => 'Ốp lưng, kính cường lực',
        'phu kien' => 'Phụ kiện',
        'sac cap' => 'Sạc, cáp',
        'tin tuc' => 'Tin tức',
        'tin tuc cong nghe' => 'Tin tức công nghệ',
        'video cong nghe' => 'Video công nghệ',
    ];

    public static function category(?string $name, ?string $slug = null, ?string $fallback = null): string
    {
        return static::resolve($name, $slug, $fallback);
    }

    public static function blogCategory(?string $name, ?string $slug = null, ?string $fallback = null): string
    {
        return static::resolve($name, $slug, $fallback);
    }

    public static function text(?string $value, ?string $fallback = null): string
    {
        return static::resolve($value, null, $fallback);
    }

    protected static function resolve(?string $name, ?string $slug = null, ?string $fallback = null): string
    {
        foreach ([$slug, $name] as $candidate) {
            if (! filled($candidate)) {
                continue;
            }

            $mapped = static::mapped($candidate);
            if ($mapped !== null) {
                return $mapped;
            }
        }

        $resolved = trim((string) ($name ?: $fallback ?: ''));

        if ($resolved === '') {
            return '';
        }

        return $resolved;
    }

    protected static function mapped(string $value): ?string
    {
        $key = static::normalize($value);

        return static::LABEL_MAP[$key] ?? null;
    }

    protected static function normalize(string $value): string
    {
        return (string) Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->trim()
            ->squish();
    }
}
