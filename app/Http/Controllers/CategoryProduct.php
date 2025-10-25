<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Models\CatePost;
use App\Models\Slider;
use Illuminate\Http\Request;

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

        $categoryPost = CatePost::orderBy('cate_post_id', 'DESC')->get();
        $slider = Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();

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

        $minPrice = $priceRange['min'] ?? 0;
        $maxPrice = $priceRange['max'] ?? 0;

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
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'max_price_range' => $maxPrice > 0 ? $maxPrice + 10000000 : 0,
            'min_price_range' => $minPrice > 0 ? $minPrice + 500000 : 0,
            'selected_sort' => $request->input('sort_by', 'none'),
            'filter_start_price' => $request->input('start_price'),
            'filter_end_price' => $request->input('end_price'),
        ]);
    }

    public function product_tabs(Request $request)
    {
        $categoryId = (int) $request->input('cate_id');
        $category = ProductCategory::where('is_active', true)->find($categoryId);

        if (! $category) {
            return response('');
        }

        $categoryIds = array_merge([
            $category->id,
        ], $this->categoryService->getDescendantIds($category));

        $products = $this->productService->getProductsByCategoryIds($categoryIds, 8);

        $html = view('pages.category.partials.product_tab_content', [
            'products' => $products,
        ])->render();

        return response($html);
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
            $filters['price_min'] = (float) $request->input('start_price');
            $filters['price_max'] = (float) $request->input('end_price');
        }

        return $filters;
    }
}
