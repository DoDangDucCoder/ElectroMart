@extends('layouts.home-tech')

@section('body_class', trim('g-category '.($__env->yieldContent('category_listing_body_class', '') ?? '')))

@push('styles')
<link href="{{ asset('frontend/css/category-redesign.css') }}?v=2.1" rel="stylesheet">
@endpush

@section('content')
    @php
        $storefrontLabel = $storefrontLabel ?? \App\Support\StorefrontLabel::class;
        $activeCategory = $activeCategory ?? collect($category_name ?? [])->first();
        $activeCategoryLabel = $activeCategoryLabel ?? ($activeCategory
            ? $storefrontLabel::category($activeCategory->category_name, $activeCategory->slug_category_product ?? null)
            : 'Sản phẩm');
        $activeCategoryHeading = $activeCategoryHeading ?? (trim((string) optional($activeCategory)->category_name) ?: $activeCategoryLabel);

        $products = $products ?? ($category_by_id ?? collect());
        $productCount = $productCount ?? (method_exists($products, 'total') ? $products->total() : $products->count());
        $perPage = $perPage ?? (method_exists($products, 'perPage') ? $products->perPage() : 12);
        $currentPage = $currentPage ?? (method_exists($products, 'currentPage') ? $products->currentPage() : 1);
        $fromIndex = $fromIndex ?? ($productCount > 0 ? (($currentPage - 1) * $perPage) + 1 : 0);
        $toIndex = $toIndex ?? ($productCount > 0 ? min($currentPage * $perPage, $productCount) : 0);
        $sort = $sort ?? ($selected_sort ?? 'none');
        $selected_sort = $sort;
        $pageMode = $pageMode ?? 'category';
        $categorySlug = optional($activeCategory)->slug_category_product ?? optional($activeCategory)->slug ?? '';

        $allCategoryItems = collect($category ?? [])
            ->filter(fn($c) => (int)($c->category_status ?? $c->is_active ?? 1) >= 0);
        $activeId = (int) optional($activeCategory)->category_id;
        $activeParentId = (int) optional($activeCategory)->category_parent;
        $sidebarCategories = $sidebarCategories ?? $allCategoryItems
            ->filter(fn($c) => (int) ($c->category_parent ?? 0) === $activeParentId && (int) $c->category_id !== 0)
            ->values();
        if ($sidebarCategories->count() <= 1) {
            $sidebarCategories = $allCategoryItems
                ->filter(fn($c) => (int) ($c->category_parent ?? 0) === $activeId)
                ->values();
        }
        if ($sidebarCategories->isEmpty()) {
            $sidebarCategories = $allCategoryItems
                ->filter(fn($c) => (int) ($c->category_parent ?? 0) === 0)
                ->values();
        }
        $sidebarFilterCategories = $sidebarFilterCategories ?? $sidebarCategories
            ->reject(fn($c) => (int) $c->category_id === $activeId)
            ->values();

        $categoryIcons = $categoryIcons ?? [
            'gaming' => 'fa-gamepad',
            'smartwatch' => 'fa-clock-o',
            'watch' => 'fa-clock-o',
            'office' => 'fa-briefcase',
            'ultrabook' => 'fa-leaf',
            '2-in-1' => 'fa-refresh',
            'mini' => 'fa-cube',
            'macbook' => 'fa-apple',
            'pc' => 'fa-desktop',
            'man-hinh' => 'fa-television',
            'ban-phim' => 'fa-keyboard-o',
            'chuot' => 'fa-mouse-pointer',
            'tai-nghe' => 'fa-headphones',
            'may-anh' => 'fa-camera',
            'camera' => 'fa-camera',
            'phu-tung' => 'fa-cogs',
            'linh-kien' => 'fa-microchip',
        ];
        $resolveIcon = $resolveIcon ?? function ($name, $slug) use ($categoryIcons) {
            $haystack = strtolower(($name ?? '') . ' ' . ($slug ?? ''));
            foreach ($categoryIcons as $needle => $icon) {
                if (str_contains($haystack, $needle)) {
                    return $icon;
                }
            }
            return 'fa-laptop';
        };

        $brandCollection = $brandCollection ?? collect($brand ?? [])->take(10);
        $categoryConfig = [
            'may-anh' => [
                'promo' => ['MÁY ẢNH & THIẾT BỊ GHI HÌNH', 'Máy ảnh, phụ kiện và thiết bị sáng tạo nội dung chính hãng', 'CAM'],
                'needs' => [
                    ['label' => 'Chụp ảnh du lịch', 'slug' => 'du-lich'],
                    ['label' => 'Quay vlog', 'slug' => 'vlog'],
                    ['label' => 'Studio - sản phẩm', 'slug' => 'studio'],
                    ['label' => 'Phụ kiện máy ảnh', 'slug' => 'phu-kien-may-anh'],
                ],
            ],
            'phu-tung' => [
                'promo' => ['PHỤ TÙNG & LINH KIỆN THAY THẾ', 'Linh kiện, phụ tùng và thiết bị hỗ trợ bảo trì sản phẩm', 'PT'],
                'needs' => [
                    ['label' => 'Nâng cấp thiết bị', 'slug' => 'nang-cap'],
                    ['label' => 'Thay thế linh kiện', 'slug' => 'thay-the'],
                    ['label' => 'Bảo trì - sửa chữa', 'slug' => 'bao-tri'],
                    ['label' => 'Dự phòng sử dụng', 'slug' => 'du-phong'],
                ],
            ],
        ];
        $activeConfig = $categoryConfig[$categorySlug] ?? [];
        $promoDefaults = $activeConfig['promo'] ?? ['TRẢ GÓP 0% - DỄ DÀNG SỞ HỮU', 'Hỗ trợ trả góp qua thẻ tín dụng & CMND/CCCD', '0%'];
        $bannerImage = $bannerImage ?? null;
        $promoTitle = $promoTitle ?? $promoDefaults[0];
        $promoText = $promoText ?? $promoDefaults[1];
        $promoBadge = $promoBadge ?? $promoDefaults[2];
        $promoBadgeLabel = $promoBadgeLabel ?? 'BẢO HÀNH<br>CHÍNH HÃNG';
        $useNeeds = $useNeeds ?? ($activeConfig['needs'] ?? [
            ['label' => 'Gaming - Đồ họa', 'slug' => 'gaming'],
            ['label' => 'Văn phòng - Học tập', 'slug' => 'van-phong'],
            ['label' => 'Đồ họa - Kỹ thuật', 'slug' => 'do-hoa'],
            ['label' => 'Doanh nhân - Mỏng nhẹ', 'slug' => 'mong-nhe'],
            ['label' => 'Lập trình - IT', 'slug' => 'lap-trinh'],
        ]);
        $stockOptions = $stockOptions ?? [
            ['label' => 'Tất cả', 'value' => ''],
            ['label' => 'Mới 100%', 'value' => 'new'],
            ['label' => 'Like New', 'value' => 'like_new'],
            ['label' => 'Trưng bày', 'value' => 'display'],
        ];
        $sortRadios = $sortRadios ?? [
            'none' => 'Phổ biến',
            'moi_nhat' => 'Mới nhất',
            'tang_dan' => 'Giá thấp đến cao',
            'giam_dan' => 'Giá cao đến thấp',
            'danh_gia' => 'Đánh giá cao',
        ];
        $sortLabels = $sortLabels ?? [
            'none' => 'Phổ biến',
            'tang_dan' => 'Giá thấp đến cao',
            'giam_dan' => 'Giá cao đến thấp',
            'kytu_az' => 'Tên A-Z',
            'kytu_za' => 'Tên Z-A',
            'khuyen_mai' => 'Khuyến mãi nhiều nhất',
        ];
        $rangeMin = (float) ($range_min_price ?? 0);
        $rangeMax = (float) ($range_max_price ?? 50000000);
        $selectedMin = (float) ($filter_start_price ?? $rangeMin);
        $selectedMax = (float) ($filter_end_price ?? $rangeMax);
        $hasActivePriceFilter = request()->filled('start_price') || request()->filled('end_price');
        $filters = $filters ?? [
            'categories' => $sidebarFilterCategories,
            'brands' => $brandCollection,
            'use_needs' => $useNeeds,
            'stock_options' => $stockOptions,
            'sort_options' => $sortRadios,
            'price' => [
                'min' => $rangeMin,
                'max' => $rangeMax,
                'selected_min' => $selectedMin,
                'selected_max' => $selectedMax,
                'active' => $hasActivePriceFilter,
            ],
            'sort' => $sort,
            'mode' => $pageMode,
        ];
        $fmtPrice = $fmtPrice ?? function ($value) {
            $value = (float) $value;
            if ($value >= 1000000) {
                $millions = $value / 1000000;
                $rounded = ($millions == (int) $millions) ? (int) $millions : number_format($millions, 1, ',', '');
                return $rounded . 'tr';
            }
            return $value <= 0 ? '0đ' : number_format($value, 0, ',', '.') . 'đ';
        };
    @endphp

    <section class="cat-x" data-cat-page-mode="{{ $pageMode }}">
        <div class="cat-x__container">
            @include('pages.category.partials.listing-header', ['bannerImage' => $bannerImage])

            <div class="cat-x__layout">
                @include('pages.category.partials.sidebar-filter', ['filters' => $filters])

                <div class="cat-x__main">
                    @include('pages.category.partials.toolbar')
                    @include('pages.category.partials.product-grid', ['products' => $products])
                    @include('pages.category.partials.pagination', ['products' => $products])
                    @include('pages.category.partials.recent-products')
                    @include('pages.category.partials.brand-strip')
                </div>
            </div>
        </div>
    </section>

    @include('pages.cat-x.partials.scripts')
@endsection
