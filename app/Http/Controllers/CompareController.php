<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\CategoryService;
use App\Application\Cms\Services\BlogCategoryService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CompareController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly BlogCategoryService $blogCategoryService
    ) {
    }

    public function index(Request $request)
    {
        $ids = collect(explode(',', (string) $request->query('ids')))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $products = collect();
        if ($ids->isNotEmpty()) {
            $products = Product::with(['brand', 'primaryCategory', 'media'])
                ->whereIn('id', $ids->all())
                ->get();

            $products = $products->sortBy(fn ($product) => $ids->search($product->id))->values();
        }

        $specKeys = $this->collectSpecificationKeys($products);

        return view('pages.compare.index', [
            'products' => $products,
            'specKeys' => $specKeys,
            'category' => $this->categoryService->listAll(['is_active' => true]),
            'category_post' => $this->blogCategoryService->listAll(['is_active' => true]),
            'meta_desc' => 'So sanh chi tiet san pham',
            'meta_keywords' => 'so sanh, so sanh san pham, thong so',
            'meta_title' => 'So sanh chi tiet san pham',
            'url_canonical' => $request->url(),
            'share_image' => '',
        ]);
    }

    private function collectSpecificationKeys(Collection $products): array
    {
        $keys = [];

        foreach ($products as $product) {
            $specs = $product->meta['specifications'] ?? [];
            foreach (array_keys((array) $specs) as $key) {
                $keys[$key] = true;
            }
        }

        return array_keys($keys);
    }
}
