<?php

namespace App\Imports;

use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Support\Str;

class ExcelImports
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $name = trim((string) ($row[1] ?? ''));

        return new ProductCategory([
            'name' => $name,
            'slug' => trim((string) ($row[2] ?? '')) ?: Str::slug($name),
            'description' => $row[3] ?? null,
            'is_active' => (int) ($row[4] ?? 0) === 0,
            'meta' => [
                'meta_keywords' => $row[0] ?? null,
            ],
        ]);
    }
}
