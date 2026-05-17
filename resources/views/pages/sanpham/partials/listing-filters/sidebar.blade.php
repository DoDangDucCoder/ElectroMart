@php
    $showSidebar = $showSidebar ?? true;
    $filters = $filters ?? ($filter ?? []);
    $formAction = $formAction ?? ($searchFormAction ?? ($filters['action'] ?? url('/san-pham')));
    $clearUrl = $clearUrl ?? ($searchClearUrl ?? ($filters['clear_url'] ?? $formAction));
    $keyword = $keyword ?? '';
    $selectedCategory = $selectedCategory ?? ($filters['category_id'] ?? null);
    $selectedBrand = $selectedBrand ?? ($filters['brand_id'] ?? null);
    $rootCategories = $rootCategories ?? collect($filters['categories'] ?? []);
    $brands = $brands ?? collect($filters['brands'] ?? []);
    $productCount = $productCount ?? 0;
    $sort = $sort ?? ($selectedSort ?? ($filters['sort'] ?? 'popular'));
    $sortLabels = $sortLabels ?? ($filters['sort_labels'] ?? ['popular' => 'Phổ biến']);
    $storefrontLabel = $storefrontLabel ?? \App\Support\StorefrontLabel::class;
    $priceBuckets = [
        ['label' => 'Dưới 10 triệu', 'min' => 0, 'max' => 10000000],
        ['label' => '10 - 20 triệu', 'min' => 10000000, 'max' => 20000000],
        ['label' => '20 - 30 triệu', 'min' => 20000000, 'max' => 30000000],
        ['label' => '30 - 50 triệu', 'min' => 30000000, 'max' => 50000000],
        ['label' => 'Trên 50 triệu', 'min' => 50000000, 'max' => null],
    ];
    $useNeeds = [
        ['label' => 'Gaming - Đồ họa', 'value' => 'gaming'],
        ['label' => 'Văn phòng - Học tập', 'value' => 'office'],
        ['label' => 'Đồ họa - Kỹ thuật', 'value' => 'design'],
        ['label' => 'Doanh nhân - Mỏng nhẹ', 'value' => 'business'],
        ['label' => 'Lập trình - IT', 'value' => 'developer'],
    ];
    $stockOptions = [
        ['label' => 'Tất cả', 'value' => ''],
        ['label' => 'Mới 100%', 'value' => 'new'],
        ['label' => 'Like New', 'value' => 'like_new'],
        ['label' => 'Trưng bày', 'value' => 'showcase'],
    ];
    $selectedUsage = collect((array) request()->input('usage', []))->map(fn ($value) => (string) $value)->all();
    $selectedStock = (string) request('stock_status', '');
@endphp

@if ($showSidebar)
<aside class="cat-x__sidebar" aria-label="Bộ lọc sản phẩm" data-listing-filter-mode="{{ $filters['mode'] ?? 'listing' }}">
    <form method="GET" action="{{ $formAction }}">
        @if ($keyword !== '')
            <input type="hidden" name="keywords_submit" value="{{ $keyword }}">
        @endif
        @if (! empty($filters['product_code']))
            <input type="hidden" name="product_code" value="{{ $filters['product_code'] }}">
        @endif
        @if (! empty($filters['highlight_product']))
            <input type="hidden" name="highlight_product" value="{{ $filters['highlight_product'] }}">
        @endif

        <div class="cat-x__panel">
            <h4>Danh mục</h4>
            <ul class="cat-x__filter-list">
                <li>
                    <label class="cat-x__filter-item {{ ! $selectedCategory ? 'is-active' : '' }}">
                        <input type="radio" name="category_id" value="" @checked(! $selectedCategory) hidden>
                        <span class="ck"></span>
                        <span class="lb">Tất cả sản phẩm</span>
                        <span class="cnt">{{ number_format($productCount) }}</span>
                    </label>
                </li>
                @foreach ($rootCategories as $cat)
                    @php
                        $catLabel = $storefrontLabel::category($cat->category_name, $cat->slug_category_product ?? null);
                        $isActive = (string) $selectedCategory === (string) $cat->category_id;
                        $isExtra = $loop->index >= 7;
                    @endphp
                    <li @if ($isExtra && ! $isActive) hidden data-cat-filter-extra="category" @endif>
                        <label class="cat-x__filter-item {{ $isActive ? 'is-active' : '' }}">
                            <input type="radio" name="category_id" value="{{ $cat->category_id }}" @checked($isActive) hidden>
                            <span class="ck"></span>
                            <span class="lb">{{ $catLabel }}</span>
                            <span class="cnt">{{ number_format($cat->product_count ?? $cat->products_count ?? 0) }}</span>
                        </label>
                    </li>
                @endforeach
                @if ($rootCategories->count() > 7)
                    <li>
                        <button type="button" class="cat-x__see-more" data-cat-filter-toggle="category" aria-expanded="false">Xem thêm</button>
                    </li>
                @endif
            </ul>
        </div>

        <div class="cat-x__panel">
            <h4>Khoảng giá</h4>
            <input type="hidden" name="price_min" value="{{ $filters['price_min'] ?? '' }}">
            <input type="hidden" name="price_max" value="{{ $filters['price_max'] ?? '' }}">
            <div class="cat-x__price-buckets">
                @foreach ($priceBuckets as $bucket)
                    @php
                        $bucketQuery = array_filter(array_merge(request()->except(['page', 'price_min', 'price_max']), [
                            'price_min' => $bucket['min'],
                            'price_max' => $bucket['max'],
                        ]), static fn ($value) => $value !== null && $value !== '');
                        $bucketUrl = $formAction . '?' . http_build_query($bucketQuery);
                        $bucketActive = (string) ($filters['price_min'] ?? '') === (string) $bucket['min']
                            && (string) ($filters['price_max'] ?? '') === (string) ($bucket['max'] ?? '');
                    @endphp
                    <a href="{{ $bucketUrl }}" class="cat-x__price-bucket {{ $bucketActive ? 'is-active' : '' }}">{{ $bucket['label'] }}</a>
                @endforeach
            </div>
        </div>

        @if ($brands->isNotEmpty())
            <div class="cat-x__panel">
                <h4>Thương hiệu</h4>
                <ul class="cat-x__filter-list">
                    @foreach ($brands as $brandItem)
                        @php
                            $brandLabel = $storefrontLabel::neutralizeSource($brandItem->brand_name);
                            $brandActive = (string) $selectedBrand === (string) $brandItem->brand_id;
                            $isExtra = $loop->index >= 7;
                        @endphp
                        <li @if ($isExtra && ! $brandActive) hidden data-cat-filter-extra="brand" @endif>
                            <label class="cat-x__filter-item {{ $brandActive ? 'is-active' : '' }}">
                                <input type="radio" name="brand_id" value="{{ $brandItem->brand_id }}" @checked($brandActive) hidden>
                                <span class="ck"></span>
                                <span class="lb">{{ $brandLabel }}</span>
                                <span class="cnt">{{ number_format($brandItem->product_count ?? $brandItem->products_count ?? 0) }}</span>
                            </label>
                        </li>
                    @endforeach
                    @if ($brands->count() > 7)
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
                    @php $usageActive = in_array((string) $need['value'], $selectedUsage, true); @endphp
                    <li>
                        <label class="cat-x__filter-item {{ $usageActive ? 'is-active' : '' }}">
                            <input type="checkbox" name="usage[]" value="{{ $need['value'] }}" @checked($usageActive) hidden>
                            <span class="ck"></span>
                            <span class="lb">{{ $need['label'] }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="cat-x__panel">
            <h4>Tình trạng hàng</h4>
            <ul class="cat-x__filter-list">
                @foreach ($stockOptions as $option)
                    @php $stockActive = $selectedStock === $option['value']; @endphp
                    <li>
                        <label class="cat-x__filter-item cat-x__filter-radio {{ $stockActive ? 'is-active' : '' }}">
                            <input type="radio" name="stock_status" value="{{ $option['value'] }}" @checked($stockActive) hidden>
                            <span class="rd"></span>
                            <span class="lb">{{ $option['label'] }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="cat-x__panel">
            <h4>Sắp xếp theo</h4>
            <ul class="cat-x__filter-list">
                @foreach ($sortLabels as $sortKey => $sortLabel)
                    <li>
                        <label class="cat-x__filter-item cat-x__filter-radio {{ $sort === $sortKey ? 'is-active' : '' }}">
                            <input type="radio" name="sort" value="{{ $sortKey }}" @checked($sort === $sortKey) hidden>
                            <span class="rd"></span>
                            <span class="lb">{{ $sortLabel }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>
            <div class="cat-x__filter-actions">
                <button type="submit" class="cat-x__clear-filter">Áp dụng bộ lọc</button>
                <a href="{{ $clearUrl }}" class="cat-x__clear-filter">Xóa tất cả bộ lọc</a>
            </div>
        </div>
    </form>
</aside>
@endif
