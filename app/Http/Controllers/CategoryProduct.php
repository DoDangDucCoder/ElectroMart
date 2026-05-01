<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductService;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;
use App\Http\Requests\Storefront\Home\ProductTabsRequest;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Http\Request;

class CategoryProduct extends Controller
{
    use LegacyStorefrontContext;

    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly ProductService $productService,
    ) {}

    public function show_category_home(Request $request, string $slug)
    {
        $category = $this->categoryService->findActiveBySlug($slug);

        if (! $category) {
            abort(404);
        }

        $categoryPost = $this->legacyCategoryPosts();
        $slider = $this->legacyActiveSliders();

        $allCategories = $this->categoryService->listAll(['is_active' => true]);
        $brands = Brand::where('is_active', true)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get();

        $filters = $this->buildSortAndFilterOptions($request);
        $selectedSort = $this->selectedSortKey($request);

        $products = $this->productService->getProductsByCategorySlug($slug, $filters, 12);
        if (method_exists($products, 'appends')) {
            $products->appends($request->query());
        }

        $priceRange = $this->productService->getCategoryPriceRange($slug);

        $rangeMin = (float) ($priceRange['min'] ?? 0);
        $rangeMax = (float) ($priceRange['max'] ?? 0);
        $selectedMin = $request->filled('start_price') ? (float) $request->input('start_price') : $rangeMin;
        $selectedMax = $request->filled('end_price') ? (float) $request->input('end_price') : $rangeMax;

        $metaDesc = $category->category_desc;
        $metaKeywords = $category->meta_keywords;
        $metaTitle = $category->category_name;
        $canonicalUrl = $request->url();

        return view('pages.category.show_category', [
            'category' => $allCategories,
            'brand' => $brands,
            'category_by_id' => $products,
            'category_name' => collect([$category]),
            'meta_desc' => $metaDesc,
            'meta_keywords' => $metaKeywords,
            'meta_title' => $metaTitle,
            'url_canonical' => $canonicalUrl,
            'slider' => $slider,
            'category_post' => $categoryPost,
            'min_price' => $selectedMin,
            'max_price' => $selectedMax,
            'max_price_range' => $rangeMax,
            'min_price_range' => $rangeMin,
            'range_min_price' => $rangeMin,
            'range_max_price' => $rangeMax,
            'selected_sort' => $selectedSort,
            'filter_start_price' => $selectedMin,
            'filter_end_price' => $selectedMax,
            'price_buckets' => $this->buildPriceBuckets($rangeMin, $rangeMax, $slug),
        ]);
    }

    public function product_tabs(ProductTabsRequest $request)
    {
        $categoryId = (int) $request->validated('cate_id');
        $category = ProductCategory::where('is_active', true)->find($categoryId);

        if (! $category) {
            return response('');
        }

        $categoryIds = array_merge([
            $category->id,
        ], $this->categoryService->getDescendantIds($category));

        $products = $this->productService->getProductsByCategoryIds($categoryIds, 8);

        return response()->view('pages.category.partials.product_tab_content', [
            'products' => $products,
        ]);
    }

    protected function buildSortAndFilterOptions(Request $request): array
    {
        $filters = [];

        $sortMap = [
            'tang_dan' => 'price_asc',
            'giam_dan' => 'price_desc',
            'kytu_az' => 'name_asc',
            'kytu_za' => 'name_desc',
            'khuyen_mai' => 'sale_desc',
        ];

        $sortBy = $this->selectedSortKey($request);
        if ($sortBy && isset($sortMap[$sortBy])) {
            $filters['sort'] = $sortMap[$sortBy];
        }

        $hasMin = $request->filled('start_price');
        $hasMax = $request->filled('end_price');

        if ($hasMin || $hasMax) {
            $min = $hasMin ? (float) $request->input('start_price') : null;
            $max = $hasMax ? (float) $request->input('end_price') : null;

            if ($min !== null && $max !== null && $min > $max) {
                [$min, $max] = [$max, $min];
            }

            if ($min !== null) {
                $filters['price_min'] = $min;
            }

            if ($max !== null) {
                $filters['price_max'] = $max;
            }
        }

        return $filters;
    }

    protected function selectedSortKey(Request $request): string
    {
        $allowedSorts = ['none', 'tang_dan', 'giam_dan', 'kytu_az', 'kytu_za', 'khuyen_mai'];
        $sortBy = (string) $request->input('sort_by', 'none');

        return in_array($sortBy, $allowedSorts, true) ? $sortBy : 'none';
    }
}
