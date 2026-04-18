<?php

namespace App\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use stdClass;

final class StorefrontLink
{
    public static function normalize(iterable $links, string $urlKey = 'href', array $defaults = []): Collection
    {
        $defaults = array_merge([
            'icon' => 'fa-angle-right',
            'label' => '',
            'note' => '',
            'badge' => '',
            'class' => '',
        ], $defaults);

        return collect($links)
            ->map(function ($link) use ($urlKey, $defaults): ?array {
                $link = self::toArray($link);

                if (! is_array($link)) {
                    return null;
                }

                $url = self::resolveUrl($link, $urlKey);

                if ($url === '') {
                    return null;
                }

                $normalized = array_merge($defaults, $link, [
                    $urlKey => $url,
                ]);

                if (in_array($urlKey, ['href', 'url'], true)) {
                    $alternateKey = $urlKey === 'href' ? 'url' : 'href';
                    $normalized[$alternateKey] = self::resolveUrl($normalized, $alternateKey) ?: $url;
                }

                return $normalized;
            })
            ->filter()
            ->values();
    }

    protected static function resolveUrl(array $link, string $urlKey): string
    {
        $value = trim((string) ($link[$urlKey] ?? ''));

        if ($value !== '') {
            return $value;
        }

        if ($urlKey === 'href') {
            return trim((string) ($link['url'] ?? ''));
        }

        if ($urlKey === 'url') {
            return trim((string) ($link['href'] ?? ''));
        }

        return '';
    }

    protected static function toArray(mixed $link): mixed
    {
        if ($link instanceof Arrayable) {
            return $link->toArray();
        }

        if ($link instanceof stdClass) {
            return (array) $link;
        }

        return $link;
    }
}
