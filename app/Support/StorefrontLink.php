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

                $url = trim((string) ($link[$urlKey] ?? ''));

                if ($url === '') {
                    return null;
                }

                return array_merge($defaults, $link, [
                    $urlKey => $url,
                ]);
            })
            ->filter()
            ->values();
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
