@extends('layouts.home-tech')

@section('content')
    @php
        $storefrontLabel = \App\Support\StorefrontLabel::class;
        $assetUrl = \App\Support\AssetUrl::class;
        $filters = $search_filters ?? [];
        $selectedCategory = $filters['category_id'] ?? null;
        $selectedBrand = $filters['brand_id'] ?? null;
        $selectedSort = $filters['sort'] ?? 'popular';
        $keyword = trim((string) ($search_keyword ?? ''));
        $productCount = method_exists($search_product, 'total') ? $search_product->total() : $search_product->count();
        $perPage = method_exists($search_product, 'perPage') ? $search_product->perPage() : 12;
        $currentPage = method_exists($search_product, 'currentPage') ? $search_product->currentPage() : 1;
        $rangeFrom = $productCount > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
        $rangeTo = $productCount > 0 ? min($currentPage * $perPage, $productCount) : 0;
        $currentItems = method_exists($search_product, 'getCollection') ? $search_product->getCollection() : collect($search_product);
        $activeCategory = $selectedCategory ? ($search_categories ?? collect())->firstWhere('category_id', (int) $selectedCategory) : null;
        $activeCategoryLabel = $activeCategory
            ? $storefrontLabel::category($activeCategory->category_name, $activeCategory->slug_category_product ?? null)
            : 'Tất cả sản phẩm';
        $catalogTitle = $activeCategory ? $activeCategoryLabel : ($search_page_title ?? 'Tất cả sản phẩm');
        $catalogSummary = $activeCategory
            ? trim(strip_tags((string) ($activeCategory->category_desc ?? '')))
            : 'Khám phá ' . number_format($productCount) . ' sản phẩm chính hãng với giá tốt, bảo hành uy tín';
        if ($catalogSummary === '') {
            $catalogSummary = 'Khám phá ' . number_format($productCount) . ' sản phẩm chính hãng với giá tốt, bảo hành uy tín';
        }
        $formatMoney = fn ($value) => $value > 0 ? number_format((float) $value, 0, ',', '.') . 'đ' : 'Đang cập nhật';
        $sortLabels = [
            'popular' => 'Phổ biến',
            'created_at_desc' => 'Mới nhất',
            'price_asc' => 'Giá thấp đến cao',
            'price_desc' => 'Giá cao đến thấp',
            'best_selling' => 'Bán chạy',
            'sale_desc' => 'Khuyến mãi',
        ];
        $searchFormAction = $search_form_action ?? url('/san-pham');
        $searchClearUrl = $search_clear_url ?? url('/san-pham');

        $allCategories = collect($search_categories ?? []);
        $rootCategories = $allCategories->filter(fn ($cat) => (int) ($cat->category_parent ?? 0) === 0);
        if ($rootCategories->isEmpty()) {
            $rootCategories = $allCategories;
        }
        $subcategoryChips = $rootCategories->take(6)->values();

        $brandChunks = collect($search_brands ?? [])->take(7);

        $brandUsageOptions = [
            'gaming' => 'Gaming - Đồ họa',
            'office' => 'Văn phòng - Học tập',
            'design' => 'Đồ họa - Kỹ thuật',
            'business' => 'Doanh nhân - Mỏng nhẹ',
            'developer' => 'Lập trình - IT',
        ];

        $stockOptions = [
            '' => 'Tất cả',
            'new' => 'Mới 100%',
            'like_new' => 'Like New',
            'showcase' => 'Trưng bày',
        ];

        $catalogPriceBuckets = [
            ['label' => 'Dưới 10 triệu', 'min' => 0, 'max' => 10000000],
            ['label' => '10 - 20 triệu', 'min' => 10000000, 'max' => 20000000],
            ['label' => '20 - 30 triệu', 'min' => 20000000, 'max' => 30000000],
            ['label' => '30 - 50 triệu', 'min' => 30000000, 'max' => 50000000],
            ['label' => 'Trên 50 triệu', 'min' => 50000000, 'max' => null],
        ];

        $heroLevelTitle = mb_strtoupper($activeCategoryLabel === 'Tất cả sản phẩm' ? 'Sản phẩm công nghệ' : $activeCategoryLabel);

        $brandItems = collect(['ASUS', 'MSI', 'DELL', 'Lenovo', 'Samsung', 'Logitech', 'Kingston', 'Intel'])
            ->map(fn (string $name) => [
                'name' => $name,
                'url' => url('/thuong-hieu/' . \Illuminate\Support\Str::slug($name)),
                'logo' => null,
                'initials' => mb_strtoupper(mb_substr($name, 0, 2)),
            ]);

        $paymentItems = ['VISA', 'Mastercard', 'Momo', 'ZaloPay'];

        $cardForProduct = function ($product) use ($storefrontLabel, $assetUrl) {
            $finalPrice = (float) ($product->final_price ?? $product->product_price ?? 0);
            $originalPrice = (float) ($product->price ?? $product->product_price ?? $finalPrice);
            $discount = 0;
            if ($originalPrice > 0 && $finalPrice > 0 && $finalPrice < $originalPrice) {
                $discount = (int) round((($originalPrice - $finalPrice) / $originalPrice) * 100);
            }
            $brandName = $storefrontLabel::neutralizeSource(optional($product->brand)->brand_name ?? ($product->brand_name ?? 'DangDuc'));

            return [
                'id' => $product->product_id ?? $product->id,
                'name' => $storefrontLabel::neutralizeSource($product->product_name),
                'url' => url('/chi-tiet/' . $product->product_slug),
                'image' => $assetUrl::product($product->product_image),
                'brand' => $brandName,
                'brand_initials' => mb_strtoupper(mb_substr($brandName, 0, 2)),
                'price' => $finalPrice,
                'original_price' => $originalPrice,
                'discount_percent' => $discount,
            ];
        };
    @endphp

    <main class="tech-home-main catalog-tech-main">
        <div class="container">
            @include('partials.storefront.breadcrumb', [
                'class' => 'catalog-breadcrumb',
                'separatorClass' => 'catalog-breadcrumb-sep',
                'currentClass' => 'catalog-breadcrumb-current',
                'items' => array_filter([
                    [
                        'label' => 'Sản phẩm',
                        'url' => $activeCategory ? url('/san-pham') : null,
                        'current' => ! $activeCategory,
                    ],
                    $activeCategory ? [
                        'label' => $activeCategoryLabel,
                        'current' => true,
                    ] : null,
                ]),
            ])

            <header class="catalog-hero-head">
                <h1 class="catalog-title">{{ $catalogTitle }}</h1>
                <p class="catalog-summary">{{ $catalogSummary }}</p>
            </header>

            @if ($subcategoryChips->isNotEmpty())
                <div class="catalog-subcat-rail" role="tablist">
                    <a href="{{ $searchClearUrl }}"
                       class="catalog-subcat-card {{ ! $selectedCategory ? 'is-active' : '' }}">
                        <span class="catalog-subcat-icon"><i class="fa fa-th-large"></i></span>
                        <span class="catalog-subcat-copy">
                            <strong>Tất cả {{ $catalogTitle }}</strong>
                            <small>{{ number_format($productCount) }}</small>
                        </span>
                    </a>
                    @foreach ($subcategoryChips as $sub)
                        @php
                            $subUrl = url('/san-pham?category_id=' . $sub->category_id);
                            $subLabel = $storefrontLabel::category($sub->category_name, $sub->slug_category_product ?? null);
                            $subCount = $sub->product_count ?? $sub->products_count ?? null;
                            $isActive = (string) $selectedCategory === (string) $sub->category_id;
                        @endphp
                        <a href="{{ $subUrl }}" class="catalog-subcat-card {{ $isActive ? 'is-active' : '' }}">
                            <span class="catalog-subcat-icon"><i class="fa fa-laptop"></i></span>
                            <span class="catalog-subcat-copy">
                                <strong>{{ $subLabel }}</strong>
                                @if ($subCount !== null)
                                    <small>{{ number_format($subCount) }}</small>
                                @endif
                            </span>
                        </a>
                    @endforeach
                    <button type="button" class="catalog-subcat-arrow" aria-label="Xem thêm danh mục">
                        <i class="fa fa-angle-right"></i>
                    </button>
                </div>
            @endif

            <a href="#" class="catalog-promo-banner" aria-label="Trả góp 0% và bảo hành chính hãng">
                <div class="catalog-promo-copy">
                    <h2>TRẢ GÓP 0% – DỄ DÀNG SỞ HỮU</h2>
                    <p>Hỗ trợ trả góp qua thẻ tín dụng &amp; CMND/CCCD</p>
                    <span class="catalog-promo-cta">Xem ngay</span>
                </div>
                <div class="catalog-promo-art" aria-hidden="true">
                    <span class="catalog-promo-zero">0%</span>
                    <span class="catalog-promo-shield">
                        <i class="fa fa-shield"></i>
                        <span>BẢO HÀNH<br>CHÍNH HÃNG</span>
                    </span>
                </div>
            </a>

            <div class="catalog-shell">
                <aside class="catalog-sidebar" aria-label="Bộ lọc sản phẩm">
                    <form action="{{ $searchFormAction }}" method="GET" class="catalog-filter-form">
                        @if ($keyword !== '')
                            <input type="hidden" name="keywords_submit" value="{{ $keyword }}">
                        @endif
                        @if (! empty($filters['product_code']))
                            <input type="hidden" name="product_code" value="{{ $filters['product_code'] }}">
                        @endif
                        @if (! empty($filters['highlight_product']))
                            <input type="hidden" name="highlight_product" value="{{ $filters['highlight_product'] }}">
                        @endif

                        <section class="catalog-filter-block">
                            <h3>Danh mục</h3>
                            <ul class="catalog-filter-list">
                                <li>
                                    <label>
                                        <input type="radio" name="category_id" value="" @checked(empty($selectedCategory))>
                                        <span class="catalog-filter-check"></span>
                                        <span class="catalog-filter-text">Tất cả {{ $catalogTitle }}</span>
                                        <small class="catalog-filter-count">{{ number_format($productCount) }}</small>
                                    </label>
                                </li>
                                @foreach ($rootCategories->take(8) as $cat)
                                    @php
                                        $catLabel = $storefrontLabel::category($cat->category_name, $cat->slug_category_product ?? null);
                                        $catCount = $cat->product_count ?? $cat->products_count ?? null;
                                    @endphp
                                    <li>
                                        <label>
                                            <input type="radio" name="category_id" value="{{ $cat->category_id }}"
                                                @checked((string) $selectedCategory === (string) $cat->category_id)>
                                            <span class="catalog-filter-check"></span>
                                            <span class="catalog-filter-text">{{ $catLabel }}</span>
                                            @if ($catCount !== null)
                                                <small class="catalog-filter-count">{{ number_format($catCount) }}</small>
                                            @endif
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($rootCategories->count() > 8)
                                <button type="button" class="catalog-filter-more">Xem thêm</button>
                            @endif
                        </section>

                        <section class="catalog-filter-block">
                            <h3>Khoảng giá</h3>
                            <div class="catalog-price-meta">
                                <span>{{ $formatMoney($filters['price_min'] ?? 0) }}</span>
                                <span>{{ $filters['price_max'] ? $formatMoney($filters['price_max']) : '50.000.000đ+' }}</span>
                            </div>
                            <div class="catalog-price-rail">
                                <span class="catalog-price-line"></span>
                                <span class="catalog-price-handle catalog-price-handle--start"></span>
                                <span class="catalog-price-handle catalog-price-handle--end"></span>
                            </div>
                            <div class="catalog-price-scale">
                                <span>0đ</span>
                                <span>25tr</span>
                                <span>50tr+</span>
                            </div>
                            <div class="catalog-price-buckets">
                                @foreach ($catalogPriceBuckets as $bucket)
                                    @php
                                        $bucketUrl = url('/san-pham?price_min=' . $bucket['min'] . ($bucket['max'] !== null ? '&price_max=' . $bucket['max'] : '') . ($selectedCategory ? '&category_id=' . $selectedCategory : ''));
                                        $isActiveBucket = (string) ($filters['price_min'] ?? '') === (string) $bucket['min']
                                            && (string) ($filters['price_max'] ?? '') === (string) ($bucket['max'] ?? '');
                                    @endphp
                                    <a href="{{ $bucketUrl }}" class="catalog-price-bucket {{ $isActiveBucket ? 'is-active' : '' }}">{{ $bucket['label'] }}</a>
                                @endforeach
                            </div>
                            <input type="hidden" name="price_min" value="{{ $filters['price_min'] ?? '' }}">
                            <input type="hidden" name="price_max" value="{{ $filters['price_max'] ?? '' }}">
                        </section>

                        <section class="catalog-filter-block">
                            <h3>Thương hiệu</h3>
                            <ul class="catalog-filter-list">
                                @foreach ($brandChunks as $brandItem)
                                    @php
                                        $brandLabel = $storefrontLabel::neutralizeSource($brandItem->brand_name);
                                        $brandCount = $brandItem->product_count ?? $brandItem->products_count ?? null;
                                    @endphp
                                    <li>
                                        <label>
                                            <input type="radio" name="brand_id" value="{{ $brandItem->brand_id }}"
                                                @checked((string) $selectedBrand === (string) $brandItem->brand_id)>
                                            <span class="catalog-filter-check catalog-filter-check--square"></span>
                                            <span class="catalog-filter-text">{{ $brandLabel }}</span>
                                            @if ($brandCount !== null)
                                                <small class="catalog-filter-count">({{ number_format($brandCount) }})</small>
                                            @endif
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                            @if (collect($search_brands ?? [])->count() > 7)
                                <button type="button" class="catalog-filter-more">Xem thêm</button>
                            @endif
                        </section>

                        <section class="catalog-filter-block">
                            <h3>Nhu cầu sử dụng</h3>
                            <ul class="catalog-filter-list">
                                @foreach ($brandUsageOptions as $usageKey => $usageLabel)
                                    <li>
                                        <label>
                                            <input type="checkbox" name="usage[]" value="{{ $usageKey }}" disabled>
                                            <span class="catalog-filter-check catalog-filter-check--square"></span>
                                            <span class="catalog-filter-text">{{ $usageLabel }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </section>

                        <section class="catalog-filter-block">
                            <h3>Tình trạng hàng</h3>
                            <ul class="catalog-filter-list">
                                @foreach ($stockOptions as $stockKey => $stockLabel)
                                    <li>
                                        <label>
                                            <input type="radio" name="stock_status" value="{{ $stockKey }}" @checked($stockKey === '')>
                                            <span class="catalog-filter-check"></span>
                                            <span class="catalog-filter-text">{{ $stockLabel }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </section>

                        <section class="catalog-filter-block">
                            <h3>Sắp xếp theo</h3>
                            <ul class="catalog-filter-list">
                                @foreach ($sortLabels as $sortKey => $sortLabel)
                                    <li>
                                        <label>
                                            <input type="radio" name="sort" value="{{ $sortKey }}" @checked($selectedSort === $sortKey)>
                                            <span class="catalog-filter-check"></span>
                                            <span class="catalog-filter-text">{{ $sortLabel }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </section>

                        <div class="catalog-filter-actions">
                            <button type="submit" class="catalog-filter-apply">Áp dụng bộ lọc</button>
                            <a href="{{ $searchClearUrl }}" class="catalog-filter-reset">Xóa tất cả bộ lọc</a>
                        </div>
                    </form>
                </aside>

                <section class="catalog-results" aria-label="Danh sách sản phẩm">
                    <div class="catalog-toolbar">
                        <div class="catalog-toolbar-info">
                            Hiển thị <strong>{{ $rangeFrom }}-{{ $rangeTo }}</strong> trong <strong>{{ number_format($productCount) }}</strong> sản phẩm
                        </div>
                        <div class="catalog-toolbar-controls">
                            <div class="catalog-view-toggle" role="group" aria-label="Chế độ xem">
                                <button type="button" class="catalog-view-btn is-active" data-view="grid" aria-label="Xem dạng lưới">
                                    <i class="fa fa-th-large"></i>
                                </button>
                                <button type="button" class="catalog-view-btn" data-view="list" aria-label="Xem dạng danh sách">
                                    <i class="fa fa-list"></i>
                                </button>
                            </div>

                            <form action="{{ $searchFormAction }}" method="GET" class="catalog-sort-form">
                                @if ($keyword !== '')
                                    <input type="hidden" name="keywords_submit" value="{{ $keyword }}">
                                @endif
                                @if (! empty($selectedCategory))
                                    <input type="hidden" name="category_id" value="{{ $selectedCategory }}">
                                @endif
                                @if (! empty($selectedBrand))
                                    <input type="hidden" name="brand_id" value="{{ $selectedBrand }}">
                                @endif
                                @if (! empty($filters['price_min']))
                                    <input type="hidden" name="price_min" value="{{ $filters['price_min'] }}">
                                @endif
                                @if (! empty($filters['price_max']))
                                    <input type="hidden" name="price_max" value="{{ $filters['price_max'] }}">
                                @endif
                                <label for="catalog-sort-select">Sắp xếp:</label>
                                <select id="catalog-sort-select" name="sort" onchange="this.form.submit()">
                                    @foreach ($sortLabels as $sortKey => $sortLabel)
                                        <option value="{{ $sortKey }}" @selected($selectedSort === $sortKey)>{{ $sortLabel }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    @if ($search_product->isEmpty())
                        @include('partials.storefront.empty-state', [
                            'title' => 'Không tìm thấy sản phẩm phù hợp',
                            'message' => 'Thử đổi danh mục, thương hiệu hoặc khoảng giá để xem thêm sản phẩm.',
                            'icon' => 'fa fa-search',
                            'class' => 'shopw-search-empty',
                            'actionText' => 'Xóa bộ lọc',
                            'actionUrl' => $searchClearUrl,
                        ])
                    @else
                        <div class="catalog-grid" data-catalog-view="grid">
                            @foreach ($search_product as $product)
                                <div class="catalog-grid-item">
                                    @include('partials.home.product-card', [
                                        'product' => $cardForProduct($product),
                                    ])
                                </div>
                            @endforeach
                        </div>

                        @if (method_exists($search_product, 'links'))
                            <nav class="catalog-pagination" aria-label="Phân trang">
                                {{ $search_product->onEachSide(1)->links('pagination::bootstrap-4') }}
                            </nav>
                        @endif
                    @endif
                </section>
            </div>

            @if (! empty($recommended_products) && count($recommended_products) > 0)
                <section class="catalog-recent-section" aria-label="Sản phẩm đã xem gần đây">
                    <div class="tech-section-head">
                        <div>
                            <h2>Sản phẩm đã xem gần đây</h2>
                        </div>
                        <a href="{{ url('/san-pham') }}" class="tech-section-link">Xem tất cả <i class="fa fa-angle-right"></i></a>
                    </div>

                    <div class="catalog-recent-viewport" data-tech-sale-viewport>
                        <button type="button" class="catalog-recent-nav catalog-recent-prev" data-tech-sale-prev aria-label="Trước">
                            <i class="fa fa-angle-left"></i>
                        </button>
                        <div class="catalog-recent-track" data-tech-sale-track>
                            @foreach ($recommended_products as $recent)
                                <div class="catalog-recent-item">
                                    @include('partials.home.product-card', [
                                        'product' => $cardForProduct($recent),
                                        'compact' => true,
                                    ])
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="catalog-recent-nav catalog-recent-next" data-tech-sale-next aria-label="Sau">
                            <i class="fa fa-angle-right"></i>
                        </button>
                    </div>
                </section>
            @endif
        </div>

        @include('partials.home.partners', ['brandItems' => $brandItems])
    </main>

    <script>
        (function () {
            const grid = document.querySelector('[data-catalog-view]');
            const buttons = document.querySelectorAll('.catalog-view-btn');
            buttons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    buttons.forEach((b) => b.classList.remove('is-active'));
                    btn.classList.add('is-active');
                    if (grid) {
                        grid.setAttribute('data-catalog-view', btn.getAttribute('data-view'));
                    }
                });
            });
        })();
    </script>
@endsection
