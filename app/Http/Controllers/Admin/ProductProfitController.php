<?php

namespace App\Http\Controllers\Admin;

use App\Application\Reporting\Services\ProductProfitReportService;
use App\Http\Controllers\Controller;
use App\Support\CsvExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);
        $report = $this->reportService->getPrintableReport($filters);

        $rows = collect($report['rows'] ?? [])->map(static fn ($row): array => [
            $row->product_name,
            $row->product_slug ?: '',
            $row->category_name ?: 'Đang cập nhật',
            $row->brand_name ?: 'Đang cập nhật',
            (int) $row->sold_quantity,
            (float) $row->revenue,
            (float) $row->cost_total,
            (float) $row->profit,
        ]);

        return CsvExport::download('bao-cao-loi-nhuan-' . now()->format('Ymd-His') . '.csv', [
            'Sản phẩm',
            'Slug',
            'Danh mục',
            'Thương hiệu',
            'Số lượng đã bán',
            'Doanh thu',
            'Giá vốn',
            'Lợi nhuận',
        ], $rows);
    }

    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'sort' => ['nullable', 'string', 'in:revenue_desc,revenue_asc,profit_desc,profit_asc,sold_desc,sold_asc,cost_desc,cost_asc'],
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
