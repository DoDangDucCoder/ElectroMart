<?php

namespace App\Exports;

use App\Models\Product;

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
