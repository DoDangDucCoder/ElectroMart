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
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
        $flashSale = $this->resolveFlashSaleSection();

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
                ->selectRaw(
                    'MIN(' . $this->effectivePriceExpression() . ') as min_price, MAX(' . $this->effectivePriceExpression() . ') as max_price'
                )
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
            'flash_sale' => $flashSale,
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
        $keyword = (string) ($request->validated('query') ?? '');
        $searchSuggest = $this->resolveSearchSuggestPayload($keyword);
        $maxProducts = max(1, (int) config('storefront.search_suggest.max_products', 6));
        $product = mb_strlen($keyword) >= 2
            ? $this->productService->searchSuggestions($keyword, $maxProducts)
            : collect();

        if (
            $product->isEmpty() &&
            empty($searchSuggest['trending_keywords']) &&
            empty($searchSuggest['shortcut_links']) &&
            empty($searchSuggest['utility_links']) &&
            blank($keyword)
        ) {
            return response('');
        }

        return response()->view('pages.home.partials.autocomplete_results', [
            'products' => $product,
            'searchSuggest' => $searchSuggest,
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

    private function resolveFlashSaleSection(): ?array
    {
        $config = config('storefront.flash_sale', []);

        if (! ($config['enabled'] ?? false)) {
            return null;
        }

        $productSlugs = collect($config['product_slugs'] ?? [])
            ->map(static fn ($slug) => trim((string) $slug))
            ->filter()
            ->values();

        if ($productSlugs->isEmpty()) {
            return null;
        }

        $productsBySlug = Product::query()
            ->with(['brand', 'primaryCategory'])
            ->where('is_active', true)
            ->whereIn('slug', $productSlugs->all())
            ->get()
            ->keyBy('slug');

        $orderedProducts = $productSlugs
            ->map(fn ($slug) => $productsBySlug->get($slug))
            ->filter()
            ->values();

        if ($orderedProducts->isEmpty()) {
            return null;
        }

        $soldQuantities = $this->resolveFlashSaleSoldQuantities($orderedProducts->pluck('id')->all());
        $hasSoldMetrics = $soldQuantities !== null;

        $cards = $orderedProducts
            ->map(function (Product $product) use ($soldQuantities, $hasSoldMetrics) {
                return $this->mapFlashSaleCard(
                    $product,
                    (int) ($soldQuantities[$product->id] ?? 0),
                    $hasSoldMetrics
                );
            })
            ->filter()
            ->values();

        if ($cards->isEmpty()) {
            return null;
        }

        $countdownEndsAt = $this->parseStorefrontDate($config['countdown_ends_at'] ?? null);

        return [
            'kicker' => trim((string) ($config['kicker'] ?? 'Hot deal')),
            'title' => trim((string) ($config['title'] ?? 'Flash Sale')),
            'cta_label' => trim((string) ($config['cta_label'] ?? 'Xem tất cả deal')),
            'cta_url' => $this->resolveStorefrontUrl($config['cta_url'] ?? '/tim-kiem', url('/tim-kiem')),
            'countdown_ends_at' => $countdownEndsAt?->toIso8601String(),
            'products' => $cards,
        ];
    }

    private function mapFlashSaleCard(Product $product, int $soldQuantity, bool $hasSoldMetrics): ?array
    {
        $originalPrice = (float) ($product->getRawOriginal('price') ?? $product->price ?? 0);
        $finalPrice = (float) ($product->final_price ?? $originalPrice);

        if ($finalPrice <= 0) {
            return null;
        }

        $discountPercent = $originalPrice > 0 && $finalPrice < $originalPrice
            ? (int) round((($originalPrice - $finalPrice) / $originalPrice) * 100)
            : 0;

        $stockQuantity = max(0, (int) ($product->stock_quantity ?? 0));
        $progressMax = max(1, $soldQuantity + $stockQuantity);

        $specs = collect([
            [
                'icon' => 'fa-industry',
                'label' => $product->brand_name,
            ],
            [
                'icon' => 'fa-folder-open-o',
                'label' => $product->category_name,
            ],
            [
                'icon' => $stockQuantity > 0 ? 'fa-cubes' : 'fa-bell-o',
                'label' => $stockQuantity > 0 ? 'Còn ' . $stockQuantity : 'Sắp cháy hàng',
            ],
        ])->filter(fn ($item) => filled($item['label']))->take(3)->values()->all();

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'url' => url('/chi-tiet/' . $product->slug),
            'image' => $this->resolvePublicAssetUrl($product->product_image),
            'price' => $originalPrice,
            'final_price' => $finalPrice,
            'discount_percent' => $discountPercent,
            'specs' => $specs,
            'sold_quantity' => $hasSoldMetrics ? $soldQuantity : null,
            'sold_progress' => $hasSoldMetrics ? (int) round(($soldQuantity / $progressMax) * 100) : null,
        ];
    }

    private function resolveFlashSaleSoldQuantities(array $productIds): ?Collection
    {
        if (
            $productIds === [] ||
            ! Schema::hasTable('orders') ||
            ! Schema::hasTable('order_items') ||
            ! Schema::hasColumn('orders', 'status') ||
            ! Schema::hasColumn('order_items', 'order_id') ||
            ! Schema::hasColumn('order_items', 'product_id') ||
            ! Schema::hasColumn('order_items', 'quantity')
        ) {
            return null;
        }

        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereIn('order_items.product_id', $productIds)
            ->groupBy('order_items.product_id')
            ->selectRaw('order_items.product_id, SUM(order_items.quantity) as sold_quantity')
            ->pluck('sold_quantity', 'order_items.product_id');
    }

    private function resolveSearchSuggestPayload(?string $query = null): array
    {
        $query = trim((string) $query);
        $config = config('storefront.search_suggest', []);
        $trendingKeywords = collect($config['trending_keywords'] ?? [])
            ->map(static fn ($keyword) => trim((string) $keyword))
            ->filter()
            ->take(6)
            ->values()
            ->all();

        return [
            'query' => $query,
            'search_results_url' => $this->buildSearchResultUrl($query),
            'trending_keywords' => $trendingKeywords,
            'shortcut_links' => $this->resolveSearchShortcutLinks($config['shortcut_category_slugs'] ?? []),
            'utility_links' => [
                [
                    'label' => 'Xem bài viết công nghệ',
                    'url' => url('/video-shop'),
                    'icon' => 'fa-newspaper-o',
                ],
                [
                    'label' => config('storefront.labels.recommend_for_you', 'Gợi ý cho bạn'),
                    'url' => url('/goi-y-san-pham'),
                    'icon' => 'fa-compass',
                ],
            ],
        ];
    }

    private function resolveSearchShortcutLinks(array $slugs): array
    {
        $categoryIcons = [
            'laptop' => 'fa-laptop',
            'laptop-gaming' => 'fa-gamepad',
            'laptop-van-phong' => 'fa-briefcase',
            'dien-thoai' => 'fa-mobile',
            'iphone' => 'fa-apple',
            'samsung-galaxy' => 'fa-android',
            'android-khac' => 'fa-android',
            'phu-kien' => 'fa-headphones',
            'sac-cap' => 'fa-plug',
            'op-lung-kinh-cuong-luc' => 'fa-shield',
        ];

        $normalizedSlugs = collect($slugs)
            ->map(static fn ($slug) => trim((string) $slug))
            ->filter()
            ->values();

        if ($normalizedSlugs->isEmpty()) {
            return [];
        }

        $categories = $this->categoryService->listActive()
            ->filter(fn ($item) => ! empty($item->slug_category_product))
            ->keyBy('slug_category_product');

        return $normalizedSlugs
            ->map(function (string $slug) use ($categories, $categoryIcons) {
                $category = $categories->get($slug);

                if (! $category) {
                    return null;
                }

                return [
                    'label' => \App\Support\StorefrontLabel::category(
                        $category->category_name,
                        $category->slug_category_product
                    ),
                    'url' => url('/danh-muc/' . $category->slug_category_product),
                    'icon' => $categoryIcons[$slug] ?? 'fa-tag',
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function resolveStorefrontUrl(?string $path, string $fallback): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url($path);
    }

    private function buildSearchResultUrl(string $query): string
    {
        if ($query === '') {
            return url('/tim-kiem');
        }

        return url('/tim-kiem?' . http_build_query([
            'keywords_submit' => $query,
        ]));
    }

    private function parseStorefrontDate(?string $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function resolvePublicAssetUrl(?string $path, string $fallback = 'uploads/product/no-image.svg'): string
    {
        if (filled($path) && file_exists(public_path($path))) {
            return asset($path);
        }

        return asset($fallback);
    }

    private function effectivePriceExpression(): string
    {
        return 'CAST(CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END AS REAL)';
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
