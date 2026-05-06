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
        <div id="output"></div>

    </div>
@endsection
