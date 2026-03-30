<?php

namespace App\Http\Controllers\Admin;

use App\Application\Reporting\Services\ProductProfitReportService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
        ]);

        return view('admin.product_profit.index', array_merge(
            $this->reportService->getReport($filters, 20),
            ['filters' => $filters]
        ));
    }
}
