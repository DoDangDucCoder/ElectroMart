<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\CategoryService;
use App\Application\Cms\Services\BlogCategoryService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CompareController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly BlogCategoryService $blogCategoryService
    ) {
    }

    public function index(Request $request)
    {
        $requestedIds = collect(explode(',', (string) $request->query('ids')))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();
        $ids = $requestedIds->take(4)->values();

        $products = collect();
        if ($ids->isNotEmpty()) {
            $products = Product::with(['brand', 'primaryCategory', 'media'])
                ->where('is_active', true)
                ->whereIn('id', $ids->all())
                ->get();

            $products = $products->sortBy(fn ($product) => $ids->search($product->id))->values();
        }

        $missingCount = $ids->diff($products->pluck('id')->map(fn ($id) => (int) $id))->count();
        $trimmedCount = max(0, $requestedIds->count() - $ids->count());
        $compareNotice = null;

        if ($trimmedCount > 0) {
            $compareNotice = 'Bảng so sánh chỉ nhận tối đa 4 sản phẩm, các lựa chọn dư đã được bỏ qua.';
        } elseif ($missingCount > 0) {
            $compareNotice = 'Một số sản phẩm đã ngừng hiển thị hoặc không còn hợp lệ nên không được đưa vào bảng so sánh.';
        }

        if ($products->count() === 1) {
            $compareNotice = 'Vui lòng chọn ít nhất 2 sản phẩm hợp lệ để so sánh chi tiết.';
            $products = collect();
        }

        $specRows = $this->collectSpecificationRows($products);

        return view('pages.compare.index', [
            'products' => $products,
            'specRows' => $specRows,
            'specKeys' => array_column($specRows, 'key'),
            'compareNotice' => $compareNotice,
            'category' => $this->categoryService->listAll(['is_active' => true]),
            'category_post' => $this->blogCategoryService->listAll(['is_active' => true]),
            'meta_desc' => 'So sánh chi tiết sản phẩm',
            'meta_keywords' => 'so sánh, so sánh sản phẩm, thông số',
            'meta_title' => 'So sánh chi tiết sản phẩm',
            'url_canonical' => $request->url(),
            'share_image' => '',
        ]);
    }

    private function collectSpecificationRows(Collection $products): array
    {
        $rows = [];

        foreach ($products as $product) {
            foreach ($this->normalizedSpecifications($product) as $key => $spec) {
                if (! isset($rows[$key])) {
                    $rows[$key] = [
                        'key' => $key,
                        'label' => $spec['label'],
                        'values' => [],
                    ];
                }

                $rows[$key]['values'][$product->id] = $spec['value'];
            }
        }

        return array_values($rows);
    }

    private function normalizedSpecifications(Product $product): array
    {
        $meta = $product->meta ?? [];
        $specs = [];

        foreach ((array) data_get($meta, 'specifications', []) as $key => $value) {
            $label = trim((string) Str::of((string) $key)->replace(['_', '-'], ' ')->title());
            $normalizedKey = Str::slug($label, '_');

            if ($normalizedKey === '') {
                continue;
            }

            $specs[$normalizedKey] = [
                'label' => $label,
                'value' => $this->stringifySpecificationValue($value),
            ];
        }

        foreach ((array) data_get($meta, 'specs', []) as $spec) {
            if (! is_array($spec)) {
                continue;
            }

            $label = trim((string) ($spec['label'] ?? ''));
            $value = $this->stringifySpecificationValue($spec['value'] ?? null);

            if ($label === '' || $value === '') {
                continue;
            }

            $normalizedKey = Str::slug($label, '_');
            if ($normalizedKey === '') {
                continue;
            }

            $specs[$normalizedKey] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $specs;
    }

    private function stringifySpecificationValue(mixed $value): string
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($entry) => is_scalar($entry) ? trim((string) $entry) : '')
                ->filter()
                ->implode(', ');
        }

        return is_scalar($value) ? trim((string) $value) : '';
    }
}
