<?php

namespace App\Application\Shared\Services;

use App\Domain\Shared\ValueObjects\Slug;
use Illuminate\Support\Str;

class SlugService
{
    public function generateSlug(string $text): Slug
    {
        return Slug::fromString($text);
    }

    public function generateUniqueSlug(string $text, string $modelClass, ?int $excludeId = null): Slug
    {
        $baseSlug = Slug::fromString($text);
        $slug = $baseSlug;

        $counter = 1;
        while ($this->slugExists($slug->getValue(), $modelClass, $excludeId)) {
            $slug = Slug::fromString($text . '-' . $counter);
            $counter++;
        }

        return $slug;
    }

    public function slugExists(string $slug, string $modelClass, ?int $excludeId = null): bool
    {
        $query = $modelClass::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function validateSlug(string $slug): bool
    {
        return preg_match('/^[a-z0-9-]+$/', $slug) === 1;
    }
}
