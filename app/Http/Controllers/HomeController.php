<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storefront\Home\AutocompleteProductsRequest;
use App\Http\Requests\Storefront\Home\LoadMoreProductsRequest;
use App\Http\Requests\Storefront\Home\SearchProductsRequest;
use Illuminate\Http\Request;
use App\Models\CatePost;
use App\Models\Slider;
use App\Models\Icons;
use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductDiscoveryTrackingService;
use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly ProductService $productService,
        private readonly BrandService $brandService,
        private readonly ProductDiscoveryTrackingService $discoveryTracking
    ) {
    }

    public function error_page()
    {
        return view('errors.404');
    }

    public function load_more_product(LoadMoreProductsRequest $request)
    {
        $lastId = $request->validated('id');
        $lastId = $lastId !== null ? (int) $lastId : null;
        $products = $this->productService->loadMoreProducts($lastId, 6);

        if ($products->isEmpty()) {
            return response()->view('pages.home.partials.load_more_products', [
                'products' => $products,
                'lastLoadedId' => null,
                'isExhausted' => true,
            ]);
        }

        return response()->view('pages.home.partials.load_more_products', [
            'products' => $products,
            'lastLoadedId' => $products->last()?->product_id,
            'isExhausted' => false,
        ]);
    }

    public function index(Request $request)
    {
        $icons = $this->legacyIcons();
        $category_post = $this->legacyCategoryPosts();
        $slider = $this->legacyActiveSliders();

        $meta_desc = "Chuyên bán những phụ kiện ,thiết bị game";
        $meta_keywords = "thiet bi game,phu kien game,game phu kien,game giai tri";
        $meta_title = "Laravel 11 web bán hàng";
        $url_canonical = $request->url();

        $cate_product = $this->categoryService->listActive();
        $brand_product = $this->brandService->listAll(['is_active' => true]);
        $all_product = $this->productService->getRandomProducts(6);
        $all_product->appends($request->query());
        $cate_pro_tabs = $this->categoryService->listRootActive();
        $recommendationData = $this->productService->getRecommendedProducts(
            (int) session('customer_id') ?: null,
            $request->ip(),
            null,
            4,
            'homepage'
        );
        $heroRatio = $this->calculateHeroRatio($slider);

        $categoriesByParent = $cate_product->groupBy(static function ($category) {
            return $category->category_parent ?? 0;
        });
        $rootCategories = $categoriesByParent->get(0, collect())->sortBy('category_order');
        $accessoryRoot = $cate_product->firstWhere('slug_category_product', 'phu-kien');
        $accessoryChildren = $accessoryRoot
            ? $categoriesByParent->get($accessoryRoot->category_id, collect())->sortBy('category_order')
            : collect();

        $megaMenuData = [];

        foreach ($rootCategories as $rootCategory) {
            $rootId = (int) $rootCategory->category_id;
            $descendantIds = array_merge([$rootId], $this->categoryService->getDescendantIds($rootCategory));

            $productQuery = Product::query()
                ->where('is_active', true)
                ->whereHas('categories', function ($query) use ($descendantIds) {
                    $query->whereIn('product_categories.id', $descendantIds);
                });

            $brandIds = (clone $productQuery)
                ->whereNotNull('brand_id')
                ->distinct()
                ->pluck('brand_id')
                ->map(static fn ($id) => (int) $id)
                ->all();

            $brands = $brand_product->whereIn('id', $brandIds)
                ->sortBy('display_order')
                ->take(8)
                ->map(static function ($brand) {
                    return [
                        'name' => $brand->brand_name,
                        'url' => $brand->brand_slug ? url('/thuong-hieu/' . $brand->brand_slug) : null,
                    ];
                })
                ->values()
                ->all();

            if (empty($brands)) {
                $brands = $brand_product->sortBy('display_order')
                    ->take(8)
                    ->map(static function ($brand) {
                        return [
                            'name' => $brand->brand_name,
                            'url' => $brand->brand_slug ? url('/thuong-hieu/' . $brand->brand_slug) : null,
                        ];
                    })
                    ->values()
                    ->all();
            }

            $priceRow = (clone $productQuery)
                ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                ->first();

            $priceBuckets = $this->buildPriceBuckets(
                (float) ($priceRow->min_price ?? 0),
                (float) ($priceRow->max_price ?? 0),
                $rootCategory->slug_category_product
            );

            $usage = $categoriesByParent->get($rootId, collect())
                ->sortBy('category_order')
                ->take(6)
                ->map(static function ($category) {
                    return [
                        'name' => $category->category_name,
                        'url' => url('/danh-muc/' . $category->slug_category_product),
                    ];
                })
                ->values()
                ->all();

            $accessories = $rootCategory->slug_category_product === 'phu-kien'
                ? []
                : $accessoryChildren->take(6)->map(static function ($category) {
                    return [
                        'name' => $category->category_name,
                        'url' => url('/danh-muc/' . $category->slug_category_product),
                    ];
                })->values()->all();

            $megaMenuData[$rootCategory->slug_category_product] = [
                'brands' => $brands,
                'prices' => $priceBuckets,
                'usage' => $usage,
                'accessories' => $accessories,
            ];
        }

        return view('home', [
            'category' => $cate_product,
            'brand' => $brand_product,
            'all_product' => $all_product,
            'meta_desc' => $meta_desc,
            'meta_keywords' => $meta_keywords,
            'meta_title' => $meta_title,
            'url_canonical' => $url_canonical,
            'slider' => $slider,
            'category_post' => $category_post,
            'cate_pro_tabs' => $cate_pro_tabs,
            'icons' => $icons,
            'mega_menu_data' => $megaMenuData,
            'hero_ratio' => $heroRatio,
            'recommended_products' => $recommendationData['products'],
            'recommendation_description' => $recommendationData['description'],
        ]);
    }

    public function yeu_thich(Request $request)
    {
        $category_post = $this->legacyCategoryPosts();
        $slider = $this->legacyActiveSliders();

        $meta_desc = "Yêu thích";
        $meta_keywords = "Yêu thích";
        $meta_title = "Yêu thích";
        $url_canonical = $request->url();

        $cate_product = $this->categoryService->listActive();
        $brand_product = $this->brandService->listAll(['is_active' => true]);
        $all_product = $this->productService->getRandomProducts(6);
        $all_product->appends($request->query());

        return view('pages.yeuthich.yeuthich', [
            'category' => $cate_product,
            'brand' => $brand_product,
            'all_product' => $all_product,
            'meta_desc' => $meta_desc,
            'meta_keywords' => $meta_keywords,
            'meta_title' => $meta_title,
            'url_canonical' => $url_canonical,
            'slider' => $slider,
            'category_post' => $category_post,
        ]);
    }

    public function search(SearchProductsRequest $request)
    {
        $category_post = $this->legacyCategoryPosts();
        $slider = $this->legacyActiveSliders();

        $meta_desc = "Tìm kiếm sản phẩm";
        $meta_keywords = "Tìm kiếm sản phẩm";
        $meta_title = "Tìm kiếm sản phẩm";
        $url_canonical = $request->url();
        $validated = $request->validated();
        $keywords = $validated['keywords_submit'] ?? '';
        $filters = [
            'keyword' => $keywords,
            'product_code' => $validated['product_code'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'price_min' => $validated['price_min'] ?? null,
            'price_max' => $validated['price_max'] ?? null,
            'sort' => $validated['sort'] ?? 'popular',
        ];

        $cate_product = $this->categoryService->listActive();
        $brand_product = $this->brandService->listAll(['is_active' => true]);
        $search_product = $this->productService->searchCatalog($filters, 12);
        $search_product->appends(array_filter($validated, static fn ($value) => $value !== null && $value !== ''));
        $recommendationData = $this->productService->getRecommendedProducts(
            (int) session('customer_id') ?: null,
            $request->ip(),
            null,
            4,
            'search_page'
        );

        if (! $request->has('page') || (int) $request->query('page', 1) === 1) {
            $this->discoveryTracking->trackSearch(
                $filters,
                method_exists($search_product, 'total') ? $search_product->total() : $search_product->count(),
                (int) session('customer_id') ?: null,
                $request->ip(),
                'web'
            );
        }

        return view('pages.sanpham.search', [
            'category' => $cate_product,
            'brand' => $brand_product,
            'search_product' => $search_product,
            'search_keyword' => $keywords,
            'search_filters' => $filters,
            'search_categories' => $cate_product,
            'recommended_products' => $recommendationData['products'],
            'recommendation_description' => $recommendationData['description'],
            'meta_desc' => $meta_desc,
            'meta_keywords' => $meta_keywords,
            'meta_title' => $meta_title,
            'url_canonical' => $url_canonical,
            'slider' => $slider,
            'category_post' => $category_post,
        ]);
    }

    public function autocomplete_ajax(AutocompleteProductsRequest $request)
    {
        $keyword = $request->validated('query');

        if (! $keyword) {
            return response('');
        }

        $product = $this->productService->searchSuggestions($keyword, 8);

        if ($product->isEmpty()) {
            return response('');
        }

        return response()->view('pages.home.partials.autocomplete_results', [
            'products' => $product,
        ]);
    }

    public function recommendations(Request $request)
    {
        $category_post = $this->legacyCategoryPosts();
        $slider = $this->legacyActiveSliders();
        $cate_product = $this->categoryService->listActive();
        $brand_product = $this->brandService->listAll(['is_active' => true]);
        $recommendationData = $this->productService->getRecommendedProducts(
            (int) session('customer_id') ?: null,
            $request->ip(),
            null,
            12,
            'recommendations_page'
        );

        return view('pages.sanpham.recommendations', [
            'category' => $cate_product,
            'brand' => $brand_product,
            'recommended_products' => $recommendationData['products'],
            'recommendation_description' => $recommendationData['description'],
            'recommendation_sources' => $recommendationData['sources'],
            'meta_desc' => 'Gợi ý sản phẩm',
            'meta_keywords' => 'goi y san pham, de xuat san pham',
            'meta_title' => 'Gợi ý sản phẩm',
            'url_canonical' => $request->url(),
            'slider' => $slider,
            'category_post' => $category_post,
        ]);
    }

    private function buildPriceBuckets(float $min, float $max, string $slug): array
    {
        if ($min <= 0 || $max <= 0) {
            return [];
        }

        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        if (abs($max - $min) < 1) {
            return [[
                'label' => $this->formatPriceMillions($min),
                'url' => url('/danh-muc/' . $slug),
            ]];
        }

        $roundTo = 1000000;
        $start = floor($min / $roundTo) * $roundTo;
        $end = ceil($max / $roundTo) * $roundTo;
        if ($end <= $start) {
            $end = $start + $roundTo;
        }

        $segments = 4;
        $step = (int) ceil(($end - $start) / $segments / $roundTo) * $roundTo;
        if ($step <= 0) {
            $step = $roundTo;
        }

        $buckets = [];
        $current = $start;

        for ($i = 0; $i < $segments; $i++) {
            $next = $i === $segments - 1 ? $end : min($end, $current + $step);

            if ($i === 0) {
                $label = 'Duoi ' . $this->formatPriceMillions($next);
                $minVal = null;
                $maxVal = $next;
            } elseif ($i === $segments - 1) {
                $label = 'Tren ' . $this->formatPriceMillions($current);
                $minVal = $current;
                $maxVal = null;
            } else {
                $label = 'Tu ' . $this->formatPriceMillions($current) . ' den ' . $this->formatPriceMillions($next);
                $minVal = $current;
                $maxVal = $next;
            }

            $query = [];
            if ($minVal !== null) {
                $query['start_price'] = (int) $minVal;
            }
            if ($maxVal !== null) {
                $query['end_price'] = (int) $maxVal;
            }

            $buckets[] = [
                'label' => $label,
                'url' => url('/danh-muc/' . $slug) . ($query ? ('?' . http_build_query($query)) : ''),
            ];

            $current = $next;
            if ($current >= $end) {
                break;
            }
        }

        return $buckets;
    }

    private function formatPriceMillions(float $price): string
    {
        if ($price <= 0) {
            return '0 trieu';
        }

        $millions = (int) round($price / 1000000);

        return $millions . ' trieu';
    }

    private function calculateHeroRatio($slider): ?float
    {
        $ratios = [];

        foreach ($slider as $slide) {
            $path = public_path('uploads/slider/' . $slide->slider_image);
            if (! file_exists($path)) {
                continue;
            }

            $size = @getimagesize($path);
            if (! $size || $size[1] <= 0) {
                continue;
            }

            $ratios[] = $size[0] / $size[1];
        }

        if (empty($ratios)) {
            return null;
        }

        sort($ratios);
        $count = count($ratios);
        $middle = (int) floor($count / 2);

        if ($count % 2 === 1) {
            $median = $ratios[$middle];
        } else {
            $median = ($ratios[$middle - 1] + $ratios[$middle]) / 2;
        }

        $median = max(1.8, min(2.05, $median));

        return round($median, 3);
    }

    private function legacyIcons()
    {
        if (! Schema::hasTable('tbl_icons')) {
            return collect();
        }

        return Icons::orderBy('id_icons', 'DESC')->get();
    }

    private function legacyCategoryPosts()
    {
        if (! Schema::hasTable('tbl_category_post')) {
            return collect();
        }

        return CatePost::orderBy('cate_post_id', 'DESC')->get();
    }

    private function legacyActiveSliders()
    {
        if (! Schema::hasTable('tbl_slider')) {
            return collect();
        }

        return Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();
    }
}
