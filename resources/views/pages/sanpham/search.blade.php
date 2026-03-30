@extends('layout')

@section('body_class', 'g-search')

@section('content')
    @php
        $searchCount = method_exists($search_product, 'total') ? $search_product->total() : $search_product->count();
        $keyword = trim((string) ($search_keyword ?? ''));
        $filters = $search_filters ?? [];
        $selectedCategory = $filters['category_id'] ?? null;
        $searchGridClass = match (true) {
            $searchCount <= 1 => 'shopw-search-grid--single',
            $searchCount === 2 => 'shopw-search-grid--duo',
            default => 'shopw-search-grid--full',
        };
        $searchResultsBodyClass = match (true) {
            $searchCount <= 1 => 'shopw-search-results-body--single',
            $searchCount === 2 => 'shopw-search-results-body--duo',
            default => 'shopw-search-results-body--full',
        };
        $searchCardColumnClass = match (true) {
            $searchCount <= 1 => 'col-xs-12 shopw-search-featured-card',
            $searchCount === 2 => 'col-xs-12 col-sm-6',
            default => 'col-xs-12 col-sm-6 col-md-4 col-lg-3',
        };
    @endphp

    <div class="shopw-search-page">
        <section class="shopw-search-hero">
            <div class="shopw-search-hero-copy">
                <span class="shopw-search-kicker">Tìm kiếm sản phẩm</span>
                <h1>Kết quả tìm kiếm</h1>
                @if ($errors->has('keywords_submit'))
                    <div class="alert alert-danger">
                        {{ $errors->first('keywords_submit') }}
                    </div>
                @endif
                @if ($keyword !== '')
                    <p>Tìm thấy {{ number_format($searchCount) }} sản phẩm cho từ khóa <strong>"{{ $keyword }}"</strong>.</p>
                @else
                    <p>Nhập từ khóa từ thanh tìm kiếm để xem danh sách sản phẩm phù hợp trong cửa hàng hiện tại.</p>
                @endif
            </div>

            <div class="shopw-search-hero-actions">
                <a href="{{ url('/') }}" class="shopw-search-secondary-link">
                    <i class="fa fa-home"></i>Về trang chủ
                </a>
                <a href="{{ url('/goi-y-san-pham') }}" class="shopw-search-secondary-link">
                    <i class="fa fa-lightbulb-o"></i>{{ config('storefront.labels.recommend_for_you', 'Gợi ý cho bạn') }}
                </a>
            </div>
        </section>

        <section class="shopw-search-results">
            <div class="shopw-search-results-head">
                <div>
                    <span class="shopw-search-kicker">Bộ lọc tìm kiếm</span>
                    <h2>Tìm theo tên, mã, danh mục và khoảng giá</h2>
                    <p>Bạn có thể kết hợp nhiều điều kiện để thu hẹp kết quả sản phẩm nhanh hơn.</p>
                </div>
            </div>

            <form action="{{ url('/tim-kiem') }}" method="GET" class="shopw-search-filter-form">
                <div class="shopw-search-filter-grid">
                    <div class="shopw-search-filter-field">
                        <label for="keywords_submit">Tên sản phẩm</label>
                        <input type="text" id="keywords_submit" name="keywords_submit"
                            value="{{ old('keywords_submit', $keyword) }}" placeholder="Ví dụ: iPhone 17" />
                    </div>
                    <div class="shopw-search-filter-field">
                        <label for="product_code">Mã sản phẩm</label>
                        <input type="text" id="product_code" name="product_code"
                            value="{{ old('product_code', $filters['product_code'] ?? '') }}"
                            placeholder="Ví dụ: IP17-256" />
                    </div>
                    <div class="shopw-search-filter-field">
                        <label for="category_id">Danh mục</label>
                        <select id="category_id" name="category_id">
                            <option value="">Tất cả danh mục</option>
                            @foreach ($search_categories as $categoryItem)
                                <option value="{{ $categoryItem->category_id }}"
                                    @selected((string) $selectedCategory === (string) $categoryItem->category_id)>
                                    {{ $categoryItem->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="shopw-search-filter-field">
                        <label for="price_min">Giá từ</label>
                        <input type="number" id="price_min" name="price_min" min="0"
                            value="{{ old('price_min', $filters['price_min'] ?? '') }}" placeholder="0" />
                    </div>
                    <div class="shopw-search-filter-field">
                        <label for="price_max">Giá đến</label>
                        <input type="number" id="price_max" name="price_max" min="0"
                            value="{{ old('price_max', $filters['price_max'] ?? '') }}" placeholder="50000000" />
                    </div>
                    <div class="shopw-search-filter-field">
                        <label for="sort">Sắp xếp</label>
                        <select id="sort" name="sort">
                            <option value="popular" @selected(($filters['sort'] ?? 'popular') === 'popular')>Phổ biến</option>
                            <option value="price_asc" @selected(($filters['sort'] ?? null) === 'price_asc')>Giá thấp đến cao</option>
                            <option value="price_desc" @selected(($filters['sort'] ?? null) === 'price_desc')>Giá cao đến thấp</option>
                            <option value="name_asc" @selected(($filters['sort'] ?? null) === 'name_asc')>Tên A-Z</option>
                            <option value="name_desc" @selected(($filters['sort'] ?? null) === 'name_desc')>Tên Z-A</option>
                        </select>
                    </div>
                </div>

                <div class="shopw-search-filter-actions">
                    <button type="submit" class="shopw-search-submit-btn">
                        <i class="fa fa-search"></i>Tìm sản phẩm
                    </button>
                    <a href="{{ url('/tim-kiem') }}" class="shopw-search-secondary-link">Xóa bộ lọc</a>
                </div>
            </form>
        </section>

        <section class="shopw-search-results">
            <div class="shopw-search-results-head">
                <div>
                    <span class="shopw-search-kicker">Danh sách kết quả</span>
                    <h2>{{ $keyword !== '' ? $keyword : 'Kết quả theo bộ lọc hiện tại' }}</h2>
                    <p>{{ number_format($searchCount) }} sản phẩm đang được hiển thị trong danh sách này.</p>
                </div>
                <div class="shopw-search-chip-group">
                    @if ($keyword !== '')
                        <div class="shopw-search-chip">Tên: {{ $keyword }}</div>
                    @endif
                    @if (!empty($filters['product_code']))
                        <div class="shopw-search-chip">Mã: {{ $filters['product_code'] }}</div>
                    @endif
                    @if ($selectedCategory)
                        @php
                            $activeCategoryLabel = optional($search_categories->firstWhere('category_id', (int) $selectedCategory))->category_name;
                        @endphp
                        @if ($activeCategoryLabel)
                            <div class="shopw-search-chip">Danh mục: {{ $activeCategoryLabel }}</div>
                        @endif
                    @endif
                    @if (!empty($filters['price_min']) || !empty($filters['price_max']))
                        <div class="shopw-search-chip">
                            Giá:
                            {{ !empty($filters['price_min']) ? number_format((float) $filters['price_min']) . 'đ' : '0đ' }}
                            -
                            {{ !empty($filters['price_max']) ? number_format((float) $filters['price_max']) . 'đ' : 'Không giới hạn' }}
                        </div>
                    @endif
                </div>
            </div>

            @if ($search_product->isEmpty())
                @include('partials.storefront.empty-state', [
                    'title' => 'Không tìm thấy sản phẩm phù hợp',
                    'message' => 'Thử lại với tên ngắn hơn, từ khóa khác hoặc quay về danh mục để duyệt sản phẩm.',
                    'icon' => 'fa fa-search',
                    'class' => 'shopw-search-empty',
                    'actionText' => 'Về trang chủ',
                    'actionUrl' => url('/'),
                ])
            @else
                <div class="shopw-search-results-body {{ $searchResultsBodyClass }}">
                    <div class="row shopw-search-grid {{ $searchGridClass }}">
                        @include('pages.category.partials.product_cards', [
                            'products' => $search_product,
                            'emptyMessage' => 'Không có sản phẩm nào phù hợp.',
                            'emptyMessageClasses' => 'text-center text-muted',
                            'cardColumnClass' => $searchCardColumnClass,
                            'showQuickView' => false,
                            'showCartToggle' => false,
                            'showWishlist' => true,
                            'showCompare' => true,
                        ])
                    </div>

                    @if (method_exists($search_product, 'links'))
                        <div class="shopw-search-pagination">
                            {{ $search_product->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </section>

        @include('partials.storefront.recommendation-section', [
            'title' => 'Gợi ý sản phẩm',
            'description' => $recommendation_description ?? null,
            'products' => $recommended_products ?? collect(),
            'cardColumnClass' => 'col-xs-12 col-sm-6 col-md-3',
            'emptyTitle' => 'Hệ thống chưa đủ dữ liệu để gợi ý thêm sản phẩm cho bạn.',
        ])
    </div>
@endsection

