@extends('layout')

@section('body_class', 'g-compare')

@section('content')
    @php
        $productCount = $products->count();
    @endphp

    <div class="shopw-compare-page">
        <section class="shopw-compare-hero">
            <div class="shopw-compare-hero-copy">
                <span class="shopw-search-kicker">{{ config('storefront.labels.compare_products', 'So sánh sản phẩm') }}</span>
                <h1>So sánh chi tiết</h1>
                <p>
                    Đặt các mẫu lên cùng một bàn để nhìn nhanh sự khác nhau về giá, thương hiệu, danh mục và thông số chính
                    trước khi quyết định.
                </p>
            </div>

            <div class="shopw-compare-hero-actions">
                <a href="{{ url('/') }}" class="shopw-search-secondary-link">
                    <i class="fa fa-home"></i>Về trang chủ
                </a>
                <a href="{{ url('/tim-kiem') }}" class="shopw-search-secondary-link">
                    <i class="fa fa-search"></i>Mở tìm kiếm nâng cao
                </a>
                @if ($productCount > 0)
                    <button type="button" class="shopw-search-secondary-link shopw-compare-clear-btn" onclick="clearCompare()">
                        <i class="fa fa-trash-o"></i>Xóa danh sách
                    </button>
                @endif
            </div>
        </section>

        @if ($products->isEmpty())
            @include('partials.storefront.empty-state', [
                'title' => 'Chưa có sản phẩm để so sánh',
                'message' => 'Thêm sản phẩm vào danh sách so sánh để xem bảng thông số chi tiết và lựa chọn mẫu phù hợp hơn.',
                'icon' => 'fa fa-balance-scale',
                'class' => 'shopw-compare-empty',
                'actionText' => 'Về trang chủ',
                'actionUrl' => url('/'),
            ])
        @else
            <section class="shopw-compare-summary">
                <div class="shopw-compare-summary-copy">
                    <span class="shopw-search-kicker">Danh sách đang so sánh</span>
                    <h2>{{ number_format($productCount) }} mẫu đang có trong bảng này</h2>
                    <p>Danh sách này luôn lấy giá và trạng thái sale hiện tại để anh đối chiếu nhanh trước khi bấm vào chi tiết.</p>
                </div>

                <div class="shopw-compare-summary-grid">
                    @foreach ($products as $product)
                        @php
                            $imagePath = $product->product_image
                                ? asset($product->product_image)
                                : asset('uploads/product/no-image.svg');
                            $productBrand = $product->brand?->name ?? '-';
                        @endphp
                        <article class="shopw-compare-summary-card">
                            <a href="{{ url('chi-tiet/' . $product->product_slug) }}" class="shopw-compare-summary-media">
                                <img src="{{ $imagePath }}" alt="{{ $product->product_name }}">
                            </a>
                            <div class="shopw-compare-summary-body">
                                <strong>{{ $product->product_name }}</strong>
                                <small>{{ $productBrand }}</small>
                                @include('partials.storefront.product-price', [
                                    'product' => $product,
                                    'currency' => 'VND',
                                    'showExplanation' => false,
                                    'compact' => true,
                                ])
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="shopw-compare-table-shell">
                <div class="table-responsive shopw-compare-table-wrap">
                    <table class="table table-bordered compare-table shopw-compare-table">
                        <thead>
                            <tr>
                                <th class="shopw-compare-table-label">Tiêu chí</th>
                                @foreach ($products as $product)
                                    @php
                                        $imagePath = $product->product_image
                                            ? asset($product->product_image)
                                            : asset('uploads/product/no-image.svg');
                                    @endphp
                                    <th class="shopw-compare-table-product">
                                        <a href="{{ url('chi-tiet/' . $product->product_slug) }}" class="shopw-compare-table-product-card">
                                            <span class="shopw-compare-table-product-image">
                                                <img src="{{ $imagePath }}" alt="{{ $product->product_name }}">
                                            </span>
                                            <span class="shopw-compare-table-product-copy">
                                                <strong>{{ $product->product_name }}</strong>
                                                @include('partials.storefront.product-price', [
                                                    'product' => $product,
                                                    'currency' => 'VND',
                                                    'showExplanation' => false,
                                                    'compact' => true,
                                                ])
                                            </span>
                                        </a>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th class="shopw-compare-table-label">Thương hiệu</th>
                                @foreach ($products as $product)
                                    <td>{{ $product->brand?->name ?? '-' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="shopw-compare-table-label">Danh mục</th>
                                @foreach ($products as $product)
                                    <td>{{ $product->primaryCategory?->name ?? '-' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="shopw-compare-table-label">Giá gốc</th>
                                @foreach ($products as $product)
                                    <td>{{ number_format((float) ($product->price ?? $product->product_price ?? 0), 0, ',', '.') }} VND</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="shopw-compare-table-label">Mức sale</th>
                                @foreach ($products as $product)
                                    <td>
                                        @if ((float) $product->sale_amount > 0)
                                            @if ($product->sale_type === 'percent')
                                                -{{ rtrim(rtrim(number_format((float) $product->sale_value, 2, '.', ''), '0'), '.') }}%
                                            @else
                                                -{{ number_format((float) $product->sale_value, 0, ',', '.') }}đ
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="shopw-compare-table-label">Giá sau giảm</th>
                                @foreach ($products as $product)
                                    <td>
                                        @include('partials.storefront.product-price', [
                                            'product' => $product,
                                            'currency' => 'VND',
                                            'showExplanation' => false,
                                            'compact' => true,
                                        ])
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="shopw-compare-table-label">Số lượng tồn</th>
                                @foreach ($products as $product)
                                    <td>{{ $product->stock_quantity ?? 0 }}</td>
                                @endforeach
                            </tr>

                            @foreach ($specKeys as $specKey)
                                <tr>
                                    <th class="shopw-compare-table-label">{{ str_replace('_', ' ', $specKey) }}</th>
                                    @foreach ($products as $product)
                                        @php
                                            $specs = $product->meta['specifications'] ?? [];
                                            $value = $specs[$specKey] ?? '-';
                                            if (is_array($value)) {
                                                $value = implode(', ', $value);
                                            }
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>

    <script type="text/javascript">
        function clearCompare() {
            localStorage.removeItem('compare');
            window.location.href = "{{ url('/so-sanh') }}";
        }

        (function syncCompareIds() {
            var hasProducts = {{ $products->count() }};
            if (hasProducts) {
                return;
            }

            var raw = localStorage.getItem('compare');
            if (!raw) {
                return;
            }

            try {
                var data = JSON.parse(raw);
                if (!Array.isArray(data) || data.length === 0) {
                    return;
                }

                var ids = data.map(function(item) {
                    return item.id;
                }).filter(Boolean).join(',');

                if (!ids) {
                    return;
                }

                window.location.href = "{{ url('/so-sanh') }}" + '?ids=' + encodeURIComponent(ids);
            } catch (e) {
                return;
            }
        })();
    </script>
@endsection
