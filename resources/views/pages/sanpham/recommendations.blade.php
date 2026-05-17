@extends('pages.sanpham.listing')

@section('listing_body_class', 'g-recommendations')
@section('listing_page_class', 'shopw-recommendation-page')

@section('listing_content')
    @include('pages.sanpham.partials.listing-header', [
        'breadcrumbItems' => [
            [
                'label' => 'Sản phẩm',
                'url' => url('/san-pham'),
            ],
            [
                'label' => 'Gợi ý sản phẩm',
                'current' => true,
            ],
        ],
        'kicker' => 'Gợi ý sản phẩm',
        'title' => 'Sản phẩm nên xem tiếp',
        'subtitle' => $recommendation_description ?? 'Hệ thống đang tổng hợp từ lịch sử mua hàng, lượt xem và xu hướng hiện tại để đề xuất sản phẩm phù hợp hơn.',
        'heroClass' => 'shopw-recommendation-page-head',
        'actions' => [
            [
                'url' => url('/'),
                'icon' => 'fa-home',
                'label' => 'Về trang chủ',
            ],
            [
                'url' => url('/san-pham'),
                'icon' => 'fa-search',
                'label' => 'Mở tìm kiếm nâng cao',
            ],
        ],
    ])

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
@endsection

