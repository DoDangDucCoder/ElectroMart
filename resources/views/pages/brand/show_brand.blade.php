@extends('pages.sanpham.listing')

@php
    $pageMode = 'brand';
    $activeBrand = $selected_brand ?? null;
    $products = $brand_by_id;
    $productCount = method_exists($products, 'total') ? $products->total() : $products->count();
    $title = $activeBrand->brand_name ?? $meta_title;
    $brandSlug = $activeBrand->brand_slug ?? $activeBrand->slug;

    $banner = [
        'breadcrumbItems' => [
            [
                'label' => 'Sản phẩm',
                'url' => url('/san-pham'),
            ],
            [
                'label' => $title,
                'current' => true,
            ],
        ],
        'kicker' => 'Thương hiệu',
        'title' => $title,
        'subtitle' => 'Khám phá '.number_format($productCount).' sản phẩm thuộc thương hiệu '.$title.'.',
        'text' => 'Tổng hợp thiết bị chính hãng, dễ so sánh giá và thêm nhanh vào giỏ hàng.',
        'url' => url('/san-pham'),
        'cta' => 'Xem tất cả sản phẩm',
        'badge' => '0%',
        'badgeLabel' => 'BẢO HÀNH<br>CHÍNH HÃNG',
        'actions' => [
            [
                'url' => url('/san-pham'),
                'icon' => 'fa-th-large',
                'label' => 'Xem tất cả sản phẩm',
            ],
        ],
        'results_title' => 'Sản phẩm '.$title,
        'results_kicker' => 'Danh sách sản phẩm',
        'results_description' => 'Các sản phẩm cùng thương hiệu được trình bày bằng mẫu card dùng chung để dễ so sánh.',
        'empty_title' => 'Hiện chưa có sản phẩm nào trong thương hiệu này.',
        'empty_action_text' => 'Xem tất cả sản phẩm',
        'empty_action_url' => url('/san-pham'),
        'card_column_class' => 'col-xs-12 col-sm-6 col-md-4 col-lg-4',
    ];

    $filter = [
        'show_sidebar' => true,
        'mode' => $pageMode,
        'action' => url('/thuong-hieu/'.$brandSlug),
        'categories' => $category ?? [],
        'brands' => $brand ?? [],
        'brand_id' => $activeBrand->brand_id ?? null,
        'clear_url' => url('/thuong-hieu/'.$brandSlug),
        'sort_labels' => [
            'none' => 'Mặc định',
            'price_asc' => 'Giá thấp đến cao',
            'price_desc' => 'Giá cao đến thấp',
            'created_at_desc' => 'Mới nhất',
        ],
    ];
@endphp
