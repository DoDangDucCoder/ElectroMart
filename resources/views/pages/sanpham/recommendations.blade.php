@extends('layouts.home-tech')

@section('body_class', 'g-search g-recommendations')

@section('content')
    <div class="shopw-search-page shopw-recommendation-page">
        <section class="shopw-search-hero shopw-recommendation-page-head">
            <div class="shopw-search-hero-copy">
                <span class="shopw-search-kicker">Gợi ý sản phẩm</span>
                <h1>Sản phẩm nên xem tiếp</h1>
                <p>{{ $recommendation_description ?? 'Hệ thống đang tổng hợp từ lịch sử mua hàng, lượt xem và xu hướng hiện tại để đề xuất sản phẩm phù hợp hơn.' }}</p>
            </div>

            <div class="shopw-search-hero-actions">
                <a href="{{ url('/') }}" class="shopw-search-secondary-link">
                    <i class="fa fa-home"></i>Về trang chủ
                </a>
                <a href="{{ url('/tim-kiem') }}" class="shopw-search-secondary-link">
                    <i class="fa fa-search"></i>Mở tìm kiếm nâng cao
                </a>
            </div>
        </section>

        @if (!empty($recommendation_sources))
            <section class="shopw-search-results shopw-recommendation-source-strip">
                <div class="shopw-search-chip-group">
                    @foreach ($recommendation_sources as $source)
                        <div class="shopw-search-chip">{{ $source }}</div>
                    @endforeach
                </div>
            </section>
        @endif

        @include('partials.storefront.recommendation-section', [
            'title' => 'Danh sách gợi ý',
            'description' => $recommendation_description ?? null,
            'products' => $recommended_products ?? collect(),
            'cardColumnClass' => 'col-xs-12 col-sm-6 col-md-3',
            'emptyTitle' => 'Chưa có đủ dữ liệu để gợi ý sản phẩm cho bạn.',
        ])
    </div>
@endsection

