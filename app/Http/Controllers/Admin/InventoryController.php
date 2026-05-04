<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductVariant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    private const FALLBACK_THRESHOLD = 10;

    public function index(Request $request): View
    {
        $perPage = max(1, min((int) $request->integer('per_page', 15), 50));
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $categoryId = (int) $request->integer('category_id', 0);
        $brandId = (int) $request->integer('brand_id', 0);

        $query = Product::query()
            ->with(['brand', 'primaryCategory'])
            ->withCount('variants')
            ->withSum('variants as variant_stock_quantity', 'stock_quantity')
            ->orderByRaw('CASE WHEN stock_quantity <= 0 THEN 0 WHEN stock_quantity <= COALESCE(NULLIF(stock_alert_threshold, 0), ?) THEN 1 ELSE 2 END', [self::FALLBACK_THRESHOLD])
            ->orderByDesc('updated_at')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        if ($categoryId > 0) {
            $query->where('primary_category_id', $categoryId);
        }

        if ($brandId > 0) {
            $query->where('brand_id', $brandId);
        }

        $this->applyStatusFilter($query, $status);

        return view('admin.inventory.index', [
            'products' => $query->paginate($perPage)->withQueryString(),
            'stats' => $this->stats(),
            'filters' => [
                'q' => $search,
                'status' => $status,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'per_page' => $perPage,
            ],
            'categories' => ProductCategory::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'brands' => Brand::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'fallbackThreshold' => self::FALLBACK_THRESHOLD,
        ]);
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
