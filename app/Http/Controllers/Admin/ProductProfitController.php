<?php

namespace App\Http\Controllers\Admin;

use App\Application\Reporting\Services\ProductProfitReportService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductProfitController extends Controller
{
    public function __construct(
        private readonly ProductProfitReportService $reportService
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'brand_id' => array_values(array_filter([
                'nullable',
                'integer',
                Schema::hasTable('brands') ? 'exists:brands,id' : null,
            ])),
            'category_id' => array_values(array_filter([
                'nullable',
                'integer',
                Schema::hasTable('product_categories') ? 'exists:product_categories,id' : null,
            ])),
        ]);

        return view('admin.product_profit.index', array_merge(
            $this->reportService->getReport($filters, 20),
            ['filters' => $filters]
        ));
    }
}
