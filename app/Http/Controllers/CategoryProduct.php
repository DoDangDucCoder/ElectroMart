<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductService;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;
use App\Http\Requests\Storefront\Home\ProductTabsRequest;
use Illuminate\Http\Request;

class CategoryProduct extends Controller
{
    use LegacyStorefrontContext;

    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly ProductService $productService,
        private readonly BrandService $brandService,
    ) {}

    public function show_category_home(Request $request, string $slug)
    {
        $resolvedSlug = $this->categoryService->resolveStorefrontSlug($slug);
        $category = $this->categoryService->findActiveBySlug($resolvedSlug);

        if (! $category) {
            abort(404);
        }

        $categorySlug = $category->slug_category_product ?? $category->slug ?? $resolvedSlug;

        if ($slug !== $categorySlug) {
            return redirect()->to(url('/danh-muc/' . $categorySlug) . ($request->getQueryString() ? '?' . $request->getQueryString() : ''), 301);
        }

        $categoryPost = $this->legacyCategoryPosts();
        $slider = $this->legacyActiveSliders();

        $allCategories = $this->categoryService->listAll(['is_active' => true]);
        $brands = $this->brandService->listActive();

        $filters = $this->buildSortAndFilterOptions($request);
        $selectedSort = $this->selectedSortKey($request);

        $products = $this->productService->getProductsByCategorySlug($categorySlug, $filters, 12);
        if (method_exists($products, 'appends')) {
            $products->appends($request->query());
        }

        $priceRange = $this->productService->getCategoryPriceRange($categorySlug);

        $rangeMin = (float) ($priceRange['min'] ?? 0);
        $rangeMax = (float) ($priceRange['max'] ?? 0);
        $selectedMin = $request->filled('start_price') ? (float) $request->input('start_price') : $rangeMin;
        $selectedMax = $request->filled('end_price') ? (float) $request->input('end_price') : $rangeMax;

        $metaDesc = $category->category_desc;
        $metaKeywords = $category->meta_keywords;
        $metaTitle = trim((string) ($category->meta_title ?? '')) ?: $category->category_name;
        $canonicalUrl = $request->url();

        $presentation = $this->categoryPresentation($slug, $categorySlug);

        return view('pages.cat-x.listing', [
            'category' => $allCategories,
            'brand' => $brands,
            'category_by_id' => $products,
            'category_name' => collect([$category]),
            'meta_desc' => $metaDesc,
            'meta_keywords' => $metaKeywords,
            'meta_title' => $metaTitle,
            'activeCategoryHeading' => $category->category_name,
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
            'price_buckets' => $this->buildPriceBuckets($rangeMin, $rangeMax, $categorySlug),
            ...$presentation,
        ]);
    }

    protected function categoryPresentation(string $requestedSlug, string $categorySlug): array
    {
        $profiles = [
            'gaming' => [
                'bannerImage' => asset('assets/home/hero/hero-gaming-setup.svg'),
                'promoTitle' => 'GEAR GAMING GIÁ TỐT',
                'promoText' => 'Laptop, màn hình và phụ kiện tối ưu cho chơi game',
                'promoBadge' => 'GG',
                'promoBadgeLabel' => 'HIỆU NĂNG<br>ỔN ĐỊNH',
                'useNeeds' => [
                    ['label' => 'Laptop gaming', 'slug' => 'laptop-gaming'],
                    ['label' => 'PC - Console', 'slug' => 'pc-console'],
                    ['label' => 'Màn hình 144Hz+', 'slug' => 'man-hinh-144hz'],
                    ['label' => 'Phụ kiện gaming', 'slug' => 'phu-kien-gaming'],
                ],
            ],
            'laptop-gaming' => [
                'bannerImage' => asset('assets/home/hero/hero-gaming-setup.svg'),
                'promoTitle' => 'GEAR GAMING GIÁ TỐT',
                'promoText' => 'Laptop, màn hình và phụ kiện tối ưu cho chơi game',
                'promoBadge' => 'GG',
                'promoBadgeLabel' => 'HIỆU NĂNG<br>ỔN ĐỊNH',
                'useNeeds' => [
                    ['label' => 'Laptop gaming', 'slug' => 'laptop-gaming'],
                    ['label' => 'PC - Console', 'slug' => 'pc-console'],
                    ['label' => 'Màn hình 144Hz+', 'slug' => 'man-hinh-144hz'],
                    ['label' => 'Phụ kiện gaming', 'slug' => 'phu-kien-gaming'],
                ],
            ],
            'smartwatch' => [
                'bannerImage' => asset('uploads/products/sample-imported/WATCH-008/WATCH-008-xiaomi-watch-s4-main.jpg'),
                'promoTitle' => 'SMARTWATCH CHO MỌI NHU CẦU',
                'promoText' => 'Theo dõi sức khỏe, luyện tập và thông báo tiện lợi',
                'promoBadge' => 'SW',
                'promoBadgeLabel' => 'BẢO HÀNH<br>CHÍNH HÃNG',
                'useNeeds' => [
                    ['label' => 'Theo dõi sức khỏe', 'slug' => 'suc-khoe'],
                    ['label' => 'Thể thao - GPS', 'slug' => 'the-thao-gps'],
                    ['label' => 'Pin lâu', 'slug' => 'pin-lau'],
                    ['label' => 'Nghe gọi Bluetooth', 'slug' => 'nghe-goi'],
                ],
            ],
            'dong-ho-thong-minh' => [
                'bannerImage' => asset('uploads/products/sample-imported/WATCH-008/WATCH-008-xiaomi-watch-s4-main.jpg'),
                'promoTitle' => 'SMARTWATCH CHO MỌI NHU CẦU',
                'promoText' => 'Theo dõi sức khỏe, luyện tập và thông báo tiện lợi',
                'promoBadge' => 'SW',
                'promoBadgeLabel' => 'BẢO HÀNH<br>CHÍNH HÃNG',
                'useNeeds' => [
                    ['label' => 'Theo dõi sức khỏe', 'slug' => 'suc-khoe'],
                    ['label' => 'Thể thao - GPS', 'slug' => 'the-thao-gps'],
                    ['label' => 'Pin lâu', 'slug' => 'pin-lau'],
                    ['label' => 'Nghe gọi Bluetooth', 'slug' => 'nghe-goi'],
                ],
            ],
        ];

        return $profiles[$requestedSlug] ?? $profiles[$categorySlug] ?? [];
    }

    public function product_tabs(ProductTabsRequest $request)
    {
        $categoryId = (int) $request->validated('cate_id');
        $category = $this->categoryService->listActive()->firstWhere('id', $categoryId);

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
