@extends('layout')

@section('body_class', 'g-catalog')

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
        $currentItems = method_exists($search_product, 'getCollection') ? $search_product->getCollection() : collect($search_product);
        $activeCategory = $selectedCategory ? ($search_categories ?? collect())->firstWhere('category_id', (int) $selectedCategory) : null;
        $activeCategoryLabel = $activeCategory
            ? $storefrontLabel::category($activeCategory->category_name, $activeCategory->slug_category_product ?? null)
            : 'Sản phẩm';
        $activeBrandLabel = $selectedBrand
            ? $storefrontLabel::neutralizeSource(optional(($search_brands ?? collect())->firstWhere('brand_id', (int) $selectedBrand))->brand_name)
            : null;
        $catalogSummary = $activeCategory
            ? trim(strip_tags((string) ($activeCategory->category_desc ?? '')))
            : 'Khám phá các sản phẩm công nghệ đang mở bán, lọc nhanh theo danh mục, thương hiệu và khoảng giá phù hợp.';
        $catalogSummary = $catalogSummary !== ''
            ? $catalogSummary
            : 'Khám phá danh sách sản phẩm, lọc theo khoảng giá và sắp xếp theo nhu cầu mua sắm của bạn.';
        $priceValues = $currentItems
            ->map(fn ($product) => (float) ($product->final_price ?? $product->product_price ?? $product->price ?? 0))
            ->filter(fn ($price) => $price > 0);
        $rangeMin = $priceValues->isNotEmpty() ? $priceValues->min() : 0;
        $rangeMax = $priceValues->isNotEmpty() ? $priceValues->max() : 0;
        $selectedMin = $filters['price_min'] ?? $rangeMin;
        $selectedMax = $filters['price_max'] ?? $rangeMax;
        $formatMoney = fn ($value) => $value > 0 ? number_format((float) $value, 0, ',', '.') . 'đ' : 'Đang cập nhật';
        $sortLabels = [
            'popular' => 'Mặc định',
            'price_asc' => 'Giá thấp đến cao',
            'price_desc' => 'Giá cao đến thấp',
            'created_at_desc' => 'Mới nhất',
            'best_selling' => 'Bán chạy',
            'sale_desc' => 'Khuyến mãi',
            'name_asc' => 'Tên A-Z',
            'name_desc' => 'Tên Z-A',
        ];
        $searchFormAction = $search_form_action ?? url('/san-pham');
        $searchClearUrl = $search_clear_url ?? url('/san-pham');
    @endphp

<div class="dd-catalog-page">
    {{-- Hero Section with Teal Background --}}
    <section class="dd-catalog-hero">
        <div class="dd-catalog-hero-content">
            <nav class="dd-catalog-breadcrumb" aria-label="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span>/</span>
                <a href="{{ url('/san-pham') }}">Sản phẩm</a>
                @if ($activeCategory)
                    <span>/</span>
                    <strong>{{ $activeCategoryLabel }}</strong>
                @endif
            </nav>

            <h1>{{ $activeCategoryLabel }}</h1>
            <p class="dd-catalog-hero-desc">{{ $catalogSummary }}</p>

            <div class="dd-catalog-hero-meta">
                <div class="dd-catalog-meta-card">
                    <span>Sản phẩm:</span>
                    <strong>{{ number_format($productCount) }}</strong>
                </div>
                <div class="dd-catalog-meta-card">
                    <span>Giá:</span>
                    <strong>{{ $formatMoney($rangeMin) }} - {{ $formatMoney($rangeMax) }}</strong>
                </div>
                <div class="dd-catalog-meta-card">
                    <span>Cập nhật:</span>
                    <strong>{{ now()->format('d/m/Y') }}</strong>
                </div>
            </div>
        </div>

        {{-- Floating action buttons --}}
        <div class="dd-catalog-hero-actions">
            <button type="button" class="dd-hero-action-btn" onclick="document.querySelector('.dd-catalog-sidebar').scrollIntoView({behavior: 'smooth'})">
                <i class="fa fa-filter"></i>
            </button>
        </div>
    </section>

    {{-- Main Content: Sidebar + Product Grid --}}
    <div class="dd-catalog-container">

        {{-- Left Sidebar: Filters --}}
        <aside class="dd-catalog-sidebar">
            <form action="{{ $searchFormAction }}" method="GET" class="dd-catalog-filter-form">
                @if ($keyword !== '')
                    <input type="hidden" name="keywords_submit" value="{{ $keyword }}">
                @endif
                @if (!empty($filters['product_code']))
                    <input type="hidden" name="product_code" value="{{ $filters['product_code'] }}">
                @endif
                @if (!empty($filters['highlight_product']))
                    <input type="hidden" name="highlight_product" value="{{ $filters['highlight_product'] }}">
                @endif

                <section class="dd-filter-section">
                    <h2>Danh mục</h2>
                    <label>
                        <input type="radio" name="category_id" value="" @checked(empty($selectedCategory))>
                        <span>Tất cả</span>
                    </label>
                    @foreach (($search_categories ?? collect())->take(8) as $categoryItem)
                        <label>
                            <input type="radio" name="category_id" value="{{ $categoryItem->category_id }}"
                                @checked((string) $selectedCategory === (string) $categoryItem->category_id)>
                            <span>{{ $storefrontLabel::category($categoryItem->category_name, $categoryItem->slug_category_product ?? null) }}</span>
                        </label>
                    @endforeach
                </section>

                <section class="dd-filter-section">
                    <h2>Khoảng giá</h2>
                    <label class="dd-filter-input">
                        <span>Min</span>
                        <input type="text" name="price_min" value="{{ old('price_min', $filters['price_min'] ?? '') }}"
                            placeholder="{{ $formatMoney($rangeMin) }}">
                    </label>
                    <label class="dd-filter-input">
                        <span>Max</span>
                        <input type="text" name="price_max" value="{{ old('price_max', $filters['price_max'] ?? '') }}"
                            placeholder="{{ $formatMoney($rangeMax) }}">
                    </label>
                    <button type="submit" class="dd-filter-primary">Áp dụng</button>
                </section>

                <section class="dd-filter-section">
                    <h2>Thương hiệu</h2>
                    <label>
                        <input type="radio" name="brand_id" value="" @checked(empty($selectedBrand))>
                        <span>Tất cả</span>
                    </label>
                    @foreach (($search_brands ?? collect())->take(10) as $brandItem)
                        <label>
                            <input type="radio" name="brand_id" value="{{ $brandItem->brand_id }}"
                                @checked((string) $selectedBrand === (string) $brandItem->brand_id)>
                            <span>{{ $storefrontLabel::neutralizeSource($brandItem->brand_name) }}</span>
                        </label>
                    @endforeach
                </section>

                <section class="dd-filter-section">
                    <h2>Tình trạng</h2>
                    <label><input type="checkbox" checked disabled><span>Đang mở bán</span></label>
                    <label><input type="checkbox" disabled><span>Có khuyến mãi</span></label>
                    <label><input type="checkbox" disabled><span>Nổi bật</span></label>
                </section>

                <div class="dd-filter-actions">
                    <button type="submit" class="dd-filter-primary">Áp dụng bộ lọc</button>
                    <a href="{{ $searchClearUrl }}">Xóa lọc</a>
                </div>
            </form>
        </aside>

        {{-- Right Main Content: Product Grid --}}
        <main class="dd-catalog-main">
            {{-- Toolbar with sort and result count --}}
            <div class="dd-catalog-toolbar">
                <div class="dd-catalog-toolbar-left">
                    <h2 class="dd-catalog-result-title">Tìm thấy {{ number_format($productCount) }} sản phẩm {{ $activeCategoryLabel }}</h2>
                </div>

                <form action="{{ $searchFormAction }}" method="GET" class="dd-sort-form">
                    @if ($keyword !== '')
                        <input type="hidden" name="keywords_submit" value="{{ $keyword }}">
                    @endif
                    @if (!empty($filters['product_code']))
                        <input type="hidden" name="product_code" value="{{ $filters['product_code'] }}">
                    @endif
                    @if (!empty($selectedCategory))
                        <input type="hidden" name="category_id" value="{{ $selectedCategory }}">
                    @endif
                    @if (!empty($selectedBrand))
                        <input type="hidden" name="brand_id" value="{{ $selectedBrand }}">
                    @endif
                    @if (!empty($filters['price_min']))
                        <input type="hidden" name="price_min" value="{{ $filters['price_min'] }}">
                    @endif
                    @if (!empty($filters['price_max']))
                        <input type="hidden" name="price_max" value="{{ $filters['price_max'] }}">
                    @endif
                    <label for="dd-sort">Sắp xếp:</label>
                    <select id="dd-sort" name="sort" onchange="this.form.submit()">
                        @foreach ($sortLabels as $sortKey => $sortLabel)
                            <option value="{{ $sortKey }}" @selected($selectedSort === $sortKey)>{{ $sortLabel }}</option>
                        @endforeach
                    </select>
                </form>
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
                <div class="dd-product-grid">
                    @foreach ($search_product as $product)
                        @php
                            $productId = $product->product_id;
                            $productName = $storefrontLabel::neutralizeSource($product->product_name);
                            $productUrl = url('chi-tiet/' . $product->product_slug);
                            $imagePath = $assetUrl::product($product->product_image);
                            $finalPrice = (float) ($product->final_price ?? $product->product_price ?? 0);
                            $originalPrice = (float) ($product->price ?? $product->product_price ?? $finalPrice);
                            $displayCode = $product->sku ?: '#' . str_pad((string) $productId, 6, '0', STR_PAD_LEFT);
                            $brandLabel = $storefrontLabel::neutralizeSource(optional($product->brand)->brand_name ?? ($product->brand_name ?? ''));
                        @endphp
                        <article class="dd-product-card">
                            <form class="shopw-product-form">
                                @csrf
                                <input type="hidden" value="{{ $productId }}" class="cart_product_id_{{ $productId }}">
                                <input type="hidden" id="wishlist_productname{{ $productId }}" value="{{ $productName }}"
                                    class="cart_product_name_{{ $productId }}">
                                <input type="hidden" value="{{ $imagePath }}" class="cart_product_image_{{ $productId }}">
                                <input type="hidden" value="{{ $product->product_quantity }}" class="cart_product_quantity_{{ $productId }}">
                                <input type="hidden" id="wishlist_productprice{{ $productId }}" value="{{ number_format($finalPrice, 0, ',', '.') }}đ">
                                <input type="hidden" value="{{ $finalPrice }}" class="cart_product_price_{{ $productId }}">
                                <input type="hidden" value="1" class="cart_product_qty_{{ $productId }}">
                                <a id="wishlist_producturl{{ $productId }}" href="{{ $productUrl }}" class="hidden" aria-hidden="true"></a>

                                {{-- Product Image --}}
                                <a href="{{ $productUrl }}" class="dd-product-media">
                                    <img id="wishlist_productimage{{ $productId }}" src="{{ $imagePath }}" alt="{{ $productName }}">
                                </a>

                                {{-- Product Info --}}
                                <div class="dd-product-info">
                                    <span class="dd-product-code">{{ $displayCode }}</span>
                                    <h3 class="dd-product-name">
                                        <a href="{{ $productUrl }}">{{ $productName }}</a>
                                    </h3>
                                    
                                    {{-- Price Section --}}
                                    <div class="dd-product-price-section">
                                        @if ($originalPrice > $finalPrice)
                                            <span class="dd-product-price-original">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                                        @endif
                                        <strong class="dd-product-price-current">{{ number_format($finalPrice, 0, ',', '.') }}đ</strong>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="dd-product-actions">
                                    <a href="{{ $productUrl }}" class="dd-btn-detail">Chi tiết</a>
                                    <button type="button" class="dd-btn-cart" data-id_product="{{ $productId }}" onclick="Addtocart({{ $productId }});">Thêm giỏ</button>
                                </div>
                            </form>
                        </article>
                    @endforeach
                </div>

                @if (method_exists($search_product, 'links'))
                    <div class="dd-catalog-pagination">
                        {{ $search_product->links() }}
                    </div>
                @endif
            @endif
        </main>
    </div>
</div>
@endsection
