@extends('layout')

@section('body_class', 'g-category')

@section('content_category')
    @php
        $storefrontLabel = \App\Support\StorefrontLabel::class;
        $activeCategory = $category_name->first();
        $activeCategoryLabel = $activeCategory
            ? $storefrontLabel::category($activeCategory->category_name, $activeCategory->slug_category_product ?? null)
            : null;
        $supportConfig = config('services.storefront_support', []);
        $supportPhoneDisplay = $supportConfig['phone_display'] ?? '1900.0000';
        $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:19000000';
        $categoryDescription = trim(strip_tags((string) optional($activeCategory)->category_desc));
        $productCount = method_exists($category_by_id, 'total') ? $category_by_id->total() : $category_by_id->count();
        $rangeMinLabel = $range_min_price > 0 ? number_format($range_min_price, 0, ',', '.') . 'đ' : 'Đang cập nhật';
        $rangeMaxLabel = $range_max_price > 0 ? number_format($range_max_price, 0, ',', '.') . 'đ' : 'Đang cập nhật';
        $selectedMinLabel = number_format((float) ($filter_start_price ?? 0), 0, ',', '.') . 'đ';
        $selectedMaxLabel = number_format((float) ($filter_end_price ?? 0), 0, ',', '.') . 'đ';
        $hasActivePriceFilter = request()->filled('start_price') || request()->filled('end_price');
        $categoryQuickLinks = [
            [
                'icon' => 'fa-phone',
                'label' => config('storefront.labels.hotline_consulting', 'Hotline tư vấn'),
                'value' => $supportPhoneDisplay,
                'url' => $supportPhoneHref,
            ],
            [
                'icon' => 'fa-random',
                'label' => config('storefront.labels.compare_products', 'So sánh sản phẩm'),
                'value' => config('storefront.copy.category_compare_value', 'Mở bảng so sánh nhanh'),
                'url' => url('/so-sanh'),
            ],
            [
                'icon' => 'fa-history',
                'label' => config('storefront.labels.order_tracking', 'Theo dõi đơn hàng'),
                'value' => 'Xem lịch sử mua sắm',
                'url' => url('/history'),
            ],
        ];
        $sortLabels = [
            'none' => 'Mặc định',
            'tang_dan' => 'Giá tăng dần',
            'giam_dan' => 'Giá giảm dần',
            'kytu_az' => 'Tên A-Z',
            'kytu_za' => 'Tên Z-A',
            'khuyen_mai' => 'Khuyến mãi nhiều nhất',
        ];
    @endphp

    <div class="shopw-category-page">
        <section class="shopw-category-hero">
            <div class="shopw-category-hero-copy">
                <div class="shopw-category-hero-copy-shell">
                    <span class="shopw-category-kicker">Danh mục sản phẩm</span>
                    <h1>{{ $activeCategoryLabel }}</h1>
                    @if ($categoryDescription !== '')
                        <p>{{ $categoryDescription }}</p>
                    @else
                        <p>Khám phá danh sách sản phẩm, lọc theo khoảng giá và sắp xếp theo nhu cầu mua sắm của bạn.</p>
                    @endif
                </div>

                <div class="shopw-category-meta">
                    <div class="shopw-category-meta-card">
                        <span>Sản phẩm hiển thị</span>
                        <strong>{{ number_format($productCount) }}</strong>
                        <small>Danh sách được cập nhật trực tiếp từ kho sản phẩm hiện có.</small>
                    </div>
                    <div class="shopw-category-meta-card">
                        <span>Khoảng giá</span>
                        <strong>{{ $rangeMinLabel }} - {{ $rangeMaxLabel }}</strong>
                        <small>Giúp bạn chốt nhanh mức chi phù hợp trước khi lọc sâu hơn.</small>
                    </div>
                    <div class="shopw-category-meta-card">
                        <span>Sắp xếp</span>
                        <strong>{{ $sortLabels[$selected_sort] ?? 'Mặc định' }}</strong>
                        <small>Ưu tiên theo giá hoặc tên sản phẩm tùy cách bạn muốn duyệt.</small>
                    </div>
                </div>
            </div>

            <div class="shopw-category-hero-actions">
                <div class="shopw-category-hero-rail">
                    <div class="shopw-category-hero-note">
                        <span class="shopw-category-hero-note-label">Tư vấn nhanh</span>
                        <strong>Chưa biết chọn gì?</strong>
                        <p>Gọi ngay {{ $supportPhoneDisplay }} hoặc mở bảng so sánh để chốt cấu hình phù hợp.</p>
                    </div>

                    <div class="shopw-category-hero-links">
                        <a href="{{ url('/') }}" class="shopw-category-action-link">
                            <i class="fa fa-home"></i>Về trang chủ
                        </a>
                        <a href="{{ $supportPhoneHref }}" class="shopw-category-action-link is-hotline">
                            <i class="fa fa-phone"></i>{{ $supportPhoneDisplay }}
                        </a>
                    </div>
                </div>

                <div class="shopw-category-share">
                    <div class="fb-share-button" data-href="{{ $url_canonical }}" data-layout="button_count"
                        data-size="small">
                        <a target="_blank"
                            href="https://www.facebook.com/sharer/sharer.php?u={{ $url_canonical }}&amp;src=sdkpreparse"
                            rel="noopener" class="fb-xfbml-parse-ignore">Chia sẻ</a>
                    </div>
                    <div class="fb-like" data-href="{{ $url_canonical }}" data-width=""
                        data-layout="button_count" data-action="like" data-size="small" data-share="false"></div>
                </div>
            </div>
        </section>

        <section class="shopw-category-toolbar">
            <div class="shopw-category-toolbar-card">
                <label for="sort" class="shopw-category-toolbar-label">Sắp xếp theo</label>
                <select name="sort" id="sort" class="form-control">
                    <option value="{{ request()->fullUrlWithoutQuery(['sort_by']) }}"
                        {{ $selected_sort === 'none' ? 'selected' : '' }}>
                        Mặc định
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'tang_dan']) }}"
                        {{ $selected_sort === 'tang_dan' ? 'selected' : '' }}>
                        Giá tăng dần
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'giam_dan']) }}"
                        {{ $selected_sort === 'giam_dan' ? 'selected' : '' }}>
                        Giá giảm dần
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'kytu_az']) }}"
                        {{ $selected_sort === 'kytu_az' ? 'selected' : '' }}>
                        Tên A đến Z
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'kytu_za']) }}"
                        {{ $selected_sort === 'kytu_za' ? 'selected' : '' }}>
                        Tên Z đến A
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'khuyen_mai']) }}"
                        {{ $selected_sort === 'khuyen_mai' ? 'selected' : '' }}>
                        Khuyến mãi nhiều nhất
                    </option>
                </select>
            </div>

            <div class="shopw-category-toolbar-card shopw-category-filter-card">
                <div class="shopw-category-filter-head">
                    <div>
                        <div class="shopw-category-toolbar-label">Lọc theo khoảng giá</div>
                        <p>Thu hẹp danh sách để tập trung vào dải giá bạn muốn mua ngay hôm nay.</p>
                    </div>
                    @if ($hasActivePriceFilter)
                        <span class="shopw-category-filter-chip">{{ $selectedMinLabel }} - {{ $selectedMaxLabel }}</span>
                    @endif
                </div>

                <form method="GET" action="{{ $url_canonical }}" class="shopw-category-filter-form">
                    @if ($selected_sort !== 'none')
                        <input type="hidden" name="sort_by" value="{{ $selected_sort }}">
                    @endif

                    <div id="slider-range"></div>

                    <div class="shopw-category-range-values">
                        <label>
                            <span>Từ</span>
                            <input type="text" id="amount_start" readonly value="{{ $filter_start_price }}">
                        </label>
                        <label>
                            <span>Đến</span>
                            <input type="text" id="amount_end" readonly value="{{ $filter_end_price }}">
                        </label>
                    </div>

                    <input type="hidden" name="start_price" id="start_price" value="{{ $filter_start_price }}">
                    <input type="hidden" name="end_price" id="end_price" value="{{ $filter_end_price }}">

                    <button type="submit" name="filter_price" class="btn btn-default shopw-category-filter-submit">
                        <i class="fa fa-sliders"></i>Áp dụng mức giá
                    </button>
                </form>
            </div>

            <aside class="shopw-category-toolbar-card shopw-category-service-card">
                <div class="shopw-category-filter-head">
                    <div>
                        <div class="shopw-category-toolbar-label">Hỗ trợ mua nhanh</div>
                        <p>Giữ nhịp mua sắm liên tục với các lối tắt hỗ trợ thiết thực ngay trong quá trình chọn hàng.</p>
                    </div>
                </div>

                <div class="shopw-category-service-list">
                    @foreach ($categoryQuickLinks as $quickLink)
                        <a href="{{ $quickLink['url'] }}" class="shopw-category-service-item">
                            <span class="shopw-category-service-icon">
                                <i class="fa {{ $quickLink['icon'] }}"></i>
                            </span>
                            <span class="shopw-category-service-copy">
                                <strong>{{ $quickLink['label'] }}</strong>
                                <small>{{ $quickLink['value'] }}</small>
                            </span>
                            <span class="shopw-category-service-arrow">
                                <i class="fa fa-angle-right"></i>
                            </span>
                        </a>
                    @endforeach
                </div>
            </aside>
        </section>

        <section class="shopw-category-results">
            <div class="shopw-category-results-head">
                <div>
                    <span class="shopw-category-kicker">Kết quả hiển thị</span>
                    <h2>{{ $activeCategoryLabel }}</h2>
                    <p>{{ number_format($productCount) }} sản phẩm đang được hiển thị trong danh sách này.</p>
                </div>
                <div class="shopw-category-results-tags">
                    <span class="shopw-category-result-tag">Sắp xếp: {{ $sortLabels[$selected_sort] ?? 'Mặc định' }}</span>
                    @if ($hasActivePriceFilter)
                        <span class="shopw-category-result-tag">Giá: {{ $selectedMinLabel }} - {{ $selectedMaxLabel }}</span>
                    @endif
                </div>
            </div>

            @include('pages.category.partials.product_grid', [
                'products' => $category_by_id,
                'emptyMessage' => 'Hiện chưa có sản phẩm nào trong danh mục này.',
                'cardColumnClass' => 'col-xs-12 col-sm-6 col-md-4 col-lg-3',
                'showWishlist' => true,
                'showCompare' => true,
            ])
        </section>
    </div>
@endsection
