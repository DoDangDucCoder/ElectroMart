<?php

namespace App\Exports;

use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;

class ExcelExports
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ProductCategory::all();
    }
}
