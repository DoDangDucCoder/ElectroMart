@extends('layouts.home-tech')

@section('body_class', 'g-category')

@section('content')
    @php
        $storefrontLabel = \App\Support\StorefrontLabel::class;
        $activeCategory = $category_name->first();
        $activeCategoryLabel = $activeCategory
            ? $storefrontLabel::category($activeCategory->category_name, $activeCategory->slug_category_product ?? null)
            : 'Sản phẩm';

        $productCount = method_exists($category_by_id, 'total') ? $category_by_id->total() : $category_by_id->count();
        $perPage = method_exists($category_by_id, 'perPage') ? $category_by_id->perPage() : 12;
        $currentPage = method_exists($category_by_id, 'currentPage') ? $category_by_id->currentPage() : 1;
        $fromIndex = $productCount > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
        $toIndex = $productCount > 0 ? min($currentPage * $perPage, $productCount) : 0;

        $allCategoryItems = collect($category ?? [])
            ->filter(fn($c) => (int)($c->category_status ?? $c->is_active ?? 1) >= 0);

        $activeId = (int) optional($activeCategory)->category_id;
        $activeParentId = (int) optional($activeCategory)->category_parent;

        // Sibling categories (same parent) — fall back to children, then top-level
        $sidebarCategories = $allCategoryItems
            ->filter(fn($c) => (int) ($c->category_parent ?? 0) === $activeParentId && (int) $c->category_id !== 0)
            ->values();
        if ($sidebarCategories->count() <= 1) {
            $sidebarCategories = $allCategoryItems
                ->filter(fn($c) => (int) ($c->category_parent ?? 0) === $activeId)
                ->values();
        }
        if ($sidebarCategories->isEmpty()) {
            $sidebarCategories = $allCategoryItems
                ->filter(fn($c) => (int) ($c->category_parent ?? 0) === 0)
                ->values();
        }

        $categoryIcons = [
            'gaming' => 'fa-gamepad',
            'office' => 'fa-briefcase',
            'ultrabook' => 'fa-leaf',
            '2-in-1' => 'fa-refresh',
            'mini' => 'fa-cube',
            'macbook' => 'fa-apple',
            'pc' => 'fa-desktop',
            'man-hinh' => 'fa-television',
            'ban-phim' => 'fa-keyboard-o',
            'chuot' => 'fa-mouse-pointer',
            'tai-nghe' => 'fa-headphones',
        ];
        $resolveIcon = function ($name, $slug) use ($categoryIcons) {
            $haystack = strtolower(($name ?? '') . ' ' . ($slug ?? ''));
            foreach ($categoryIcons as $needle => $icon) {
                if (str_contains($haystack, $needle)) {
                    return $icon;
                }
            }
            return 'fa-laptop';
        };

        $brandCollection = collect($brand ?? [])->take(10);

        $useNeeds = [
            ['label' => 'Gaming - Đồ họa', 'slug' => 'gaming'],
            ['label' => 'Văn phòng - Học tập', 'slug' => 'van-phong'],
            ['label' => 'Đồ họa - Kỹ thuật', 'slug' => 'do-hoa'],
            ['label' => 'Doanh nhân - Mỏng nhẹ', 'slug' => 'mong-nhe'],
            ['label' => 'Lập trình - IT', 'slug' => 'lap-trinh'],
        ];
        $stockOptions = [
            ['label' => 'Tất cả', 'value' => ''],
            ['label' => 'Mới 100%', 'value' => 'new'],
            ['label' => 'Like New', 'value' => 'like_new'],
            ['label' => 'Trưng bày', 'value' => 'display'],
        ];
        $sortRadios = [
            'none' => 'Phổ biến',
            'moi_nhat' => 'Mới nhất',
            'tang_dan' => 'Giá thấp đến cao',
            'giam_dan' => 'Giá cao đến thấp',
            'danh_gia' => 'Đánh giá cao',
        ];
        $sortLabels = [
            'none' => 'Phổ biến',
            'tang_dan' => 'Giá thấp đến cao',
            'giam_dan' => 'Giá cao đến thấp',
            'kytu_az' => 'Tên A-Z',
            'kytu_za' => 'Tên Z-A',
            'khuyen_mai' => 'Khuyến mãi nhiều nhất',
        ];

        $rangeMin = (float) ($range_min_price ?? 0);
        $rangeMax = (float) ($range_max_price ?? 50000000);
        $selectedMin = (float) ($filter_start_price ?? $rangeMin);
        $selectedMax = (float) ($filter_end_price ?? $rangeMax);
        $hasActivePriceFilter = request()->filled('start_price') || request()->filled('end_price');

        $fmtPrice = function ($value) {
            $value = (float) $value;
            if ($value >= 1000000) {
                $millions = $value / 1000000;
                $rounded = ($millions == (int) $millions) ? (int) $millions : number_format($millions, 1, ',', '');
                return $rounded . 'tr' . ($value > 0 && $value % 1000000 === 0 ? '' : '');
            }
            if ($value <= 0) {
                return '0đ';
            }
            return number_format($value, 0, ',', '.') . 'đ';
        };
    @endphp

    <section class="cat-x">
        <div class="cat-x__container">

            {{-- Breadcrumb --}}
            <nav class="cat-x__breadcrumb" aria-label="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="sep">/</span>
                <a href="{{ url('/san-pham') }}">Sản phẩm</a>
                <span class="sep">/</span>
                <span class="current">{{ $activeCategoryLabel }}</span>
            </nav>

            {{-- Title --}}
            <header class="cat-x__title-block">
                <h1>{{ $activeCategoryLabel }}</h1>
                <p>Khám phá {{ number_format($productCount) }} sản phẩm chính hãng với giá tốt, bảo hành uy tín</p>
            </header>

            {{-- Category quick chips --}}
            @if ($sidebarCategories->isNotEmpty())
                <div class="cat-x__chips-wrap">
                    <button type="button" class="cat-x__chips-nav is-prev" aria-label="Cuộn trái" data-cat-chips-prev>
                        <i class="fa fa-angle-left"></i>
                    </button>
                    <div class="cat-x__chips" data-cat-chips>
                        @php
                            // First chip = "Tất cả ..." pointing to current category
                            $firstLabel = 'Tất cả ' . ($activeCategoryLabel);
                            $isFirstActive = !request()->has('child');
                        @endphp
                        <a href="{{ url('/danh-muc/' . optional($activeCategory)->slug_category_product) }}"
                            class="cat-x__chip {{ $isFirstActive ? 'is-active' : '' }}">
                            <span class="cat-x__chip-icon">
                                <i class="fa {{ $resolveIcon($activeCategoryLabel, optional($activeCategory)->slug_category_product) }}"></i>
                            </span>
                            <span class="cat-x__chip-text">
                                <strong>{{ $firstLabel }}</strong>
                                <span>{{ number_format($productCount) }}</span>
                            </span>
                        </a>
                        @foreach ($sidebarCategories as $cat)
                            @continue((int) $cat->category_id === $activeId)
                            @php
                                $catLabel = $storefrontLabel::category($cat->category_name, $cat->slug_category_product);
                            @endphp
                            <a href="{{ url('/danh-muc/' . $cat->slug_category_product) }}"
                                class="cat-x__chip">
                                <span class="cat-x__chip-icon">
                                    <i class="fa {{ $resolveIcon($cat->category_name, $cat->slug_category_product) }}"></i>
                                </span>
                                <span class="cat-x__chip-text">
                                    <strong>{{ $catLabel }}</strong>
                                    <span>—</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                    <button type="button" class="cat-x__chips-nav is-next" aria-label="Cuộn phải" data-cat-chips-next>
                        <i class="fa fa-angle-right"></i>
                    </button>
                </div>
            @endif

            {{-- Promo banner --}}
            <div class="cat-x__promo">
                <div class="cat-x__promo-copy">
                    <h3>TRẢ GÓP 0% – DỄ DÀNG SỞ HỮU</h3>
                    <p>Hỗ trợ trả góp qua thẻ tín dụng &amp; CMND/CCCD</p>
                    <a href="{{ url('/lien-he') }}" class="cat-x__promo-cta">Xem ngay</a>
                </div>
                <div class="cat-x__promo-zero">0%</div>
                <div class="cat-x__promo-warranty">
                    <i class="fa fa-shield"></i>
                    <span>BẢO HÀNH<br>CHÍNH HÃNG</span>
                </div>
            </div>

            {{-- Layout: sidebar + main --}}
            <div class="cat-x__layout">

                {{-- Sidebar --}}
                <aside class="cat-x__sidebar">

                    {{-- Danh mục --}}
                    <div class="cat-x__panel">
                        <h4>Danh mục</h4>
                        <ul class="cat-x__filter-list">
                            <li>
                                <a href="{{ url('/danh-muc/' . optional($activeCategory)->slug_category_product) }}"
                                    class="cat-x__filter-item is-active">
                                    <span class="ck"></span>
                                    <span class="lb">Tất cả {{ $activeCategoryLabel }}</span>
                                    <span class="cnt">{{ number_format($productCount) }}</span>
                                </a>
                            </li>
                            @foreach ($sidebarCategories->take(6) as $cat)
                                @continue((int) $cat->category_id === $activeId)
                                @php
                                    $catLabel = $storefrontLabel::category($cat->category_name, $cat->slug_category_product);
                                @endphp
                                <li>
                                    <a href="{{ url('/danh-muc/' . $cat->slug_category_product) }}"
                                        class="cat-x__filter-item">
                                        <span class="ck"></span>
                                        <span class="lb">{{ $catLabel }}</span>
                                        <span class="cnt">—</span>
                                    </a>
                                </li>
                            @endforeach
                            @if ($sidebarCategories->count() > 6)
                                <li><a href="#" class="cat-x__see-more">Xem thêm</a></li>
                            @endif
                        </ul>
                    </div>

                    {{-- Khoảng giá --}}
                    <div class="cat-x__panel">
                        <h4>Khoảng giá</h4>
                        <form method="GET" action="{{ $url_canonical }}" id="cat-x-price-form">
                            @if ($selected_sort !== 'none')
                                <input type="hidden" name="sort_by" value="{{ $selected_sort }}">
                            @endif

                            <div class="cat-x__price-range">
                                <strong>{{ $fmtPrice($rangeMin) }}</strong>
                                <strong>{{ $fmtPrice($rangeMax) }}+</strong>
                            </div>
                            <div id="slider-range" class="cat-x__slider"></div>
                            <div class="cat-x__price-ticks">
                                <span>{{ $fmtPrice($rangeMin) }}</span>
                                <span>{{ $fmtPrice(($rangeMin + $rangeMax) / 2) }}</span>
                                <span>{{ $fmtPrice($rangeMax) }}+</span>
                            </div>

                            <input type="hidden" name="start_price" id="start_price" value="{{ (int) $selectedMin }}">
                            <input type="hidden" name="end_price" id="end_price" value="{{ (int) $selectedMax }}">
                            <input type="text" id="amount_start" hidden value="{{ (int) $selectedMin }}">
                            <input type="text" id="amount_end" hidden value="{{ (int) $selectedMax }}">

                            <div class="cat-x__price-buckets">
                                <a href="{{ request()->fullUrlWithQuery(['start_price' => 0, 'end_price' => 10000000]) }}"
                                    class="cat-x__price-bucket">Dưới 10 triệu</a>
                                <a href="{{ request()->fullUrlWithQuery(['start_price' => 10000000, 'end_price' => 20000000]) }}"
                                    class="cat-x__price-bucket">10 - 20 triệu</a>
                                <a href="{{ request()->fullUrlWithQuery(['start_price' => 20000000, 'end_price' => 30000000]) }}"
                                    class="cat-x__price-bucket">20 - 30 triệu</a>
                                <a href="{{ request()->fullUrlWithQuery(['start_price' => 30000000, 'end_price' => 50000000]) }}"
                                    class="cat-x__price-bucket">30 - 50 triệu</a>
                                <a href="{{ request()->fullUrlWithQuery(['start_price' => 50000000, 'end_price' => 999000000]) }}"
                                    class="cat-x__price-bucket cat-x__price-bucket--full">Trên 50 triệu</a>
                            </div>
                        </form>
                    </div>

                    {{-- Thương hiệu --}}
                    @if ($brandCollection->isNotEmpty())
                        <div class="cat-x__panel">
                            <h4>Thương hiệu</h4>
                            <ul class="cat-x__filter-list">
                                @foreach ($brandCollection->take(7) as $brandItem)
                                    @php
                                        $brandSlug = $brandItem->brand_slug ?? \Illuminate\Support\Str::slug($brandItem->brand_name);
                                        $brandActive = request('brand') === $brandSlug;
                                    @endphp
                                    <li>
                                        <a href="{{ request()->fullUrlWithQuery(['brand' => $brandActive ? null : $brandSlug]) }}"
                                            class="cat-x__filter-item {{ $brandActive ? 'is-active' : '' }}">
                                            <span class="ck"></span>
                                            <span class="lb">{{ $brandItem->brand_name }}</span>
                                            <span class="cnt">—</span>
                                        </a>
                                    </li>
                                @endforeach
                                @if ($brandCollection->count() > 7)
                                    <li><a href="#" class="cat-x__see-more">Xem thêm</a></li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    {{-- Nhu cầu sử dụng --}}
                    <div class="cat-x__panel">
                        <h4>Nhu cầu sử dụng</h4>
                        <ul class="cat-x__filter-list">
                            @foreach ($useNeeds as $need)
                                @php $needActive = request('use') === $need['slug']; @endphp
                                <li>
                                    <a href="{{ request()->fullUrlWithQuery(['use' => $needActive ? null : $need['slug']]) }}"
                                        class="cat-x__filter-item {{ $needActive ? 'is-active' : '' }}">
                                        <span class="ck"></span>
                                        <span class="lb">{{ $need['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Tình trạng hàng --}}
                    <div class="cat-x__panel">
                        <h4>Tình trạng hàng</h4>
                        <ul class="cat-x__filter-list">
                            @foreach ($stockOptions as $option)
                                @php $stockActive = (string) request('stock', '') === $option['value']; @endphp
                                <li>
                                    <a href="{{ request()->fullUrlWithQuery(['stock' => $option['value'] ?: null]) }}"
                                        class="cat-x__filter-item cat-x__filter-radio {{ $stockActive ? 'is-active' : '' }}">
                                        <span class="rd"></span>
                                        <span class="lb">{{ $option['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Sắp xếp theo --}}
                    <div class="cat-x__panel">
                        <h4>Sắp xếp theo</h4>
                        <ul class="cat-x__filter-list">
                            @foreach ($sortRadios as $sortKey => $sortLabel)
                                @php
                                    $sortActive = $selected_sort === $sortKey;
                                    $sortUrl = $sortKey === 'none'
                                        ? request()->fullUrlWithoutQuery(['sort_by'])
                                        : request()->fullUrlWithQuery(['sort_by' => $sortKey]);
                                @endphp
                                <li>
                                    <a href="{{ $sortUrl }}"
                                        class="cat-x__filter-item cat-x__filter-radio {{ $sortActive ? 'is-active' : '' }}">
                                        <span class="rd"></span>
                                        <span class="lb">{{ $sortLabel }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <a href="{{ url()->current() }}" class="cat-x__clear-filter">Xóa tất cả bộ lọc</a>
                </aside>

                {{-- Main --}}
                <div class="cat-x__main">
                    <div class="cat-x__toolbar">
                        <div class="cat-x__count">
                            Hiển thị {{ $fromIndex }}-{{ $toIndex }} trong {{ number_format($productCount) }} sản phẩm
                        </div>

                        <div class="cat-x__view-toggle" role="tablist" aria-label="Chế độ hiển thị">
                            <button type="button" class="is-active" data-cat-view="grid" aria-label="Lưới">
                                <i class="fa fa-th"></i>
                            </button>
                            <button type="button" data-cat-view="list" aria-label="Danh sách">
                                <i class="fa fa-list"></i>
                            </button>
                        </div>

                        <div class="cat-x__sort-wrap">
                            <span>Sắp xếp:</span>
                            <select id="cat-x-sort" onchange="if(this.value){window.location.href=this.value;}">
                                <option value="{{ request()->fullUrlWithoutQuery(['sort_by']) }}"
                                    {{ $selected_sort === 'none' ? 'selected' : '' }}>Phổ biến</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'tang_dan']) }}"
                                    {{ $selected_sort === 'tang_dan' ? 'selected' : '' }}>Giá thấp đến cao</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'giam_dan']) }}"
                                    {{ $selected_sort === 'giam_dan' ? 'selected' : '' }}>Giá cao đến thấp</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'kytu_az']) }}"
                                    {{ $selected_sort === 'kytu_az' ? 'selected' : '' }}>Tên A-Z</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'kytu_za']) }}"
                                    {{ $selected_sort === 'kytu_za' ? 'selected' : '' }}>Tên Z-A</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'khuyen_mai']) }}"
                                    {{ $selected_sort === 'khuyen_mai' ? 'selected' : '' }}>Khuyến mãi nhiều nhất</option>
                            </select>
                        </div>
                    </div>

                    {{-- Product grid --}}
                    <div class="cat-x__grid" data-cat-grid>
                        @forelse ($category_by_id as $product)
                            @include('pages.category.partials.product_card_x', ['product' => $product])
                        @empty
                            <div style="grid-column: 1 / -1;">
                                @include('partials.storefront.empty-state', [
                                    'title' => 'Hiện chưa có sản phẩm nào trong danh mục này.',
                                    'message' => 'Hãy thử chuyển danh mục, thay đổi bộ lọc hoặc quay lại trang chủ.',
                                    'icon' => 'fa fa-cubes',
                                    'compact' => true,
                                ])
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if ($category_by_id instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $category_by_id->lastPage() > 1)
                        <div class="cat-x__pagination">
                            {{ $category_by_id->onEachSide(1)->links() }}
                        </div>
                    @endif

                    {{-- Recently viewed --}}
                    <div class="cat-x__recent" id="cat-x-recent" style="display:none;">
                        <div class="cat-x__recent-head">
                            <h3>Sản phẩm đã xem gần đây</h3>
                            <a href="{{ url('/san-pham') }}">Xem tất cả →</a>
                        </div>
                        <div class="cat-x__recent-track-wrap">
                            <button type="button" class="cat-x__recent-nav is-prev" aria-label="Cuộn trái" data-recent-prev>
                                <i class="fa fa-angle-left"></i>
                            </button>
                            <div class="cat-x__recent-track" data-recent-track></div>
                            <button type="button" class="cat-x__recent-nav is-next" aria-label="Cuộn phải" data-recent-next>
                                <i class="fa fa-angle-right"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Brand partners --}}
                    @if ($brandCollection->isNotEmpty())
                        <div class="cat-x__brands">
                            <div class="cat-x__brands-head">
                                <i class="fa fa-handshake-o"></i>THƯƠNG HIỆU ĐỐI TÁC
                            </div>
                            <div class="cat-x__brands-grid">
                                @foreach ($brandCollection->take(8) as $brandItem)
                                    @php
                                        $brandLogoUrl = $brandItem->logo_path ? \App\Support\AssetUrl::resolve($brandItem->logo_path, null) : null;
                                    @endphp
                                    @if ($brandLogoUrl)
                                        <img src="{{ $brandLogoUrl }}" alt="{{ $brandItem->brand_name }}">
                                    @else
                                        <span class="brand-text">{{ strtoupper($brandItem->brand_name) }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <script>
        (function () {
            'use strict';

            // Chips horizontal scroll buttons
            const chipsTrack = document.querySelector('[data-cat-chips]');
            if (chipsTrack) {
                const prev = document.querySelector('[data-cat-chips-prev]');
                const next = document.querySelector('[data-cat-chips-next]');
                const update = () => {
                    if (prev) prev.disabled = chipsTrack.scrollLeft <= 4;
                    if (next) next.disabled = chipsTrack.scrollLeft + chipsTrack.clientWidth >= chipsTrack.scrollWidth - 4;
                };
                if (prev) prev.addEventListener('click', () => chipsTrack.scrollBy({ left: -260, behavior: 'smooth' }));
                if (next) next.addEventListener('click', () => chipsTrack.scrollBy({ left: 260, behavior: 'smooth' }));
                chipsTrack.addEventListener('scroll', update, { passive: true });
                window.addEventListener('resize', update);
                update();
            }

            // View toggle (grid / list)
            const grid = document.querySelector('[data-cat-grid]');
            document.querySelectorAll('[data-cat-view]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('[data-cat-view]').forEach((b) => b.classList.remove('is-active'));
                    btn.classList.add('is-active');
                    if (btn.getAttribute('data-cat-view') === 'list') {
                        grid.classList.add('is-list');
                    } else {
                        grid.classList.remove('is-list');
                    }
                });
            });

            // Recently viewed (read from localStorage 'recent-products')
            try {
                const raw = localStorage.getItem('recent-products');
                const parsed = raw ? JSON.parse(raw) : [];
                if (Array.isArray(parsed) && parsed.length) {
                    const wrap = document.getElementById('cat-x-recent');
                    const track = wrap.querySelector('[data-recent-track]');
                    parsed.slice(0, 12).forEach((p) => {
                        const a = document.createElement('a');
                        a.className = 'cat-x__recent-card';
                        a.href = p.url || '#';
                        a.innerHTML = `
                            <button type="button" class="like" aria-label="Yêu thích"><i class="fa fa-heart-o"></i></button>
                            <div class="img"><img src="${p.image}" alt=""></div>
                            <div class="ttl">${p.name || ''}</div>
                            <div class="pr">${p.price || ''}</div>
                            <div class="rt">
                                <span class="stars">★★★★<span class="o">★</span></span>
                                <button type="button" class="add" aria-label="Thêm vào giỏ"><i class="fa fa-shopping-cart"></i></button>
                            </div>
                        `;
                        track.appendChild(a);
                    });
                    wrap.style.display = '';
                    const prev = wrap.querySelector('[data-recent-prev]');
                    const next = wrap.querySelector('[data-recent-next]');
                    if (prev) prev.addEventListener('click', () => track.scrollBy({ left: -260, behavior: 'smooth' }));
                    if (next) next.addEventListener('click', () => track.scrollBy({ left: 260, behavior: 'smooth' }));
                }
            } catch (e) { /* ignore */ }
        })();
    </script>

    {{-- Price slider (jQuery UI) --}}
    @push('scripts')
        <script>
            $(function () {
                if (!$.fn.slider || !document.getElementById('slider-range')) return;
                $('#slider-range').slider({
                    range: true,
                    min: {{ (int) $rangeMin }},
                    max: {{ (int) max($rangeMax, $rangeMin + 1) }},
                    values: [{{ (int) $selectedMin }}, {{ (int) $selectedMax }}],
                    slide: function (event, ui) {
                        $('#start_price').val(ui.values[0]);
                        $('#end_price').val(ui.values[1]);
                    },
                    change: function (event, ui) {
                        $('#start_price').val(ui.values[0]);
                        $('#end_price').val(ui.values[1]);
                        $('#cat-x-price-form').submit();
                    }
                });
            });
        </script>
    @endpush
@endsection
