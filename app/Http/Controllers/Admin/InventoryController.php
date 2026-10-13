<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductVariant;
use App\Support\CsvExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class InventoryController extends Controller
{
    private const FALLBACK_THRESHOLD = 10;

    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $query = $this->inventoryQuery($filters);

        return view('admin.inventory.index', [
            'products' => $query->paginate($filters['per_page'])->withQueryString(),
            'stats' => $this->stats(),
            'filters' => $filters,
            'categories' => ProductCategory::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'brands' => Brand::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'fallbackThreshold' => self::FALLBACK_THRESHOLD,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);
        $rows = $this->inventoryQuery($filters)
            ->limit(5000)
            ->get()
            ->map(function (Product $product): array {
                $stock = (int) ($product->stock_quantity ?? 0);
                $threshold = (int) ($product->stock_alert_threshold ?: self::FALLBACK_THRESHOLD);
                $costPrice = (float) ($product->cost_price ?? 0);
                $status = $stock <= 0
                    ? 'Hết hàng'
                    : ($stock <= $threshold ? 'Sắp hết' : 'Còn hàng');

                return [
                    $product->sku ?: '',
                    $product->name,
                    $product->primaryCategory?->name ?: '',
                    $product->brand?->name ?: '',
                    $stock,
                    $status,
                    $costPrice,
                    $product->updated_at?->format('d/m/Y H:i') ?? '',
                ];
            });

        return CsvExport::download('ton-kho-' . now()->format('Ymd-His') . '.csv', [
            'SKU',
            'Tên sản phẩm',
            'Danh mục',
            'Thương hiệu',
            'Số lượng tồn',
            'Trạng thái tồn',
            'Giá vốn',
            'Cập nhật',
        ], $rows);
    }

    private function filters(Request $request): array
    {
        return [
            'q' => trim((string) $request->query('q', '')),
            'status' => (string) $request->query('status', 'all'),
            'category_id' => (int) $request->integer('category_id', 0),
            'brand_id' => (int) $request->integer('brand_id', 0),
            'sort' => (string) $request->query('sort', 'stock_asc'),
            'per_page' => max(1, min((int) $request->integer('per_page', 15), 50)),
        ];
    }

    private function inventoryQuery(array $filters): Builder
    {
        $query = Product::query()
            ->with(['brand', 'primaryCategory'])
            ->withCount('variants')
            ->withSum('variants as variant_stock_quantity', 'stock_quantity');

        if ($filters['q'] !== '') {
            $query->where(function (Builder $builder) use ($filters): void {
                $builder->where('name', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('sku', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('barcode', 'like', '%' . $filters['q'] . '%');
            });
        }

        if ($filters['category_id'] > 0) {
            $query->where('primary_category_id', $filters['category_id']);
        }

        if ($filters['brand_id'] > 0) {
            $query->where('brand_id', $filters['brand_id']);
        }

        $this->applyStatusFilter($query, $filters['status']);

        match ($filters['sort']) {
            'stock_desc' => $query->orderByDesc('stock_quantity')->orderByDesc('id'),
            'value_desc' => $query->orderByRaw('(stock_quantity * COALESCE(cost_price, 0)) DESC')->orderByDesc('id'),
            'cost_desc' => $query->orderByDesc('cost_price')->orderByDesc('id'),
            'cost_asc' => $query->orderBy('cost_price')->orderByDesc('id'),
            'updated' => $query->orderByDesc('updated_at')->orderByDesc('id'),
            'updated_asc' => $query->orderBy('updated_at')->orderBy('id'),
            'name_asc' => $query->orderBy('name')->orderByDesc('id'),
            'name_desc' => $query->orderByDesc('name')->orderByDesc('id'),
            default => $query->orderBy('stock_quantity')->orderByDesc('id'),
        };

        return $query;
    }

    private function applyStatusFilter(Builder $query, string $status): void
    {
        if ($status === 'out_of_stock') {
            $query->where('stock_quantity', '<=', 0);

            return;
        }

        if ($status === 'low_stock') {
            $query->where('stock_quantity', '>', 0)
                ->whereRaw('stock_quantity <= COALESCE(NULLIF(stock_alert_threshold, 0), ?)', [self::FALLBACK_THRESHOLD]);

            return;
        }

        if ($status === 'in_stock') {
            $query->whereRaw('stock_quantity > COALESCE(NULLIF(stock_alert_threshold, 0), ?)', [self::FALLBACK_THRESHOLD]);
        }
    }

    private function stats(): array
    {
        $productStats = Product::query()
            ->selectRaw('COUNT(*) as total_products')
            ->selectRaw('COALESCE(SUM(stock_quantity), 0) as product_stock_quantity')
            ->selectRaw('COALESCE(SUM(stock_quantity * COALESCE(cost_price, 0)), 0) as product_inventory_value')
            ->selectRaw('SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock')
            ->selectRaw('SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= COALESCE(NULLIF(stock_alert_threshold, 0), ?) THEN 1 ELSE 0 END) as low_stock', [self::FALLBACK_THRESHOLD])
            ->first();

        $variantStats = ProductVariant::query()
            ->selectRaw('COUNT(*) as total_variants')
            ->selectRaw('COALESCE(SUM(stock_quantity), 0) as variant_stock_quantity')
            ->selectRaw('COALESCE(SUM(stock_quantity * COALESCE(cost_price, 0)), 0) as variant_inventory_value')
            ->first();

        $productSkuCount = Product::query()
            ->whereNotNull('sku')
            ->where('sku', '<>', '')
            ->count();

        $variantSkuCount = ProductVariant::query()
            ->whereNotNull('sku')
            ->where('sku', '<>', '')
            ->count();

        return [
            'total_products' => (int) ($productStats?->total_products ?? 0),
            'total_variants' => (int) ($variantStats?->total_variants ?? 0),
            'total_skus' => $productSkuCount + $variantSkuCount,
            'product_stock_quantity' => (int) ($productStats?->product_stock_quantity ?? 0),
            'variant_stock_quantity' => (int) ($variantStats?->variant_stock_quantity ?? 0),
            'total_stock_quantity' => (int) ($productStats?->product_stock_quantity ?? 0) + (int) ($variantStats?->variant_stock_quantity ?? 0),
            'low_stock' => (int) ($productStats?->low_stock ?? 0),
            'out_of_stock' => (int) ($productStats?->out_of_stock ?? 0),
            'total_inventory_value' => (float) ($productStats?->product_inventory_value ?? 0) + (float) ($variantStats?->variant_inventory_value ?? 0),
        ];
    }
}
