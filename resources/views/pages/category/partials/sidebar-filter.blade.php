@php
    $filters = $filters ?? [];
    $sidebarFilterCategories = collect($filters['categories'] ?? ($sidebarFilterCategories ?? []));
    $brandCollection = collect($filters['brands'] ?? ($brandCollection ?? []));
    $useNeeds = $filters['use_needs'] ?? ($useNeeds ?? []);
    $stockOptions = $filters['stock_options'] ?? ($stockOptions ?? []);
    $sortRadios = $filters['sort_options'] ?? ($sortRadios ?? []);
@endphp

<aside class="cat-x__sidebar">
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
            @foreach ($sidebarFilterCategories as $cat)
                @php
                    $catLabel = $storefrontLabel::category($cat->category_name, $cat->slug_category_product);
                    $isExtraCategory = $loop->index >= 6;
                @endphp
                <li @if ($isExtraCategory) hidden data-cat-filter-extra="category" @endif>
                    <a href="{{ url('/danh-muc/' . $cat->slug_category_product) }}"
                        class="cat-x__filter-item">
                        <span class="ck"></span>
                        <span class="lb">{{ $catLabel }}</span>
                        <span class="cnt">—</span>
                    </a>
                </li>
            @endforeach
            @if ($sidebarFilterCategories->count() > 6)
                <li>
                    <button type="button" class="cat-x__see-more" data-cat-filter-toggle="category" aria-expanded="false">Xem thêm</button>
                </li>
            @endif
        </ul>
    </div>

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

    @if ($brandCollection->isNotEmpty())
        <div class="cat-x__panel">
            <h4>Thương hiệu</h4>
            <ul class="cat-x__filter-list">
                @foreach ($brandCollection as $brandItem)
                    @php
                        $brandSlug = $brandItem->brand_slug ?? \Illuminate\Support\Str::slug($brandItem->brand_name);
                        $brandActive = request('brand') === $brandSlug;
                        $isExtraBrand = $loop->index >= 7;
                    @endphp
                    <li @if ($isExtraBrand && ! $brandActive) hidden data-cat-filter-extra="brand" @endif>
                        <a href="{{ request()->fullUrlWithQuery(['brand' => $brandActive ? null : $brandSlug]) }}"
                            class="cat-x__filter-item {{ $brandActive ? 'is-active' : '' }}">
                            <span class="ck"></span>
                            <span class="lb">{{ $brandItem->brand_name }}</span>
                            <span class="cnt">—</span>
                        </a>
                    </li>
                @endforeach
                @if ($brandCollection->count() > 7)
                    <li>
                        <button type="button" class="cat-x__see-more" data-cat-filter-toggle="brand" aria-expanded="false">Xem thêm</button>
                    </li>
                @endif
            </ul>
        </div>
    @endif

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
