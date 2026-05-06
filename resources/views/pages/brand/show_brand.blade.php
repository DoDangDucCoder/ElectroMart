@extends('layouts.home-tech')
@section('body_class', 'g-search')
@section('content')
    <div class="features_items storefront-page"><!--features_items-->
        @include('partials.storefront.page-header', [
            'title' => $selected_brand->brand_name ?? $meta_title,
            'breadcrumbItems' => [
                [
                    'label' => 'Sản phẩm',
                    'url' => url('/san-pham'),
                ],
                [
                    'label' => $selected_brand->brand_name ?? $meta_title,
                    'current' => true,
                ],
            ],
        ])

        @include('pages.category.partials.product_grid', [
            'products' => $brand_by_id,
            'emptyMessage' => 'Hiện chưa có sản phẩm nào trong thương hiệu này.',
        ])
    </div><!--features_items-->
@endsection
