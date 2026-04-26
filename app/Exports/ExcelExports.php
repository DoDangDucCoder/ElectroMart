<?php

namespace App\Exports;

use App\Models\CategoryProductModel;

class ExcelExports
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return CategoryProductModel::all();
    }
}
