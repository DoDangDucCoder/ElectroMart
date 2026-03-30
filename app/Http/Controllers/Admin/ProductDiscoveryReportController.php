<?php

namespace App\Http\Controllers\Admin;

use App\Application\Reporting\Services\ProductDiscoveryReportService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductDiscoveryReportController extends Controller
{
    public function __construct(
        private readonly ProductDiscoveryReportService $reportService
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        return view('admin.product_discovery.index', array_merge(
            $this->reportService->getReport($filters, 20),
            ['filters' => $filters]
        ));
    }
}
