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

    protected const EXTERNAL_SOURCE_MAP = [
        'phong vu' => 'Đối tác phân phối',
        'phong vũ' => 'Đối tác phân phối',
        'phongvu' => 'Đối tác phân phối',
        'phongvu.com' => 'Đối tác phân phối',
        'cellphones' => 'Đối tác phân phối',
        'cellphone s' => 'Đối tác phân phối',
        'cellphones.com.vn' => 'Đối tác phân phối',
        'fpt shop' => 'Đối tác phân phối',
        'fptshop' => 'Đối tác phân phối',
        'thế giới di động' => 'Đối tác phân phối',
        'the gioi di dong' => 'Đối tác phân phối',
        'thegioididong' => 'Đối tác phân phối',
        'thegioididong.com' => 'Đối tác phân phối',
        'tgdd' => 'Đối tác phân phối',
        'gearvn' => 'Đối tác phân phối',
        'hacom' => 'Đối tác phân phối',
        'hoàng hà mobile' => 'Đối tác phân phối',
        'hoang ha mobile' => 'Đối tác phân phối',
        'hnam mobile' => 'Đối tác phân phối',
        'hnam' => 'Đối tác phân phối',
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

    public static function neutralizeSource(?string $value, ?string $fallback = null): string
    {
        return static::sanitizeExternalSource(
            static::resolve($value, null, $fallback)
        );
    }

    public static function neutralizeSourceHtml(?string $value, ?string $fallback = null): string
    {
        $resolved = trim((string) ($value ?: $fallback ?: ''));

        if ($resolved === '') {
            return '';
        }

        return static::sanitizeExternalSource($resolved);
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

        return static::sanitizeExternalSource($resolved);
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

    protected static function sanitizeExternalSource(string $value): string
    {
        $sanitized = $value;

        foreach (static::EXTERNAL_SOURCE_MAP as $from => $to) {
            $sanitized = preg_replace('/' . preg_quote($from, '/') . '/iu', $to, $sanitized) ?? $sanitized;
        }

        return trim($sanitized);
    }
}
