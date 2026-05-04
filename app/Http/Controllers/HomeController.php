<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductDiscoveryTrackingService;
use App\Application\Catalog\Services\ProductService;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;
use App\Http\Requests\Storefront\Home\AutocompleteProductsRequest;
use App\Http\Requests\Storefront\Home\LoadMoreProductsRequest;
use App\Http\Requests\Storefront\Home\SearchProductsRequest;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogPost;
use App\Infrastructure\Persistence\Eloquent\Cms\SiteLink;
use App\Models\News;
use App\Support\AssetUrl;
use App\Support\ProductPriceSql;
use App\Support\StorefrontLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    use LegacyStorefrontContext;

    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly ProductService $productService,
        private readonly BrandService $brandService,
        private readonly ProductDiscoveryTrackingService $discoveryTracking
    ) {}

    public function error_page()
    {
        return view('errors.404');
    }

    private function allowsDemoFallbacks(): bool
    {
        return app()->environment(['local', 'testing']);
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
        return view('home', [
            'category' => $this->categoryService->listActive(),
            'brand' => $this->brandService->listAll(['is_active' => true]),
            'category_post' => $this->legacyCategoryPosts(),
            'meta_desc' => 'Cửa hàng công nghệ chuyên laptop, điện thoại và phụ kiện chính hãng.',
            'meta_keywords' => 'laptop, dien thoai, iphone, samsung, macbook, flash sale cong nghe',
            'meta_title' => 'DangDuc - Laptop, điện thoại, phụ kiện',
            'url_canonical' => $request->url(),
            'slider' => $this->legacyActiveSliders(5),
            'featuredProducts' => $this->resolveTechHomeProducts(8, preferHot: true),
            'flashSaleProducts' => $this->resolveTechHomeProducts(10, preferHot: true, onlyDiscounted: true),
            'newsItems' => $this->resolveTechHomeNews(3),
            'brandItems' => $this->resolveTechHomeBrands(8),
            'policyItems' => $this->resolveTechHomePolicyItems(),
            'paymentItems' => $this->resolveTechHomePaymentItems(),
        ]);
    }

    public function khuyenMai(Request $request)
    {
        return view('pages.khuyenmai.index', [
            'category' => $this->categoryService->listActive(),
            'brand' => $this->brandService->listAll(['is_active' => true]),
            'category_post' => $this->legacyCategoryPosts(),
            'meta_desc' => 'Bộ sưu tập & Khuyến mãi HOT - Laptop, PC & Phụ kiện chính hãng giá tốt, ưu đãi mỗi ngày.',
            'meta_keywords' => 'khuyen mai, bo suu tap, flash sale, giam gia, laptop, phu kien, coupon',
            'meta_title' => 'Bộ sưu tập & Khuyến mãi HOT - DangDuc',
            'url_canonical' => $request->url(),
            'flashSaleProducts' => $this->resolveTechHomeProducts(10, preferHot: true, onlyDiscounted: true),
            'promoProducts' => $this->resolveTechHomeProducts(7, preferHot: true, onlyDiscounted: true),
            'policyItems' => $this->resolveTechHomePolicyItems(),
            'paymentItems' => $this->resolveTechHomePaymentItems(),
        ]);
    }

    public function orderTracking(Request $request)
    {
        $trackingResult = null;
        $code = $request->query('code');

        if ($code && $this->allowsDemoFallbacks()) {
            $trackingResult = [
                'code' => $code,
                'status_label' => 'Đang giao hàng',
                'date' => '20/05/2025 10:30',
                'payment_method' => 'Thanh toán khi nhận hàng',
                'total' => 23457000,
                'estimated_delivery' => '22/05/2025',
                'timeline' => [
                    ['label' => 'Đã đặt hàng', 'icon' => 'fa-check', 'done' => true, 'date' => '20/05/2025 10:30'],
                    ['label' => 'Đã xác nhận', 'icon' => 'fa-check', 'done' => true, 'date' => '20/05/2025 10:45'],
                    ['label' => 'Đang chuẩn bị', 'icon' => 'fa-check', 'done' => true, 'date' => '20/05/2025 11:20'],
                    ['label' => 'Đang giao', 'icon' => 'fa-truck', 'done' => true, 'active' => true, 'date' => '21/05/2025 09:15'],
                    ['label' => 'Hoàn tất', 'icon' => 'fa-flag-checkered', 'done' => false, 'date' => ''],
                ],
                'shipping' => ['name' => 'Nguyễn Văn An', 'phone' => '0901 234 567', 'address' => '123 Đường Cầu Giấy, Phường Quan Hoa, Quận Cầu Giấy, Hà Nội'],
                'carrier' => ['name' => 'GHN - Giao Hàng Nhanh', 'tracking_code' => 'GHN1234567890', 'last_update' => '21/05/2025 09:15'],
                'totals' => ['subtotal' => 22670000, 'shipping' => 30000, 'discount' => 1243000, 'total' => 23457000],
                'items' => [
                    ['name' => 'Laptop ASUS TUF Gaming A15 FA506NC-HN005W', 'image' => 'uploads/product/no-image.svg', 'quantity' => 1, 'price' => 19990000, 'subtotal' => 19990000],
                    ['name' => 'Chuột không dây Logitech G304 Lightspeed', 'image' => 'uploads/product/no-image.svg', 'quantity' => 1, 'price' => 890000, 'subtotal' => 890000],
                    ['name' => 'Bàn phím cơ AKKO 5075B Plus Black Silver', 'image' => 'uploads/product/no-image.svg', 'quantity' => 1, 'price' => 1790000, 'subtotal' => 1790000],
                ],
            ];
        }

        return view('pages.support.order_tracking', [
            'trackingResult' => $trackingResult,
            'meta_title' => 'Kiểm tra đơn hàng - DangDuc',
            'meta_desc' => 'Nhập mã đơn hàng để kiểm tra trạng thái đơn hàng tại DangDuc.',
            'meta_keywords' => 'kiem tra don hang, theo doi don hang, order tracking',
            'url_canonical' => $request->url(),
            'category' => $this->categoryService->listActive(),
            'brand' => $this->brandService->listAll(['is_active' => true]),
            'category_post' => $this->legacyCategoryPosts(),
            'slider' => $this->legacyActiveSliders(),
        ]);
    }

    private function resolveTechHomeProducts(int $limit = 8, bool $preferHot = false, bool $onlyDiscounted = false): Collection
    {
        $productTable = (new Product)->getTable();

        if (Schema::hasTable($productTable)) {
            try {
                $hasIsHotColumn = Schema::hasColumn($productTable, 'is_hot');
                $hasIsFeaturedColumn = Schema::hasColumn($productTable, 'is_featured');
                $hasDisplayOrderColumn = Schema::hasColumn($productTable, 'display_order');
                $hasSalePriceColumn = Schema::hasColumn($productTable, 'sale_price');
                $hasSaleValueColumn = Schema::hasColumn($productTable, 'sale_value');
                $hasPriceColumn = Schema::hasColumn($productTable, 'price');

                $query = Product::query()
                    ->with('brand')
                    ->where('is_active', true);

                if ($onlyDiscounted && $hasPriceColumn && ($hasSalePriceColumn || $hasSaleValueColumn)) {
                    $query->where(function ($discountQuery) use ($hasSalePriceColumn, $hasSaleValueColumn) {
                        if ($hasSalePriceColumn) {
                            $discountQuery->where(function ($salePriceQuery) {
                                $salePriceQuery->whereNotNull('sale_price')
                                    ->where('sale_price', '>', 0)
                                    ->whereColumn('sale_price', '<', 'price');
                            });
                        }

                        if ($hasSaleValueColumn) {
                            $method = $hasSalePriceColumn ? 'orWhere' : 'where';
                            $discountQuery->{$method}(function ($saleValueQuery) {
                                $saleValueQuery->whereNotNull('sale_value')
                                    ->where('sale_value', '>', 0);
                            });
                        }
                    });
                }

                if ($preferHot && $hasIsHotColumn) {
                    $query->orderByDesc('is_hot');
                }

                if ($hasIsFeaturedColumn) {
                    $query->orderByDesc('is_featured');
                }

                if ($hasDisplayOrderColumn) {
                    $query->orderBy('display_order');
                }

                $products = $query
                    ->orderByDesc('views_count')
                    ->orderByDesc('id')
                    ->limit($limit * 3)
                    ->get()
                    ->map(fn (Product $product) => $this->mapTechHomeProduct($product));

                if ($onlyDiscounted) {
                    $products = $products->filter(fn (array $product) => $product['discount_percent'] > 0)->values();
                }

                if ($products->isNotEmpty()) {
                    return $products->take($limit)->values();
                }
            } catch (\Throwable) {
                // Keep non-production previews available while optional catalog data is incomplete.
            }
        }

        if (! $this->allowsDemoFallbacks()) {
            return collect();
        }

        return $this->fallbackTechHomeProducts($limit, $onlyDiscounted);
    }

    private function mapTechHomeProduct(Product $product): array
    {
        $originalPrice = (float) ($product->getRawOriginal('price') ?? $product->price ?? 0);
        $finalPrice = (float) ($product->final_price ?? $product->sale_price ?? $originalPrice);

        if ($finalPrice <= 0) {
            $finalPrice = $originalPrice;
        }

        if ($originalPrice > 0 && $finalPrice > $originalPrice) {
            $finalPrice = $originalPrice;
        }

        $discountPercent = $originalPrice > 0 && $finalPrice < $originalPrice
            ? (int) round((($originalPrice - $finalPrice) / $originalPrice) * 100)
            : 0;
        $brandName = StorefrontLabel::neutralizeSource($product->brand?->brand_name ?? $product->brand?->name ?? 'DangDuc');

        return [
            'id' => $product->id,
            'name' => StorefrontLabel::neutralizeSource($product->name),
            'url' => filled($product->slug) ? url('/chi-tiet/'.$product->slug) : url('/tim-kiem'),
            'brand' => $brandName,
            'brand_logo' => $this->resolveTechHomeAssetUrl($product->brand?->logo_path),
            'brand_initials' => $this->brandInitials($brandName),
            'image' => $this->resolveTechHomeAssetUrl($product->thumbnail_path, 'uploads/product/no-image.svg'),
            'original_price' => $originalPrice,
            'price' => $finalPrice,
            'discount_percent' => $discountPercent,
            'is_hot' => (bool) ($product->is_hot ?? false),
        ];
    }

    private function fallbackTechHomeProducts(int $limit, bool $onlyDiscounted = false): Collection
    {
        $samples = collect([
            [
                'name' => 'MacBook Air M3 13 inch 16GB/512GB',
                'brand' => 'Apple',
                'query' => 'MacBook Air M3',
                'original_price' => 32990000,
                'price' => 29990000,
                'discount_percent' => 9,
                'is_hot' => true,
            ],
            [
                'name' => 'iPhone 15 Pro Max 256GB',
                'brand' => 'Apple',
                'query' => 'iPhone 15 Pro Max',
                'original_price' => 34990000,
                'price' => 30990000,
                'discount_percent' => 11,
                'is_hot' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra 512GB',
                'brand' => 'Samsung',
                'query' => 'Samsung Galaxy S24 Ultra',
                'original_price' => 32990000,
                'price' => 28990000,
                'discount_percent' => 12,
                'is_hot' => true,
            ],
            [
                'name' => 'ASUS Vivobook 15 OLED Ryzen 7',
                'brand' => 'ASUS',
                'query' => 'ASUS Vivobook 15 OLED',
                'original_price' => 18990000,
                'price' => 16490000,
                'discount_percent' => 13,
                'is_hot' => false,
            ],
            [
                'name' => 'Dell Inspiron 14 Plus Core Ultra 7',
                'brand' => 'Dell',
                'query' => 'Dell Inspiron 14 Plus',
                'original_price' => 24990000,
                'price' => 21990000,
                'discount_percent' => 12,
                'is_hot' => false,
            ],
            [
                'name' => 'Xiaomi 14T Pro 12GB/512GB',
                'brand' => 'Xiaomi',
                'query' => 'Xiaomi 14T Pro',
                'original_price' => 16990000,
                'price' => 14990000,
                'discount_percent' => 12,
                'is_hot' => false,
            ],
        ]);

        return $samples
            ->filter(fn (array $sample) => ! $onlyDiscounted || $sample['discount_percent'] > 0)
            ->map(function (array $sample, int $index) {
                return [
                    'id' => 'demo-'.$index,
                    'name' => $sample['name'],
                    'url' => url('/tim-kiem?'.http_build_query(['keywords_submit' => $sample['query']])),
                    'brand' => $sample['brand'],
                    'brand_logo' => null,
                    'brand_initials' => $this->brandInitials($sample['brand']),
                    'image' => $this->resolveTechHomeAssetUrl(null, 'uploads/product/no-image.svg'),
                    'original_price' => $sample['original_price'],
                    'price' => $sample['price'],
                    'discount_percent' => $sample['discount_percent'],
                    'is_hot' => $sample['is_hot'],
                ];
            })
            ->take($limit)
            ->values();
    }

    private function resolveTechHomeNews(int $limit = 3): Collection
    {
        if (Schema::hasTable('news')) {
            try {
                $newsItems = News::query()
                    ->where('is_active', true)
                    ->orderByDesc('is_featured')
                    ->orderByRaw('CASE WHEN published_at IS NULL THEN 1 ELSE 0 END ASC')
                    ->orderByDesc('published_at')
                    ->orderByDesc('id')
                    ->limit($limit)
                    ->get()
                    ->map(fn (News $news) => [
                        'title' => $news->title,
                        'url' => '#',
                        'summary' => $news->summary,
                        'published_at' => $news->published_at,
                        'thumbnail' => $this->resolveTechHomeAssetUrl($news->thumbnail_path, 'uploads/contact/blog-three41.jpg'),
                    ]);

                if ($newsItems->isNotEmpty()) {
                    return $newsItems;
                }
            } catch (\Throwable) {
                // Fall through to local preview news only.
            }
        }

        if (! $this->allowsDemoFallbacks()) {
            return collect();
        }

        return collect([
            [
                'title' => '5 mẫu laptop mỏng nhẹ đáng mua cho sinh viên và dân văn phòng',
                'url' => '#',
                'summary' => 'Gợi ý cấu hình cân bằng giữa hiệu năng, pin và mức giá cho nhu cầu học tập, làm việc hằng ngày.',
                'published_at' => now()->subDays(1),
                'thumbnail' => $this->resolveTechHomeAssetUrl('uploads/post/smartphone12.jpg', 'uploads/contact/blog-three41.jpg'),
            ],
            [
                'title' => 'So sánh iPhone và Galaxy: chọn flagship nào trong năm nay?',
                'url' => '#',
                'summary' => 'Những khác biệt quan trọng về camera, màn hình, pin và hệ sinh thái trước khi nâng cấp điện thoại.',
                'published_at' => now()->subDays(3),
                'thumbnail' => $this->resolveTechHomeAssetUrl('uploads/post/smartphone24.jpg', 'uploads/contact/blog-three41.jpg'),
            ],
            [
                'title' => 'Cách săn ưu đãi công nghệ an toàn trong mùa khuyến mãi',
                'url' => '#',
                'summary' => 'Kiểm tra giá gốc, bảo hành và phương thức thanh toán để mua đúng sản phẩm cần thiết.',
                'published_at' => now()->subDays(5),
                'thumbnail' => $this->resolveTechHomeAssetUrl('uploads/post/smartphone31.jpg', 'uploads/contact/blog-three41.jpg'),
            ],
        ])->take($limit)->values();
    }

    private function resolveTechHomeBrands(int $limit = 8): Collection
    {
        if (Schema::hasTable((new Brand)->getTable())) {
            try {
                $brands = Brand::query()
                    ->where('is_active', true)
                    ->orderBy('display_order')
                    ->orderBy('name')
                    ->limit($limit)
                    ->get()
                    ->map(fn (Brand $brand) => [
                        'name' => StorefrontLabel::neutralizeSource($brand->brand_name ?? $brand->name),
                        'url' => filled($brand->brand_slug ?? $brand->slug) ? url('/thuong-hieu/'.($brand->brand_slug ?? $brand->slug)) : '#',
                        'logo' => $this->resolveTechHomeAssetUrl($brand->logo_path),
                        'initials' => $this->brandInitials(StorefrontLabel::neutralizeSource($brand->brand_name ?? $brand->name)),
                    ]);

                if ($brands->isNotEmpty()) {
                    return $brands;
                }
            } catch (\Throwable) {
                // Fall through to local preview brands only.
            }
        }

        if (! $this->allowsDemoFallbacks()) {
            return collect();
        }

        return collect(['Apple', 'Samsung', 'ASUS', 'Dell', 'Lenovo', 'Xiaomi', 'OPPO', 'Acer'])
            ->take($limit)
            ->map(fn (string $name) => [
                'name' => $name,
                'url' => url('/thuong-hieu/'.Str::slug($name)),
                'logo' => null,
                'initials' => $this->brandInitials($name),
            ])
            ->values();
    }

    private function resolveTechHomePolicyItems(): Collection
    {
        return collect([
            [
                'icon' => 'fa-truck',
                'title' => 'Miễn phí giao hàng',
                'description' => 'Áp dụng cho đơn hàng công nghệ đủ điều kiện trong nội thành.',
            ],
            [
                'icon' => 'fa-refresh',
                'title' => 'Đổi trả hàng',
                'description' => 'Hỗ trợ đổi trả minh bạch theo chính sách từng nhóm sản phẩm.',
            ],
            [
                'icon' => 'fa-shield',
                'title' => 'Thanh toán an toàn',
                'description' => 'Bảo vệ thông tin thanh toán và xác nhận đơn hàng rõ ràng.',
            ],
            [
                'icon' => 'fa-headphones',
                'title' => 'Hỗ trợ tư vấn 24/7',
                'description' => 'Tư vấn chọn laptop, điện thoại và phụ kiện phù hợp nhu cầu.',
            ],
        ]);
    }

    private function resolveTechHomePaymentItems(): Collection
    {
        return collect(['Momo', 'MB', 'VNPAY']);
    }

    private function resolveTechHomeAssetUrl(?string $path, ?string $fallback = null): ?string
    {
        return AssetUrl::resolve($path, $fallback);
    }

    private function brandInitials(?string $brandName): string
    {
        $brandName = trim((string) $brandName);

        if ($brandName === '') {
            return 'SW';
        }

        return Str::upper(Str::substr($brandName, 0, 2));
    }

    public function yeu_thich(Request $request)
    {
        $category_post = $this->legacyCategoryPosts();
        $slider = $this->legacyActiveSliders();

        $meta_desc = 'Yêu thích';
        $meta_keywords = 'Yêu thích';
        $meta_title = 'Yêu thích';
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
        return $this->renderCatalogListingPage($request, [
            'meta_desc' => 'Tìm kiếm sản phẩm',
            'meta_keywords' => 'Tìm kiếm sản phẩm',
            'meta_title' => 'Tìm kiếm sản phẩm',
            'default_sort' => 'popular',
            'search_page_kicker' => 'Tìm kiếm sản phẩm',
            'search_page_title' => 'Kết quả tìm kiếm',
            'search_form_action' => url('/tim-kiem'),
            'search_clear_url' => url('/tim-kiem'),
            'search_results_kicker' => 'Danh sách kết quả',
            'tracking_context' => 'search_page',
        ]);
    }

    public function products(SearchProductsRequest $request)
    {
        return $this->renderCatalogListingPage($request, [
            'meta_desc' => 'Danh sách sản phẩm - Laptop, điện thoại, phụ kiện công nghệ chính hãng',
            'meta_keywords' => 'sản phẩm, laptop, điện thoại, phụ kiện công nghệ',
            'meta_title' => 'Sản phẩm - Shop công nghệ',
            'default_sort' => 'popular',
            'search_page_mode' => 'all_products',
            'search_page_kicker' => 'Danh mục sản phẩm',
            'search_page_title' => 'Tất cả sản phẩm',
            'search_form_action' => url('/san-pham'),
            'search_clear_url' => url('/san-pham'),
            'search_results_kicker' => 'Danh sách sản phẩm',
            'tracking_context' => 'products_page',
        ]);
    }

    public function best_selling(SearchProductsRequest $request)
    {
        return $this->renderCatalogListingPage($request, [
            'meta_desc' => 'Sản phẩm đang bán chạy',
            'meta_keywords' => 'sản phẩm bán chạy, top sản phẩm bán chạy',
            'meta_title' => 'Sản phẩm đang bán chạy',
            'default_sort' => 'best_selling',
            'search_page_mode' => 'best_selling',
            'search_page_kicker' => 'Sản phẩm đang bán chạy',
            'search_page_title' => 'Top sản phẩm bán chạy',
            'search_form_action' => url('/san-pham-ban-chay'),
            'search_clear_url' => url('/san-pham-ban-chay'),
            'search_results_kicker' => 'Bảng xếp hạng bán chạy',
            'tracking_context' => 'best_selling_page',
        ]);
    }

    private function renderCatalogListingPage(SearchProductsRequest $request, array $overrides = [])
    {
        $category_post = $this->legacyCategoryPosts();
        $slider = $this->legacyActiveSliders();
        $url_canonical = $request->url();
        $validated = $request->validated();
        $keywords = $validated['keywords_submit'] ?? '';
        $defaultSort = $overrides['default_sort'] ?? 'popular';
        $highlightProductSlug = trim((string) ($validated['highlight_product'] ?? ''));
        $highlightProduct = $highlightProductSlug !== ''
            ? $this->productService->findProductBySlug($highlightProductSlug)
            : null;

        $filters = [
            'keyword' => $keywords,
            'product_code' => $validated['product_code'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'brand_id' => $validated['brand_id'] ?? null,
            'price_min' => $validated['price_min'] ?? null,
            'price_max' => $validated['price_max'] ?? null,
            'sort' => $validated['sort'] ?? $defaultSort,
            'highlight_product' => $highlightProduct?->slug,
            'highlight_product_slug' => $highlightProduct?->slug,
        ];

        $cate_product = $this->categoryService->listActive();
        $brand_product = $this->brandService->listAll(['is_active' => true]);
        $search_product = $this->productService->searchCatalog($filters, 12);
        $search_product->appends(array_filter([
            'keywords_submit' => $validated['keywords_submit'] ?? null,
            'product_code' => $validated['product_code'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'brand_id' => $validated['brand_id'] ?? null,
            'price_min' => $validated['price_min'] ?? null,
            'price_max' => $validated['price_max'] ?? null,
            'sort' => $validated['sort'] ?? null,
            'highlight_product' => $highlightProduct?->slug,
        ], static fn ($value) => $value !== null && $value !== ''));

        $trackingContext = trim((string) ($overrides['tracking_context'] ?? 'search_page'));
        $recommendationData = $this->productService->getRecommendedProducts(
            (int) session('customer_id') ?: null,
            $request->ip(),
            $highlightProduct?->id,
            4,
            $trackingContext
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

        $pageMode = trim((string) ($overrides['search_page_mode'] ?? 'search'));
        $searchPageSummary = $overrides['search_page_summary'] ?? null;
        $searchResultsTitle = $overrides['search_results_title'] ?? null;

        if ($pageMode === 'best_selling') {
            $searchPageSummary ??= $highlightProduct
                ? 'Danh sách đang được xếp theo số lượng bán thành công. Chiến dịch hiện tại ưu tiên '.$highlightProduct->name.' ở đầu danh sách để người dùng mở ra thấy ngay.'
                : 'Danh sách được xếp theo số lượng bán thành công gần đây và mức độ quan tâm thực tế của người dùng.';
            $searchResultsTitle ??= $highlightProduct
                ? 'Ưu tiên từ banner: '.$highlightProduct->name
                : 'Sản phẩm bán chạy hiện tại';
        }

        $viewName = $pageMode === 'all_products' ? 'pages.sanpham.catalog' : 'pages.sanpham.search';

        return view($viewName, [
            'category' => $cate_product,
            'brand' => $brand_product,
            'search_product' => $search_product,
            'search_keyword' => $keywords,
            'search_filters' => $filters,
            'search_categories' => $cate_product,
            'search_brands' => $brand_product,
            'recommended_products' => $recommendationData['products'],
            'recommendation_description' => $recommendationData['description'],
            'meta_desc' => $overrides['meta_desc'] ?? 'Tìm kiếm sản phẩm',
            'meta_keywords' => $overrides['meta_keywords'] ?? 'Tìm kiếm sản phẩm',
            'meta_title' => $overrides['meta_title'] ?? 'Tìm kiếm sản phẩm',
            'url_canonical' => $url_canonical,
            'slider' => $slider,
            'category_post' => $category_post,
            'search_page_mode' => $pageMode,
            'search_page_kicker' => $overrides['search_page_kicker'] ?? 'Tìm kiếm sản phẩm',
            'search_page_title' => $overrides['search_page_title'] ?? 'Kết quả tìm kiếm',
            'search_page_summary' => $searchPageSummary,
            'search_form_action' => $overrides['search_form_action'] ?? url('/tim-kiem'),
            'search_clear_url' => $overrides['search_clear_url'] ?? url('/tim-kiem'),
            'search_results_kicker' => $overrides['search_results_kicker'] ?? 'Danh sách kết quả',
            'search_results_title' => $searchResultsTitle,
            'search_highlight_product' => $highlightProduct,
        ]);
    }

    private function resolveHeroSlideTargets(Collection $slides): array
    {
        return $slides
            ->mapWithKeys(function ($slide) {
                $imageKey = basename((string) ($slide->slider_image ?? ''));

                if ($imageKey === '') {
                    return [];
                }

                return [
                    $imageKey => $this->resolveHeroSlideTarget($slide),
                ];
            })
            ->all();
    }

    private function resolveHeroSlideTarget(mixed $slide): array
    {
        $imageKey = basename((string) ($slide->slider_image ?? ''));
        $configuredHints = (array) config('storefront.hero_slider.product_hints', []);
        $configuredTarget = $imageKey === ''
            ? []
            : (array) ($configuredHints[$imageKey] ?? []);
        $configuredProductSlug = trim((string) ($configuredTarget['product_slug'] ?? ''));
        $product = $this->resolveHeroSlideProduct($slide, $configuredTarget);
        $highlightProductSlug = $product?->slug ?: ($configuredProductSlug !== '' ? $configuredProductSlug : null);

        return [
            'url' => $this->buildBestSellingListingUrl($highlightProductSlug),
            'highlight_product_slug' => $highlightProductSlug,
            'highlight_product_name' => $product?->name,
        ];
    }

    private function resolveHeroSlideProduct(mixed $slide, array $configuredTarget = []): ?Product
    {
        $configuredProductSlug = trim((string) ($configuredTarget['product_slug'] ?? ''));

        if ($configuredProductSlug !== '') {
            $configuredProduct = $this->productService->findProductBySlug($configuredProductSlug);

            if ($configuredProduct) {
                return $configuredProduct;
            }
        }

        $candidateHints = collect([
            $configuredTarget['search_hint'] ?? null,
            $slide->slider_alt ?? null,
            $slide->slider_name ?? null,
            $slide->slider_desc ?? null,
        ])
            ->flatMap(fn ($text) => $this->extractHeroProductHints($text))
            ->unique()
            ->values();

        foreach ($candidateHints as $hint) {
            $matchedProduct = $this->productService->searchSuggestions($hint, 1)->first();

            if ($matchedProduct) {
                return $matchedProduct;
            }
        }

        return null;
    }

    private function extractHeroProductHints(?string $text): array
    {
        $cleanedText = trim((string) Str::of((string) $text)
            ->replaceMatches('/[^A-Za-z0-9+\- ]+/u', ' ')
            ->squish());

        if ($cleanedText === '') {
            return [];
        }

        $tokens = array_values(array_filter(preg_split('/\s+/', $cleanedText) ?: []));
        $stopWords = [
            'arena',
            'banner',
            'ban',
            'baner',
            'chinh',
            'cong',
            'deal',
            'demo',
            'de',
            'dien',
            'hot',
            'khuyen',
            'len',
            'mai',
            'moi',
            'ngay',
            'nghe',
            'pham',
            'sale',
            'slider',
            'tech',
            'thong',
            'tu',
            'uu',
            'xem',
        ];
        $candidatePhrases = [$cleanedText];

        preg_match_all(
            '/\b[A-Za-z]{2,}(?:\s+[A-Za-z]{1,12})?\s+[A-Za-z0-9+\-]*\d[A-Za-z0-9+\-]*\b/u',
            $cleanedText,
            $matchedPhrases
        );

        foreach ($matchedPhrases[0] ?? [] as $matchedPhrase) {
            $candidatePhrases[] = trim((string) $matchedPhrase);
        }

        foreach ($tokens as $index => $token) {
            if (! preg_match('/\d/', $token)) {
                continue;
            }

            $previousToken = $tokens[$index - 1] ?? null;
            $nextToken = $tokens[$index + 1] ?? null;
            $candidatePhrases[] = $token;

            if ($previousToken !== null) {
                $candidatePhrases[] = trim($previousToken.' '.$token);
            }

            if ($previousToken !== null && $nextToken !== null) {
                $candidatePhrases[] = trim($previousToken.' '.$token.' '.$nextToken);
            }
        }

        $filteredTokens = collect($tokens)
            ->reject(fn ($token) => in_array(Str::lower((string) $token), $stopWords, true))
            ->values();

        if ($filteredTokens->count() >= 2) {
            $candidatePhrases[] = $filteredTokens->take(3)->implode(' ');
        }

        return collect($candidatePhrases)
            ->map(fn ($phrase) => trim((string) $phrase))
            ->filter(fn ($phrase) => Str::length($phrase) >= 2)
            ->unique()
            ->values()
            ->all();
    }

    private function buildBestSellingListingUrl(?string $highlightProductSlug = null): string
    {
        $bestSellingPath = trim((string) config('storefront.hero_slider.best_selling_path', '/san-pham-ban-chay'));
        $baseUrl = $this->resolveStorefrontUrl($bestSellingPath, url('/san-pham-ban-chay'));
        $highlightProductSlug = trim((string) $highlightProductSlug);

        if ($highlightProductSlug === '') {
            return $baseUrl;
        }

        return $baseUrl.(str_contains($baseUrl, '?') ? '&' : '?').http_build_query([
            'highlight_product' => $highlightProductSlug,
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

    private function calculateHeroRatio($slider): ?float
    {
        $ratios = [];

        foreach ($slider as $slide) {
            $path = public_path('uploads/slider/'.$slide->slider_image);
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

        $targetCount = max(1, (int) ($config['target_count'] ?? 6));
        $useDemoProducts = $this->allowsDemoFallbacks() && (bool) ($config['use_demo_products'] ?? false);
        $productSlugs = collect($config['product_slugs'] ?? [])
            ->map(static fn ($slug) => trim((string) $slug))
            ->filter()
            ->values();

        $productsBySlug = $productSlugs->isNotEmpty()
            ? Product::query()
                ->with(['brand', 'primaryCategory'])
                ->where('is_active', true)
                ->whereIn('slug', $productSlugs->all())
                ->get()
                ->keyBy('slug')
            : collect();

        $orderedProducts = $productSlugs
            ->map(fn ($slug) => $productsBySlug->get($slug))
            ->filter()
            ->values();

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

        if ($useDemoProducts && $cards->count() < $targetCount) {
            $cards = $cards
                ->concat($this->resolveFlashSaleDemoCards(
                    $targetCount - $cards->count(),
                    $cards->pluck('name')->all(),
                ))
                ->values();
        }

        $cards = $cards->take($targetCount)->values();

        if ($cards->isEmpty()) {
            return null;
        }

        $countdownEndsAt = $this->parseStorefrontDate($config['countdown_ends_at'] ?? null);

        return [
            'kicker' => trim((string) ($config['kicker'] ?? 'Flash')),
            'title' => trim((string) ($config['title'] ?? 'Khuyến mãi')),
            'cta_label' => trim((string) ($config['cta_label'] ?? 'Xem tất cả')),
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
        $progressValue = $hasSoldMetrics
            ? (int) round(($soldQuantity / $progressMax) * 100)
            : max(34, min(92, 52 + (int) floor($discountPercent / 2)));

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
                'label' => $stockQuantity > 0 ? 'Còn '.$stockQuantity : 'Sắp cháy hàng',
            ],
        ])->filter(fn ($item) => filled($item['label']))->take(3)->values()->all();

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'url' => url('/chi-tiet/'.$product->slug),
            'image' => $this->resolvePublicAssetUrl($product->product_image),
            'price' => $originalPrice,
            'final_price' => $finalPrice,
            'discount_percent' => $discountPercent,
            'specs' => $specs,
            'seller_badge' => $discountPercent >= 45 ? 'Yêu thích+' : 'Yêu thích',
            'seller_badge_variant' => 'favorite',
            'campaign_tags' => ['25.4', 'VOUCHER', 'SIÊU RẺ'],
            'sold_quantity' => $hasSoldMetrics ? $soldQuantity : null,
            'sold_percentage' => $progressValue,
            'sold_progress' => $progressValue,
            'sold_message' => $hasSoldMetrics
                ? 'Đã bán '.number_format(max(0, $soldQuantity)).' sản phẩm'
                : 'Số lượng có hạn, chốt đơn nhanh trong ngày',
            'deal_bar_label' => $hasSoldMetrics && $soldQuantity > 0
                ? 'ĐÃ BÁN '.number_format(max(0, $soldQuantity))
                : 'ĐANG BÁN CHẠY',
            'is_mock' => false,
        ];
    }

    private function resolveFlashSaleDemoCards(int $limit, array $excludeNames = []): Collection
    {
        if ($limit <= 0) {
            return collect();
        }

        $samples = $this->loadFlashSaleDemoProductData();

        return $samples
            ->reject(fn (array $sample) => in_array($sample['name'], $excludeNames, true))
            ->take($limit)
            ->values()
            ->map(function (array $sample, int $index) {
                $price = (float) ($sample['price'] ?? 0);
                $finalPrice = (float) ($sample['final_price'] ?? $price);
                $sampleDiscountPercent = isset($sample['discount_percent'])
                    ? (int) $sample['discount_percent']
                    : null;
                $discountPercent = $sampleDiscountPercent !== null
                    ? max(0, $sampleDiscountPercent)
                    : (
                        $price > 0 && $finalPrice < $price
                            ? (int) round((($price - $finalPrice) / $price) * 100)
                            : 0
                    );

                return [
                    'id' => 'flash-demo-'.$index,
                    'name' => $sample['name'],
                    'slug' => Str::slug($sample['name']),
                    'url' => $this->buildSearchResultUrl((string) ($sample['query'] ?? $sample['name'])),
                    'image' => $this->resolvePublicAssetUrl((string) ($sample['image'] ?? null)),
                    'price' => $price,
                    'final_price' => $finalPrice,
                    'discount_percent' => $discountPercent,
                    'specs' => [],
                    'seller_badge' => $sample['seller_badge'] ?? 'Yêu thích',
                    'seller_badge_variant' => $sample['seller_badge_variant'] ?? 'favorite',
                    'campaign_tags' => collect($sample['campaign_tags'] ?? [])->filter()->take(3)->values()->all(),
                    'sold_quantity' => null,
                    'sold_percentage' => (int) ($sample['sold_progress'] ?? 68),
                    'sold_progress' => (int) ($sample['sold_progress'] ?? 68),
                    'sold_message' => $sample['sold_message'] ?? 'Số lượng có hạn, chốt đơn nhanh trong ngày',
                    'deal_bar_label' => $sample['deal_bar_label']
                        ?? ('ĐÃ BÁN '.(int) ($sample['sold_progress'] ?? 68)),
                    'is_mock' => true,
                ];
            });
    }

    private function loadFlashSaleDemoProductData(): Collection
    {
        $path = resource_path('data/flash-sale-products.json');

        if (! is_file($path)) {
            return collect();
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        if (! is_array($decoded)) {
            return collect();
        }

        return collect($decoded)->filter(function ($item) {
            return is_array($item)
                && filled($item['name'] ?? null)
                && filled($item['image'] ?? null);
        })->values();
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

    private function resolveHomepageFeaturedProducts(int $limit = 8): Collection
    {
        $limit = max(1, $limit);

        $featuredProducts = $this->baseHomepageProductQuery()
            ->where('is_featured', true)
            ->orderByDesc('views_count')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        if ($featuredProducts->count() >= $limit) {
            return $featuredProducts->values();
        }

        $fallbackProducts = $this->baseHomepageProductQuery()
            ->whereNotIn('id', $featuredProducts->pluck('id')->all())
            ->orderByDesc('views_count')
            ->orderByDesc('id')
            ->limit($limit - $featuredProducts->count())
            ->get();

        return $featuredProducts
            ->concat($fallbackProducts)
            ->values();
    }

    private function resolveHomepageSaleProducts(int $limit = 4, ?Collection $featuredProducts = null): Collection
    {
        $limit = max(1, $limit);
        $productTable = (new Product)->getTable();
        $hasSalePriceColumn = Schema::hasColumn($productTable, 'sale_price');
        $hasSaleValueColumn = Schema::hasColumn($productTable, 'sale_value');
        $excludeIds = collect($featuredProducts)
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->filter(static fn ($id) => $id > 0)
            ->values();

        $saleProducts = $this->baseHomepageProductQuery()
            ->when(
                $excludeIds->isNotEmpty(),
                fn ($query) => $query->whereNotIn('id', $excludeIds->all())
            )
            ->when($hasSalePriceColumn || $hasSaleValueColumn, function ($query) use ($hasSalePriceColumn, $hasSaleValueColumn) {
                $query->where(function ($discountQuery) use ($hasSalePriceColumn, $hasSaleValueColumn) {
                    if ($hasSalePriceColumn) {
                        $discountQuery->where(function ($saleQuery) {
                            $saleQuery->whereNotNull('sale_price')
                                ->where('sale_price', '>', 0);
                        });
                    }

                    if ($hasSaleValueColumn) {
                        $method = $hasSalePriceColumn ? 'orWhere' : 'where';

                        $discountQuery->{$method}(function ($saleQuery) {
                            $saleQuery->whereNotNull('sale_value')
                                ->where('sale_value', '>', 0);
                        });
                    }
                });
            })
            ->orderByDesc('is_featured')
            ->when($hasSaleValueColumn, fn ($query) => $query->orderByDesc('sale_value'))
            ->orderByDesc('views_count')
            ->orderByDesc('id')
            ->limit($limit * 2)
            ->get()
            ->filter(function (Product $product) {
                $originalPrice = (float) ($product->getRawOriginal('price') ?? $product->price ?? 0);
                $finalPrice = (float) ($product->final_price ?? $originalPrice);

                return $originalPrice > 0 && $finalPrice > 0 && $finalPrice < $originalPrice;
            })
            ->take($limit)
            ->values();

        if ($saleProducts->count() >= $limit) {
            return $saleProducts;
        }

        $fallbackProducts = $this->baseHomepageProductQuery()
            ->when(
                $excludeIds->isNotEmpty() || $saleProducts->isNotEmpty(),
                fn ($query) => $query->whereNotIn(
                    'id',
                    $excludeIds->merge($saleProducts->pluck('id'))->unique()->values()->all()
                )
            )
            ->orderByDesc('is_featured')
            ->orderByDesc('views_count')
            ->orderByDesc('id')
            ->limit($limit - $saleProducts->count())
            ->get();

        return $saleProducts
            ->concat($fallbackProducts)
            ->take($limit)
            ->values();
    }

    private function resolveHomepageBestSellerProducts(int $limit = 6): Collection
    {
        $limit = max(1, $limit);
        $query = $this->baseHomepageProductQuery();

        if ($this->canUseBestSellingRanking()) {
            $productTable = (new Product)->getTable();
            $salesRanking = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', 'completed')
                ->whereNotNull('order_items.product_id')
                ->groupBy('order_items.product_id')
                ->selectRaw('order_items.product_id, SUM(order_items.quantity) as sold_quantity');

            $query->leftJoinSub($salesRanking, 'homepage_sales_rank', function ($join) use ($productTable) {
                $join->on('homepage_sales_rank.product_id', '=', $productTable.'.id');
            });

            $query->select($productTable.'.*')
                ->selectRaw('COALESCE(homepage_sales_rank.sold_quantity, 0) as sold_quantity_rank')
                ->orderByRaw('COALESCE(homepage_sales_rank.sold_quantity, 0) DESC')
                ->orderByDesc($productTable.'.views_count')
                ->orderByDesc($productTable.'.is_featured')
                ->orderByDesc($productTable.'.id');
        } else {
            $query->orderByDesc('views_count')
                ->orderByDesc('is_featured')
                ->orderByDesc('id');
        }

        return $query->limit($limit)->get();
    }

    private function resolveHomepageNewsPosts(int $limit = 3): Collection
    {
        if (! Schema::hasTable((new BlogPost)->getTable())) {
            return collect();
        }

        return BlogPost::query()
            ->with('category')
            ->where('status', 'published')
            ->orderByDesc('is_featured')
            ->orderByRaw('CASE WHEN published_at IS NULL THEN 1 ELSE 0 END ASC')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(max(1, $limit))
            ->get();
    }

    private function baseHomepageProductQuery()
    {
        return Product::query()
            ->with(['brand', 'primaryCategory'])
            ->where('is_active', true);
    }

    private function canUseBestSellingRanking(): bool
    {
        return Schema::hasTable('orders')
            && Schema::hasTable('order_items')
            && Schema::hasColumn('orders', 'status')
            && Schema::hasColumn('order_items', 'order_id')
            && Schema::hasColumn('order_items', 'product_id')
            && Schema::hasColumn('order_items', 'quantity');
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
                    'url' => url('/danh-muc/'.$category->slug_category_product),
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

        return url('/tim-kiem?'.http_build_query([
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
        return AssetUrl::resolve($path, $fallback);
    }

    private function effectivePriceExpression(): string
    {
        return ProductPriceSql::effectivePriceExpression();
    }

    private function legacyIcons()
    {
        $table = (new SiteLink())->getTable();

        if (! Schema::hasTable($table)) {
            return collect();
        }

        return SiteLink::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get()
            ->map(function (SiteLink $link) {
                $iconPath = trim((string) $link->icon_path);

                return (object) [
                    'id_icons' => $link->id,
                    'name' => $link->name,
                    'link' => $link->url,
                    'category' => $link->category,
                    'image' => $iconPath !== '' ? basename($iconPath) : null,
                ];
            });
    }
}
