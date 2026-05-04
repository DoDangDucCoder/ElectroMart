@extends('layouts.home-tech')
@section('content')
    <div class="features_items"><!--features_items-->
        <h2 class="title text-center">{{ $selected_brand->brand_name ?? $meta_title }}</h2>

        @include('pages.category.partials.product_grid', [
            'products' => $brand_by_id,
            'emptyMessage' => 'Hiện chưa có sản phẩm nào trong thương hiệu này.',
        ])
    </div><!--features_items-->
@endsection
