@php
    $pageMode = 'tag';
    $tag = \App\Support\StorefrontLabel::neutralizeSource(str_replace('-', ' ', (string) $product_tag));
    $products = $pro_tag;
    $productCount = method_exists($products, 'total') ? $products->total() : $products->count();
    $banner = [
        'breadcrumbItems' => [
            [
                'label' => 'Sản phẩm',
                'url' => url('/san-pham'),
            ],
            [
                'label' => $tag,
                'current' => true,
            ],
        ],
        'kicker' => 'Nhãn sản phẩm',
        'title' => $tag,
        'subtitle' => number_format($productCount).' sản phẩm đang được hiển thị theo nhãn này.',
        'results_kicker' => 'Danh sách sản phẩm',
        'results_title' => $tag,
        'results_description' => 'Các sản phẩm cùng nhãn được trình bày bằng cùng một mẫu card để dễ so sánh và thêm vào giỏ hàng.',
        'empty_title' => 'Không có sản phẩm nào phù hợp với nhãn này.',
        'empty_action_text' => 'Xem tất cả sản phẩm',
        'empty_action_url' => url('/san-pham'),
        'actions' => [
            [
                'url' => url('/'),
                'icon' => 'fa-home',
                'label' => 'Về trang chủ',
            ],
            [
                'url' => url('/san-pham'),
                'icon' => 'fa-search',
                'label' => 'Tìm kiếm nâng cao',
            ],
        ],
    ];
    $filter = [
        'keyword' => $tag,
        'tag' => $tag,
        'action' => url()->current(),
        'clear_url' => url('/san-pham'),
        'show_sidebar' => true,
        'show_sort' => true,
        'categories' => $category ?? collect(),
        'brands' => $brand ?? collect(),
        'sort' => request('sort', 'popular'),
        'sort_labels' => [
            'popular' => 'Phổ biến',
            'newest' => 'Mới nhất',
            'price_asc' => 'Giá thấp đến cao',
            'price_desc' => 'Giá cao đến thấp',
        ],
        'mode' => $pageMode,
    ];
@endphp

@extends('pages.sanpham.listing')
