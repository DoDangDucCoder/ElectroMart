@php
    $products = collect($products ?? [])->values();
    $query = trim((string) data_get($searchSuggest ?? [], 'query', ''));
    $trendingKeywords = collect(data_get($searchSuggest ?? [], 'trending_keywords', []))->values();
    $shortcutLinks = collect(data_get($searchSuggest ?? [], 'shortcut_links', []))->values();
    $utilityLinks = collect(data_get($searchSuggest ?? [], 'utility_links', []))->values();
    $searchResultsUrl = data_get($searchSuggest ?? [], 'search_results_url', url('/san-pham'));
    $hasProducts = $products->isNotEmpty();
    $showHelperState = blank($query) || mb_strlen($query) < 2;
    $showEmptyState = !$hasProducts && !$showHelperState;
@endphp

<div class="shopw-search-suggest-panel {{ $hasProducts ? 'is-rich-results' : 'is-helper-state' }}">
    <div class="shopw-search-suggest-layout">
        <aside class="shopw-search-suggest-side">
            @if ($trendingKeywords->isNotEmpty())
                <section class="shopw-search-suggest-group">
                    <span class="shopw-search-suggest-group-title">Tìm kiếm phổ biến</span>
                    <div class="shopw-search-suggest-chips">
                        @foreach ($trendingKeywords as $keyword)
                            <button type="button" class="shopw-search-suggest-chip li_search_ajax"
                                data-search-keyword="{{ $keyword }}">
                                <i class="fa fa-search"></i>
                                <span>{{ $keyword }}</span>
                            </button>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($shortcutLinks->isNotEmpty())
                <section class="shopw-search-suggest-group">
                    <span class="shopw-search-suggest-group-title">Đi nhanh theo danh mục</span>
                    <div class="shopw-search-suggest-links">
                        @foreach ($shortcutLinks as $link)
                            @php
                                $shortcutSubtitles = [
                                    'fa-gamepad' => 'Hiệu năng đỉnh cao',
                                    'fa-laptop' => 'Văn phòng & học tập',
                                    'fa-briefcase' => 'Làm việc mỗi ngày',
                                    'fa-mobile' => 'Smartphone chính hãng',
                                    'fa-apple' => 'Thiết bị Apple',
                                    'fa-android' => 'Android đa lựa chọn',
                                    'fa-headphones' => 'Chuột, bàn phím, tai nghe...',
                                    'fa-plug' => 'Sạc, cáp và phụ kiện',
                                    'fa-shield' => 'Bảo vệ thiết bị',
                                ];
                                $shortcutSubtitle = $link['subtitle'] ?? ($shortcutSubtitles[$link['icon'] ?? ''] ?? 'Khám phá sản phẩm');
                            @endphp
                            <a href="{{ $link['url'] }}" class="shopw-search-suggest-shortcut">
                                <i class="fa {{ $link['icon'] }}"></i>
                                <span>
                                    <strong>{{ $link['label'] }}</strong>
                                    <small>{{ $shortcutSubtitle }}</small>
                                </span>
                                <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($utilityLinks->isNotEmpty())
                <section class="shopw-search-suggest-group">
                    <span class="shopw-search-suggest-group-title">Lối vào nhanh</span>
                    <div class="shopw-search-suggest-links">
                        @foreach ($utilityLinks as $link)
                            <a href="{{ $link['url'] }}" class="shopw-search-suggest-shortcut">
                                <i class="fa {{ $link['icon'] }}"></i>
                                <span>
                                    <strong>{{ $link['label'] }}</strong>
                                    <small>Đi tới trang liên quan</small>
                                </span>
                                <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </aside>

        <div class="shopw-search-suggest-main">
            @if ($hasProducts)
                <div class="shopw-search-suggest-results-head">
                    <span class="shopw-search-suggest-group-title">Sản phẩm phù hợp</span>
                    <small>{{ number_format($products->count()) }} gợi ý tức thì</small>
                </div>
                <ul class="dropdown-menu shopw-search-suggest-list">
                    @foreach ($products as $item)
                        @php
                            $imageUrl = \App\Support\AssetUrl::productVariant($item->product_image, 'thumb');
                            $productName = \App\Support\StorefrontLabel::neutralizeSource($item->product_name);
                            $brandName = \App\Support\StorefrontLabel::neutralizeSource($item->brand?->brand_name ?? $item->brand_name);
                            $categoryName = \App\Support\StorefrontLabel::neutralizeSource($item->primaryCategory?->category_name ?? $item->category_name);
                            $originalPrice = (float) ($item->getRawOriginal('price') ?? $item->price ?? 0);
                            $finalPrice = (float) ($item->final_price ?? $item->product_price ?? $originalPrice);
                        @endphp
                        <li class="li_search_ajax" data-search-keyword="{{ $productName }}">
                            <a href="{{ url('/chi-tiet/' . $item->product_slug) }}">
                                <span class="shopw-search-suggest-card">
                                    <span class="shopw-search-suggest-thumb">
                                        <img src="{{ $imageUrl }}" alt="{{ $productName }}" loading="lazy">
                                    </span>
                                    <span class="shopw-search-suggest-item">
                                        <strong>{{ $productName }}</strong>
                                        <small>
                                            @if ($brandName)
                                                {{ $brandName }}
                                            @endif
                                            @if ($brandName && $categoryName)
                                                ·
                                            @endif
                                            @if ($categoryName)
                                                {{ $categoryName }}
                                            @endif
                                        </small>
                                        <span class="shopw-search-suggest-price">
                                            <strong>{{ number_format($finalPrice, 0, ',', '.') }}đ</strong>
                                            @if ($originalPrice > $finalPrice)
                                                <small>{{ number_format($originalPrice, 0, ',', '.') }}đ</small>
                                            @endif
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @elseif ($showEmptyState)
                <div class="shopw-search-suggest-empty">
                    <div class="shopw-empty-state shopw-empty-state--compact shopw-empty-state--center">
                        <span class="shopw-empty-state__icon">
                            <i class="fa fa-search"></i>
                        </span>
                        <div class="shopw-empty-state__body">
                            <strong>Chưa thấy sản phẩm khớp với "{{ $query }}"</strong>
                            <p>Thử mở trang kết quả đầy đủ hoặc chọn nhanh một danh mục bên cạnh.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="shopw-search-suggest-empty is-helper">
                    <div class="shopw-empty-state shopw-empty-state--compact shopw-empty-state--center">
                        <span class="shopw-empty-state__icon">
                            <i class="fa fa-lightbulb-o"></i>
                        </span>
                        <div class="shopw-empty-state__body">
                            <strong>Bắt đầu bằng từ khóa phổ biến</strong>
                            <p>Gõ ít nhất 2 ký tự để xem sản phẩm phù hợp ngay trong ô tìm kiếm.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if (!blank($query))
        <a href="{{ $searchResultsUrl }}" class="shopw-search-suggest-footer">
            <span>Xem tất cả kết quả cho "{{ $query }}"</span>
            <i class="fa fa-angle-right"></i>
        </a>
    @endif
</div>
