<?php

namespace App\Http\Controllers\Admin;

use App\Application\Reporting\Services\ProductProfitReportService;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
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
        $filters = $this->validatedFilters($request);

        return view('admin.product_profit.index', array_merge(
            $this->reportService->getReport($filters, 20),
            ['filters' => $filters]
        ));
    }

    public function print(Request $request)
    {
        $filters = $this->validatedFilters($request);

        return view('admin.product_profit.print', array_merge(
            $this->reportService->getPrintableReport($filters),
            [
                'filters' => $filters,
                'printedAt' => Carbon::now(),
            ]
        ));
    }

    private function validatedFilters(Request $request): array
    {
        return $request->validate([
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
    }
}
