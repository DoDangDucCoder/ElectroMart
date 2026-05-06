@extends('layouts.home-tech')

@section('body_class', 'g-search search-redesign')

@push('styles')
    <link href="{{ asset('frontend/css/search-redesign.css') }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $searchCount = method_exists($search_product, 'total') ? $search_product->total() : $search_product->count();
        $keyword = trim((string) ($search_keyword ?? ''));
        $filters = $search_filters ?? [];
        $pageMode = $search_page_mode ?? 'search';
        $selectedCategory = $filters['category_id'] ?? null;
        $selectedBrand = $filters['brand_id'] ?? null;
        $selectedSort = $filters['sort'] ?? 'popular';
        $priceMin = $filters['price_min'] ?? '';
        $priceMax = $filters['price_max'] ?? '';
        $searchPageTitle = $search_page_title ?? 'Kết quả tìm kiếm';
        $searchFormAction = $search_form_action ?? url('/tim-kiem');
        $searchClearUrl = $search_clear_url ?? $searchFormAction;
        $highlightProduct = $search_highlight_product ?? null;

        $allCategories = collect($search_categories ?? [])
            ->filter(fn ($item) => (int) ($item->category_status ?? 0) === 0);
        $allBrands = collect($search_brands ?? []);

        $pricePresets = [
            ['label' => 'Dưới 10 triệu',     'min' => 0,        'max' => 10000000,  'count' => 0],
            ['label' => '10 - 20 triệu',     'min' => 10000000, 'max' => 20000000,  'count' => 0],
            ['label' => '20 - 30 triệu',     'min' => 20000000, 'max' => 30000000,  'count' => 0],
            ['label' => '30 - 40 triệu',     'min' => 30000000, 'max' => 40000000,  'count' => 0],
            ['label' => 'Trên 40 triệu',     'min' => 40000000, 'max' => null,      'count' => 0],
        ];

        // Build a query-string helper that drops a given key for chip removal
        $currentQuery = request()->except(['page']);

        $buildRemoveUrl = function (array $remove = []) use ($searchFormAction, $currentQuery) {
            $params = $currentQuery;
            foreach ($remove as $key) {
                unset($params[$key]);
            }
            return $searchFormAction . (count($params) ? ('?' . http_build_query($params)) : '');
        };

        $sortLabels = [
            'popular' => 'Liên quan',
            'best_selling' => 'Bán chạy',
            'price_asc' => 'Giá thấp đến cao',
            'price_desc' => 'Giá cao đến thấp',
            'sale_desc' => 'Khuyến mãi nhiều nhất',
            'name_asc' => 'Tên A-Z',
            'name_desc' => 'Tên Z-A',
            'created_at_desc' => 'Mới nhất',
        ];

        $activeCategoryLabel = null;
        if ($selectedCategory) {
            $cat = $allCategories->firstWhere('category_id', (int) $selectedCategory);
            $activeCategoryLabel = $cat ? \App\Support\StorefrontLabel::category($cat->category_name, $cat->slug_category_product ?? null) : null;
        }
        $activeBrandLabel = null;
        if ($selectedBrand) {
            $br = $allBrands->firstWhere('brand_id', (int) $selectedBrand);
            $activeBrandLabel = $br ? \App\Support\StorefrontLabel::neutralizeSource($br->brand_name) : null;
        }

        $hasActivePriceFilter = $priceMin !== '' && $priceMin !== null || $priceMax !== '' && $priceMax !== null;
        $hasAnyActiveFilter = $keyword !== ''
            || !empty($filters['product_code'])
            || $selectedCategory
            || $selectedBrand
            || $hasActivePriceFilter
            || ($selectedSort && $selectedSort !== 'popular');

        $activePricePresetIndex = null;
        if ($hasActivePriceFilter) {
            foreach ($pricePresets as $idx => $p) {
                $minMatch = ((int) $priceMin) === (int) $p['min'];
                $maxMatch = ($p['max'] === null && ($priceMax === '' || $priceMax === null))
                    || ((int) $priceMax) === (int) ($p['max'] ?? -1);
                if ($minMatch && $maxMatch) {
                    $activePricePresetIndex = $idx;
                    break;
                }
            }
        }

        $maxVisibleCategories = 5;
        $maxVisibleBrands = 5;
    @endphp

    <main class="search-page">
        <div class="container">
            @include('partials.storefront.breadcrumb', [
                'class' => 'search-breadcrumb',
                'items' => [
                    [
                        'label' => 'Tìm kiếm',
                        'current' => true,
                    ],
                ],
            ])

            @if ($errors->has('keywords_submit'))
                <div class="alert alert-danger">{{ $errors->first('keywords_submit') }}</div>
            @endif

            <!-- Title row -->
            <div class="search-title-row">
                <div>
                    <h1 class="search-title">
                        @if ($keyword !== '')
                            Kết quả tìm kiếm cho <span class="keyword">"{{ $keyword }}"</span>
                        @else
                            {{ $searchPageTitle }}
                        @endif
                    </h1>
                    <p class="search-meta">Tìm thấy {{ number_format($searchCount) }} sản phẩm</p>
                </div>

                <form method="GET" action="{{ $searchFormAction }}" class="search-sort-wrap" id="search-sort-form">
                    @foreach ($currentQuery as $k => $v)
                        @if ($k !== 'sort')
                            <input type="hidden" name="{{ $k }}" value="{{ is_array($v) ? '' : $v }}">
                        @endif
                    @endforeach
                    <label for="search-sort">Sắp xếp:</label>
                    <select name="sort" id="search-sort" class="search-sort-select"
                        onchange="document.getElementById('search-sort-form').submit();">
                        @foreach ($sortLabels as $val => $lbl)
                            <option value="{{ $val }}" @selected($selectedSort === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Tools row -->
            <div class="search-tools">
                <div class="search-active-filters">
                    @if ($hasAnyActiveFilter)
                        <span class="label">Đang lọc:</span>

                        @if ($keyword !== '')
                            <a class="filter-chip" href="{{ $buildRemoveUrl(['keywords_submit']) }}">
                                {{ $keyword }} <i class="fa fa-times"></i>
                            </a>
                        @endif

                        @if (!empty($filters['product_code']))
                            <a class="filter-chip" href="{{ $buildRemoveUrl(['product_code']) }}">
                                Mã: {{ $filters['product_code'] }} <i class="fa fa-times"></i>
                            </a>
                        @endif

                        @if ($activeCategoryLabel)
                            <a class="filter-chip" href="{{ $buildRemoveUrl(['category_id']) }}">
                                {{ $activeCategoryLabel }} <i class="fa fa-times"></i>
                            </a>
                        @endif

                        @if ($activeBrandLabel)
                            <a class="filter-chip" href="{{ $buildRemoveUrl(['brand_id']) }}">
                                {{ $activeBrandLabel }} <i class="fa fa-times"></i>
                            </a>
                        @endif

                        @if ($hasActivePriceFilter)
                            <a class="filter-chip" href="{{ $buildRemoveUrl(['price_min', 'price_max']) }}">
                                {{ $priceMin !== '' && $priceMin !== null ? number_format((float) $priceMin) . 'đ' : '0đ' }}
                                -
                                {{ $priceMax !== '' && $priceMax !== null ? number_format((float) $priceMax) . 'đ' : 'Không giới hạn' }}
                                <i class="fa fa-times"></i>
                            </a>
                        @endif

                        <a class="clear-all-link" href="{{ $searchClearUrl }}">Xóa tất cả</a>
                    @else
                        <span class="label">Đang lọc: <em style="color:#94a3b8;font-style:normal;">không có bộ lọc</em></span>
                    @endif
                </div>

                <div class="search-view">
                    <label>Hiển thị:</label>
                    <div class="view-toggle" role="tablist" aria-label="Chế độ xem">
                        <button type="button" class="is-active" data-view="grid" aria-label="Xem dạng lưới">
                            <i class="fa fa-th"></i>
                        </button>
                        <button type="button" data-view="list" aria-label="Xem dạng danh sách">
                            <i class="fa fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Layout -->
            <div class="search-layout">
                <!-- Sidebar -->
                <aside class="search-sidebar">
                    <h3 class="sidebar-title"><i class="fa fa-filter"></i> BỘ LỌC TÌM KIẾM</h3>

                    <form method="GET" action="{{ $searchFormAction }}" id="filter-form">
                        @if ($keyword !== '')
                            <input type="hidden" name="keywords_submit" value="{{ $keyword }}">
                        @endif
                        @if (!empty($filters['product_code']))
                            <input type="hidden" name="product_code" value="{{ $filters['product_code'] }}">
                        @endif
                        @if ($selectedSort)
                            <input type="hidden" name="sort" value="{{ $selectedSort }}">
                        @endif
                        @if (!empty($filters['highlight_product']))
                            <input type="hidden" name="highlight_product" value="{{ $filters['highlight_product'] }}">
                        @endif

                        <!-- Category -->
                        @if ($allCategories->isNotEmpty())
                            <div class="filter-section">
                                <h4>Danh mục</h4>
                                <ul class="filter-list" data-collapsible="{{ $allCategories->count() > $maxVisibleCategories ? '1' : '0' }}">
                                    @foreach ($allCategories->values() as $idx => $cate)
                                        @php
                                            $isExtra = $idx >= $maxVisibleCategories;
                                            $catId = (int) $cate->category_id;
                                            $catLabel = \App\Support\StorefrontLabel::category($cate->category_name, $cate->slug_category_product ?? null);
                                        @endphp
                                        <li class="filter-item {{ $isExtra ? 'is-extra' : '' }}">
                                            <input type="radio" name="category_id" id="cat-{{ $catId }}"
                                                value="{{ $catId }}"
                                                @checked((int) $selectedCategory === $catId)
                                                onchange="document.getElementById('filter-form').submit()">
                                            <label class="filter-item-label" for="cat-{{ $catId }}">
                                                <span class="box"></span>
                                                <span class="filter-item-text">{{ $catLabel }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                                @if ($allCategories->count() > $maxVisibleCategories)
                                    <button type="button" class="filter-show-more" data-toggle-collapse>
                                        <span class="when-collapsed">Xem thêm <i class="fa fa-angle-down"></i></span>
                                        <span class="when-open" hidden>Thu gọn <i class="fa fa-angle-up"></i></span>
                                    </button>
                                @endif
                            </div>
                        @endif

                        <!-- Brand -->
                        @if ($allBrands->isNotEmpty())
                            <div class="filter-section">
                                <h4>Thương hiệu</h4>
                                <ul class="filter-list" data-collapsible="{{ $allBrands->count() > $maxVisibleBrands ? '1' : '0' }}">
                                    @foreach ($allBrands->values() as $idx => $br)
                                        @php
                                            $isExtra = $idx >= $maxVisibleBrands;
                                            $brId = (int) $br->brand_id;
                                            $brLabel = \App\Support\StorefrontLabel::neutralizeSource($br->brand_name);
                                        @endphp
                                        <li class="filter-item {{ $isExtra ? 'is-extra' : '' }}">
                                            <input type="radio" name="brand_id" id="br-{{ $brId }}"
                                                value="{{ $brId }}"
                                                @checked((int) $selectedBrand === $brId)
                                                onchange="document.getElementById('filter-form').submit()">
                                            <label class="filter-item-label" for="br-{{ $brId }}">
                                                <span class="box"></span>
                                                <span class="filter-item-text">{{ $brLabel }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                                @if ($allBrands->count() > $maxVisibleBrands)
                                    <button type="button" class="filter-show-more" data-toggle-collapse>
                                        <span class="when-collapsed">Xem thêm <i class="fa fa-angle-down"></i></span>
                                        <span class="when-open" hidden>Thu gọn <i class="fa fa-angle-up"></i></span>
                                    </button>
                                @endif
                            </div>
                        @endif

                        <!-- Price ranges -->
                        <div class="filter-section">
                            <h4>Khoảng giá</h4>
                            <ul class="filter-list">
                                @foreach ($pricePresets as $idx => $preset)
                                    <li class="filter-item is-radio">
                                        <input type="radio" name="price_preset" id="price-{{ $idx }}"
                                            value="{{ $idx }}"
                                            @checked($activePricePresetIndex === $idx)
                                            data-min="{{ $preset['min'] }}"
                                            data-max="{{ $preset['max'] ?? '' }}"
                                            onchange="(function(el){var f=document.getElementById('filter-form');document.getElementById('price-min-input').value=el.dataset.min||'';document.getElementById('price-max-input').value=el.dataset.max||'';f.submit();})(this)">
                                        <label class="filter-item-label" for="price-{{ $idx }}">
                                            <span class="box"></span>
                                            <span class="filter-item-text">{{ $preset['label'] }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="price-custom">
                                <div class="price-input">
                                    <input type="number" id="price-min-input" name="price_min" min="0"
                                        value="{{ $priceMin }}" placeholder="Từ">
                                </div>
                                <div class="price-input">
                                    <input type="number" id="price-max-input" name="price_max" min="0"
                                        value="{{ $priceMax }}" placeholder="Đến">
                                </div>
                                <button type="submit" class="price-apply">Áp dụng</button>
                            </div>
                        </div>

                        <div class="sidebar-actions">
                            <a class="btn-clear-all" href="{{ $searchClearUrl }}">
                                <i class="fa fa-trash-o"></i> Xóa tất cả
                            </a>
                            <button type="submit" class="btn-apply-filter">Áp dụng bộ lọc</button>
                        </div>
                    </form>
                </aside>

                <!-- Results -->
                <section class="search-results">
                    @if ($search_product->isEmpty())
                        <div class="empty-state">
                            <i class="fa fa-search"></i>
                            <h3>Không tìm thấy sản phẩm phù hợp</h3>
                            <p>Thử lại với từ khóa khác hoặc bỏ bớt bộ lọc để xem thêm sản phẩm.</p>
                            <a class="empty-action" href="{{ url('/') }}">
                                <i class="fa fa-home"></i> Về trang chủ
                            </a>
                        </div>
                    @else
                        <div class="product-grid" id="product-grid">
                            @foreach ($search_product as $product)
                                @php
                                    $productId = $product->product_id;
                                    $productName = \App\Support\StorefrontLabel::neutralizeSource($product->product_name);
                                    $productUrl = url('chi-tiet/' . $product->product_slug);
                                    $imagePath = \App\Support\AssetUrl::product($product->product_image);
                                    $finalPrice = $product->final_price ?? $product->product_price;
                                    $priceFormatted = number_format($finalPrice, 0, ',', '.') . 'đ';
                                    $hasOldPrice = ($product->sale_amount ?? 0) > 0;
                                    $oldPriceFormatted = $hasOldPrice
                                        ? number_format($product->product_price, 0, ',', '.') . 'đ'
                                        : null;
                                    $rating = isset($product->rating_avg) ? (float) $product->rating_avg : 4.8;
                                    $ratingCount = isset($product->rating_count) ? (int) $product->rating_count : null;
                                @endphp
                                <article class="product-card">
                                    <button type="button" class="wish-btn" data-tech-wishlist
                                        data-product-id="{{ $productId }}"
                                        data-product-name="{{ $productName }}"
                                        data-product-price="{{ $priceFormatted }}"
                                        data-product-image="{{ $imagePath }}"
                                        data-product-url="{{ $productUrl }}"
                                        aria-label="Thêm {{ $productName }} vào yêu thích">
                                        <i class="fa fa-heart-o"></i>
                                    </button>

                                    <a class="product-media" href="{{ $productUrl }}">
                                        <img src="{{ $imagePath }}" alt="{{ $productName }}" loading="lazy">
                                    </a>

                                    <a class="product-name" href="{{ $productUrl }}">{{ $productName }}</a>

                                    <div class="product-prices">
                                        <span class="product-price">{{ $priceFormatted }}</span>
                                        @if ($oldPriceFormatted)
                                            <span class="product-price-old">{{ $oldPriceFormatted }}</span>
                                        @endif
                                    </div>

                                    <div class="product-rating">
                                        <i class="fa fa-star"></i>
                                        <span>{{ number_format($rating, 1) }}</span>
                                        @if ($ratingCount !== null)
                                            <span class="count">({{ $ratingCount }})</span>
                                        @endif
                                    </div>

                                    <div class="product-tags">
                                        <span class="product-tag tag-new">Mới</span>
                                        <span class="product-tag tag-installment">Trả góp 0%</span>
                                        @if ($hasOldPrice)
                                            <span class="product-tag tag-sale">-{{ number_format($product->sale_amount, 0, ',', '.') }}đ</span>
                                        @endif
                                    </div>

                                    <div class="product-actions">
                                        <button type="button" class="btn-cart-icon"
                                            onclick="if(typeof Addtocart==='function'){Addtocart({{ $productId }});}"
                                            aria-label="Thêm vào giỏ">
                                            <i class="fa fa-shopping-cart"></i>
                                        </button>
                                        <a class="btn-detail" href="{{ $productUrl }}">Xem chi tiết</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        @php
                            $paginator = $search_product;
                            $hasPaginator = method_exists($paginator, 'lastPage');
                            $currentPage = $hasPaginator ? (int) $paginator->currentPage() : 1;
                            $lastPage = $hasPaginator ? (int) $paginator->lastPage() : 1;
                            $window = 2;
                            $pages = [];
                            if ($lastPage <= 7) {
                                for ($i = 1; $i <= $lastPage; $i++) $pages[] = $i;
                            } else {
                                $pages[] = 1;
                                if ($currentPage - $window > 2) $pages[] = '...';
                                for ($i = max(2, $currentPage - $window); $i <= min($lastPage - 1, $currentPage + $window); $i++) $pages[] = $i;
                                if ($currentPage + $window < $lastPage - 1) $pages[] = '...';
                                $pages[] = $lastPage;
                            }
                        @endphp

                        @if ($hasPaginator && $lastPage > 1)
                            <div class="pagination-row">
                                <div class="search-pagination">
                                    <ul>
                                        <li class="{{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                                            @if ($paginator->onFirstPage())
                                                <span><i class="fa fa-angle-left"></i></span>
                                            @else
                                                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left"></i></a>
                                            @endif
                                        </li>
                                        @foreach ($pages as $p)
                                            @if ($p === '...')
                                                <li class="disabled"><span>...</span></li>
                                            @elseif ($p === $currentPage)
                                                <li class="active"><span>{{ $p }}</span></li>
                                            @else
                                                <li><a href="{{ $paginator->url($p) }}">{{ $p }}</a></li>
                                            @endif
                                        @endforeach
                                        <li class="{{ ! $paginator->hasMorePages() ? 'disabled' : '' }}">
                                            @if (! $paginator->hasMorePages())
                                                <span><i class="fa fa-angle-right"></i></span>
                                            @else
                                                <a href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right"></i></a>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                                <div class="pagination-perpage">
                                    <span>Hiển thị:</span>
                                    <select disabled>
                                        <option>12 sản phẩm</option>
                                    </select>
                                </div>
                            </div>
                        @else
                            <div class="pagination-row">
                                <div></div>
                                <div class="pagination-perpage">
                                    <span>Hiển thị:</span>
                                    <select disabled>
                                        <option>12 sản phẩm</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                    @endif
                </section>
            </div>

            <!-- Trust badges -->
            <div class="trust-row">
                <div class="trust-item">
                    <span class="trust-icon"><i class="fa fa-truck"></i></span>
                    <div class="trust-text">
                        <strong>Giao hàng nhanh</strong>
                        <small>Giao nhanh nội thành trong 2h</small>
                    </div>
                </div>
                <div class="trust-item">
                    <span class="trust-icon"><i class="fa fa-check-circle"></i></span>
                    <div class="trust-text">
                        <strong>Sản phẩm chính hãng</strong>
                        <small>Cam kết 100% chính hãng</small>
                    </div>
                </div>
                <div class="trust-item">
                    <span class="trust-icon"><i class="fa fa-shield"></i></span>
                    <div class="trust-text">
                        <strong>Bảo hành toàn quốc</strong>
                        <small>Bảo hành tại tất cả TTBH</small>
                    </div>
                </div>
                <div class="trust-item">
                    <span class="trust-icon"><i class="fa fa-refresh"></i></span>
                    <div class="trust-text">
                        <strong>Đổi trả dễ dàng</strong>
                        <small>Đổi trả trong 7 ngày</small>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        (function () {
            // Show more / less for collapsible filter lists
            document.querySelectorAll('.filter-list[data-collapsible="1"]').forEach(function (list) {
                list.classList.add('is-collapsed');
            });

            document.querySelectorAll('[data-toggle-collapse]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var list = btn.previousElementSibling;
                    while (list && !list.classList.contains('filter-list')) {
                        list = list.previousElementSibling;
                    }
                    if (!list) return;
                    list.classList.toggle('is-collapsed');
                    var collapsed = list.classList.contains('is-collapsed');
                    var when1 = btn.querySelector('.when-collapsed');
                    var when2 = btn.querySelector('.when-open');
                    if (when1) when1.hidden = !collapsed;
                    if (when2) when2.hidden = collapsed;
                });
            });

            // Grid / list view toggle
            var grid = document.getElementById('product-grid');
            document.querySelectorAll('.view-toggle [data-view]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.view-toggle [data-view]').forEach(function (b) {
                        b.classList.remove('is-active');
                    });
                    btn.classList.add('is-active');
                    if (!grid) return;
                    if (btn.dataset.view === 'list') {
                        grid.classList.add('is-list');
                    } else {
                        grid.classList.remove('is-list');
                    }
                });
            });
        })();
    </script>
@endsection
