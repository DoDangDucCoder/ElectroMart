@extends('layout')

@section('body_class', 'g-search')

@section('content')
    @php
        $tagLabel = \App\Support\StorefrontLabel::neutralizeSource(str_replace('-', ' ', (string) $product_tag));
        $productCount = $pro_tag->count();
    @endphp

    <div class="shopw-search-page">
        <section class="shopw-search-hero">
            <div class="shopw-search-hero-copy">
                <span class="shopw-search-kicker">Nhãn sản phẩm</span>
                <h1>{{ $tagLabel }}</h1>
                <p>{{ number_format($productCount) }} sản phẩm đang được hiển thị theo nhãn này.</p>
            </div>

            <div class="shopw-search-hero-actions">
                <a href="{{ url('/') }}" class="shopw-search-secondary-link">
                    <i class="fa fa-home"></i>Về trang chủ
                </a>
                <a href="{{ url('/tim-kiem') }}" class="shopw-search-secondary-link">
                    <i class="fa fa-search"></i>Tìm kiếm nâng cao
                </a>
            </div>
        </section>

        <section class="shopw-search-results">
            <div class="shopw-search-results-head">
                <div>
                    <span class="shopw-search-kicker">Danh sách sản phẩm</span>
                    <h2>{{ $tagLabel }}</h2>
                    <p>Các sản phẩm cùng nhãn được trình bày bằng cùng một mẫu card để dễ so sánh và thêm vào giỏ hàng.</p>
                </div>
            </div>

            <div class="row shopw-search-grid shopw-search-grid--full">
                @include('pages.category.partials.product_cards', [
                    'products' => $pro_tag,
                    'emptyMessage' => 'Không có sản phẩm nào phù hợp với nhãn này.',
                    'emptyMessageClasses' => 'text-center text-muted',
                    'cardColumnClass' => 'col-xs-12 col-sm-6 col-md-4 col-lg-3',
                    'showQuickView' => false,
                    'showCartToggle' => false,
                    'showWishlist' => true,
                    'showCompare' => true,
                ])
            </div>
        </section>
    </div>
@endsection
