<?php

namespace App\Exports;

use App\Infrastructure\Persistence\Eloquent\Catalog\Product;

class ExportProduct
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return Product::all();
    }
}
