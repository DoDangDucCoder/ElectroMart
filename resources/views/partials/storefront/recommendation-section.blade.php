@php
    $title = $title ?? config('storefront.labels.recommend_for_you', 'Gợi ý cho bạn');
    $description = $description ?? null;
    $products = collect($products ?? [])->values();
    $cardColumnClass = $cardColumnClass ?? 'col-xs-12 col-sm-6 col-md-3';
    $emptyTitle = $emptyTitle ?? 'Chưa có sản phẩm gợi ý phù hợp';
    $hasProducts = $products->isNotEmpty();
    $recommendationGridClass = match (true) {
        $products->count() <= 1 => 'shopw-search-grid--single',
        $products->count() === 2 => 'shopw-search-grid--duo',
        default => 'shopw-search-grid--full',
    };
    $resolvedCardColumnClass = match (true) {
        $products->count() <= 1 => 'col-xs-12 shopw-search-featured-card',
        $products->count() === 2 => 'col-xs-12 col-sm-6',
        default => $cardColumnClass,
    };
    $fallbackLinks = [
        [
            'title' => 'Về trang chủ',
            'description' => 'Quay lại trang chủ để mở thêm danh mục và chiến dịch đang nổi bật.',
            'url' => url('/'),
            'icon' => 'fa-home',
        ],
        [
            'title' => 'Tiếp tục lọc sản phẩm',
            'description' => 'Mở bộ lọc tìm kiếm để thử lại với tên, mã hoặc khoảng giá khác.',
            'url' => url('/san-pham'),
            'icon' => 'fa-search',
        ],
        [
            'title' => config('storefront.labels.compare_products', 'So sánh sản phẩm'),
            'description' => 'Đặt vài mẫu lên cùng một bàn so sánh để ra quyết định nhanh hơn.',
            'url' => url('/san-pham'),
            'icon' => 'fa-refresh',
        ],
    ];
@endphp

<section
    class="shopw-recommendation-section {{ $hasProducts ? 'shopw-recommendation-section--products' : 'shopw-recommendation-section--fallback' }}">
    <div class="shopw-search-results-head">
        <div>
            <span class="shopw-search-kicker">Gợi ý sản phẩm</span>
            <h2>{{ $title }}</h2>
            @if ($description)
                <p>{{ $description }}</p>
            @endif
        </div>
    </div>

    @if ($hasProducts)
        <div class="row shopw-search-grid {{ $recommendationGridClass }}">
            @include('pages.category.partials.product_cards', [
                'products' => $products,
                'emptyMessage' => $emptyTitle,
                'emptyStateClass' => 'shopw-recommendation-empty',
                'cardColumnClass' => $resolvedCardColumnClass,
                'showQuickView' => true,
                'showCartToggle' => true,
                'showWishlist' => true,
                'showCompare' => true,
            ])
        </div>
    @else
        <div class="shopw-recommendation-fallback">
            @foreach ($fallbackLinks as $fallbackLink)
                <a href="{{ $fallbackLink['url'] }}" class="shopw-recommendation-fallback-card">
                    <span class="shopw-recommendation-fallback-icon">
                        <i class="fa {{ $fallbackLink['icon'] }}"></i>
                    </span>
                    <span class="shopw-recommendation-fallback-copy">
                        <strong>{{ $fallbackLink['title'] }}</strong>
                        <small>{{ $fallbackLink['description'] }}</small>
                    </span>
                    <span class="shopw-recommendation-fallback-arrow" aria-hidden="true">
                        <i class="fa fa-angle-right"></i>
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</section>

