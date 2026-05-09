@extends('layouts.home-tech')
@section('body_class', 'g-search')
@section('content')
    <div class="features_items storefront-page"><!--features_items-->
        @include('partials.storefront.page-header', [
            'title' => 'Sản phẩm yêu thích',
            'breadcrumbItems' => [
                [
                    'label' => 'Sản phẩm yêu thích',
                    'current' => true,
                ],
            ],
        ])
        <div id="output">
            <div id="row_wishlist">
                @include('partials.storefront.empty-state', [
                    'title' => 'Chưa có sản phẩm yêu thích',
                    'message' => 'Bạn có thể lưu các sản phẩm quan tâm để quay lại xem nhanh hơn.',
                    'icon' => 'fa fa-heart-o',
                    'actionText' => 'Khám phá sản phẩm',
                    'actionUrl' => url('/san-pham'),
                ])
            </div>
        </div>

    </div>
@endsection
