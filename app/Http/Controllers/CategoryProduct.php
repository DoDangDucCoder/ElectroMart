<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductService;
use App\Http\Requests\Storefront\Home\ProductTabsRequest;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Models\CatePost;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CategoryProduct extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly ProductService $productService,
    ) {
    }

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
            'max_price_range' => $rangeMax > 0 ? $rangeMax + 10000000 : 0,
            'min_price_range' => $rangeMin > 0 ? $rangeMin + 500000 : 0,
            'range_min_price' => $rangeMin,
            'range_max_price' => $rangeMax,
            'selected_sort' => $request->input('sort_by', 'none'),
            'filter_start_price' => $request->input('start_price'),
            'filter_end_price' => $request->input('end_price'),
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
        ];

        $sortBy = $request->input('sort_by');
        if ($sortBy && isset($sortMap[$sortBy])) {
            $filters['sort'] = $sortMap[$sortBy];
        }

        if ($request->filled('start_price') && $request->filled('end_price')) {
            $min = (float) $request->input('start_price');
            $max = (float) $request->input('end_price');

            if ($min > $max) {
                [$min, $max] = [$max, $min];
            }

            $filters['price_min'] = $min;
            $filters['price_max'] = $max;
        }

        return $filters;
    }

    protected function legacyCategoryPosts()
    {
        if (! Schema::hasTable('tbl_category_post')) {
            return collect();
        }

        return CatePost::orderBy('cate_post_id', 'DESC')->get();
    }

    protected function legacyActiveSliders()
    {
        if (! Schema::hasTable('tbl_slider')) {
            return collect();
        }

        return Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();
    }
}
