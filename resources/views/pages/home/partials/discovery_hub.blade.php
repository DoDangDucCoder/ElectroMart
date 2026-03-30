@php
    $discoveryCards = collect($discoveryCards ?? [])->values();
    $cate_pro_tabs = collect($cate_pro_tabs ?? [])->values();
    $storefrontLabel = $storefrontLabel ?? \App\Support\StorefrontLabel::class;
    $hasDiscoveryCards = $discoveryCards->isNotEmpty();
    $hasTabCategories = $cate_pro_tabs->isNotEmpty();
    $discoveryGridClass = match (true) {
        $discoveryCards->count() <= 1 => 'shopw-home-discovery-grid--solo',
        $discoveryCards->count() === 2 => 'shopw-home-discovery-grid--duo',
        default => 'shopw-home-discovery-grid--full',
    };
@endphp

@if ($hasDiscoveryCards || $hasTabCategories)
    <section class="features_items shopw-home-section shopw-home-discovery-section">
        <div class="shopw-section-head">
            <div>
                <span class="shopw-section-kicker">Danh mục nhanh</span>
                <h2 class="title text-center">Đi thẳng vào nhóm sản phẩm đang có sức mua tốt</h2>
                <p class="shopw-section-copy">Giữ nhịp mua sắm ngắn hơn bằng cách tách các nhóm nhu cầu lớn thành những lối vào rõ ràng, dễ quét và đủ thông tin để bấm ngay.</p>
            </div>
        </div>

        @if ($hasDiscoveryCards)
            <div class="shopw-home-discovery-grid {{ $discoveryGridClass }}">
                @foreach ($discoveryCards as $card)
                    <a href="{{ $card['url'] }}" class="shopw-home-discovery-card">
                        <span class="shopw-home-discovery-media">
                            @if (!empty($card['image']))
                                <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}">
                            @else
                                <i class="fa {{ $card['icon'] }}"></i>
                            @endif
                        </span>
                        <span class="shopw-home-discovery-body">
                            <strong>{{ $card['title'] }}</strong>
                            <small>{{ $card['description'] }}</small>
                            @if (!empty($card['tags']))
                                <span class="shopw-home-discovery-tags">
                                    @foreach ($card['tags'] as $tag)
                                        <em>{{ $tag }}</em>
                                    @endforeach
                                </span>
                            @endif
                        </span>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($hasTabCategories)
            <div class="category-tab shopw-home-tabs shopw-home-tab-shell">
                <div class="shopw-home-tab-head">
                    <span class="shopw-home-tab-kicker">Duyệt theo tab</span>
                    <p>Chuyển nhanh giữa các danh mục gốc để xem sản phẩm nổi bật ngay trên trang chủ.</p>
                </div>
                <div class="col-sm-12">
                    <ul class="nav nav-tabs shopw-home-tab-nav">
                        @php
                            $i = 0;
                        @endphp

                        @foreach ($cate_pro_tabs as $cat_tab)
                            @php
                                $i++;
                            @endphp
                            <li class="tabs_pro {{ $i === 1 ? 'active' : '' }}" data-id="{{ $cat_tab->category_id }}">
                                <a href="#tshirt" data-toggle="tab">
                                    {{ $storefrontLabel::category($cat_tab->category_name, $cat_tab->slug_category_product ?? null) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div id="tabs_product" class="shopw-home-products-panel"></div>
            </div>
        @endif
    </section>
@endif

