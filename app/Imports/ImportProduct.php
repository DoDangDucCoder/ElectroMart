<?php

namespace App\Imports;

use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use Illuminate\Support\Str;

class ImportProduct
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $name = trim((string) ($row[0] ?? ''));
        $thumbnail = trim((string) ($row[7] ?? ''));

        return new Product([
            'name' => $name,
            'slug' => trim((string) ($row[1] ?? '')) ?: Str::slug($name),
            'primary_category_id' => $row[2] ?? null,
            'brand_id' => $row[3] ?? null,
            'description' => $row[4] ?? null,
            'content' => $row[5] ?? null,
            'price' => (float) ($row[6] ?? 0),
            'thumbnail_path' => $thumbnail !== '' && ! str_contains($thumbnail, '/')
                ? 'uploads/product/'.$thumbnail
                : ($thumbnail ?: null),
            'is_active' => (int) ($row[8] ?? 0) === 0,
        ]);
    }
}
