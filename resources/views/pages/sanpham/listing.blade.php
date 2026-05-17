@extends('layouts.home-tech')

@php
    $listingPageMode = $pageMode ?? $search_page_mode ?? null;
    $listingBodyMode = $listingPageMode ? ' g-'.str_replace('_', '-', $listingPageMode) : '';
@endphp

@section('body_class', trim('g-search'.$listingBodyMode.' '.$__env->yieldContent('listing_body_class', '')))

@push('styles')
<link href="{{ asset('frontend/css/category-redesign.css') }}?v=3.1" rel="stylesheet">
@endpush

@section('content')
    @hasSection('listing_content')
        <main class="{{ trim('tech-home-main catalog-tech-main '.$__env->yieldContent('listing_main_class', '')) }}">
            <div class="container">
                <div class="{{ trim('shopw-search-page '.$__env->yieldContent('listing_page_class', '')) }}">
                    @yield('listing_content')
                </div>
            </div>
        </main>
    @else
        @php
            $storefrontLabel = \App\Support\StorefrontLabel::class;
            $products = $products ?? $search_product ?? $recommended_products ?? collect();
            $filters = $filters ?? $filter ?? $search_filters ?? [];
            $banner = $banner ?? [];
            $pageModeForPartials = str_replace('-', '_', (string) ($listingPageMode ?? 'search'));
            $productCount = method_exists($products, 'total') ? $products->total() : $products->count();
            $perPage = method_exists($products, 'perPage') ? $products->perPage() : 12;
            $currentPage = method_exists($products, 'currentPage') ? $products->currentPage() : 1;
            $fromIndex = method_exists($products, 'firstItem') ? ($products->firstItem() ?? 0) : ($productCount > 0 ? (($currentPage - 1) * $perPage) + 1 : 0);
            $toIndex = method_exists($products, 'lastItem') ? ($products->lastItem() ?? 0) : ($productCount > 0 ? min($currentPage * $perPage, $productCount) : 0);
            $keyword = trim((string) ($filters['keyword'] ?? $search_keyword ?? ''));
            $selectedCategory = $filters['category_id'] ?? null;
            $selectedBrand = $filters['brand_id'] ?? null;
            $rangeMin = (float) ($filters['range_min'] ?? 0);
            $rangeMax = (float) ($filters['range_max'] ?? 50000000);
            $selectedMin = (float) ($filters['price_min'] ?? $rangeMin);
            $selectedMax = (float) ($filters['price_max'] ?? $rangeMax);
            $sort = $filters['sort'] ?? 'popular';
            $selected_sort = $sort;
            $formAction = $filters['action'] ?? $search_form_action ?? url()->current();
            $clearUrl = $filters['clear_url'] ?? $search_clear_url ?? $formAction;
            $allCategories = collect($filters['categories'] ?? $search_categories ?? $category ?? []);
            $rootCategories = $allCategories
                ->filter(fn ($cat) => (int) ($cat->category_parent ?? 0) === 0)
                ->filter(function ($cat) use ($selectedCategory) {
                    // Always show the currently selected category
                    if ($selectedCategory && (string) ($cat->category_id ?? $cat->id) === (string) $selectedCategory) {
                        return true;
                    }
                    // Only show categories with ≥ 3 products
                    return (int) ($cat->products_count ?? $cat->product_count ?? 0) >= 3;
                })
                ->sortByDesc(fn ($cat) => (int) ($cat->products_count ?? $cat->product_count ?? 0))
                ->values();
            if ($rootCategories->isEmpty()) {
                $rootCategories = $allCategories->values();
            }
            $brands = collect($filters['brands'] ?? $search_brands ?? $brand ?? []);
            $sortLabels = $filters['sort_labels'] ?? [
                'popular' => 'Phổ biến',
                'best_selling' => 'Bán chạy',
                'created_at_desc' => 'Mới nhất',
                'price_asc' => 'Giá thấp đến cao',
                'price_desc' => 'Giá cao đến thấp',
                'sale_desc' => 'Khuyến mãi',
                'name_asc' => 'Tên A-Z',
                'name_desc' => 'Tên Z-A',
            ];
            $showSidebar = (bool) ($filters['show_sidebar'] ?? in_array($pageModeForPartials, ['search', 'all_products', 'best_selling'], true));
            $activeCategory = $selectedCategory ? $allCategories->firstWhere('category_id', (int) $selectedCategory) : null;
            $activeCategoryLabel = $activeCategory
                ? $storefrontLabel::category($activeCategory->category_name, $activeCategory->slug_category_product ?? null)
                : ($pageModeForPartials === 'recommendations' ? 'Gợi ý sản phẩm' : null);
            $pageMode = $pageModeForPartials;
            $pageTitle = $banner['title'] ?? $search_page_title ?? $meta_title ?? 'Sản phẩm';
            if ($keyword !== '') {
                $pageTitle = 'Kết quả tìm kiếm cho "'.$keyword.'"';
            }
            if ($activeCategoryLabel && $pageModeForPartials !== 'recommendations') {
                $pageTitle = $activeCategoryLabel;
            }
            $pageSummary = $banner['subtitle'] ?? $search_page_summary ?? 'Khám phá '.number_format($productCount).' sản phẩm chính hãng với giá tốt, bảo hành uy tín.';
            $promoTitle = $banner['promo_title'] ?? $banner['title'] ?? $pageTitle;
            $promoText = $banner['promo_text'] ?? $banner['results_description'] ?? $pageSummary;
            $promoBadge = $banner['promo_badge'] ?? ($pageModeForPartials === 'recommendations' ? 'AI' : 'TOP');
            $promoBadgeLabel = $banner['promo_badge_label'] ?? 'BẢO HÀNH<br>CHÍNH HÃNG';
            $bannerImage = $banner['image'] ?? ($banner['bannerImage'] ?? null);
            $breadcrumbItems = $banner['breadcrumbItems'] ?? [];
            $title = $pageTitle;
            $subtitle = $pageSummary;
            $kicker = $banner['kicker'] ?? null;
            $actions = $banner['actions'] ?? [];
            $rangeMin = (float) ($filters['price_min'] ?? 0);
            $rangeMax = (float) ($filters['price_max'] ?? 50000000);
            $selectedMin = $rangeMin;
            $selectedMax = $rangeMax;
        @endphp

        <section class="cat-x" data-cat-page-mode="{{ $listingPageMode }}">
            <div class="cat-x__container">
                @include('pages.sanpham.partials.listing-header')

                @if (! empty($banner['chips']))
                    <div class="cat-x__chips-wrap">
                        <div class="cat-x__chips">
                            @foreach ($banner['chips'] as $chip)
                                <span class="cat-x__chip">{{ $chip }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="cat-x__layout {{ $showSidebar ? '' : 'cat-x__layout--full' }}">
                    @if ($showSidebar)
                        @include('pages.sanpham.partials.listing-filters.sidebar')
                    @endif

                    <div class="cat-x__main">
                        @include('pages.category.partials.toolbar')
                        @include('pages.sanpham.partials.product-grid', [
                            'products' => $products,
                            'emptyMessage' => $banner['empty_title'] ?? 'Không có sản phẩm nào phù hợp.',
                            'emptyStateActionText' => $banner['empty_action_text'] ?? 'Xem tất cả sản phẩm',
                            'emptyStateActionUrl' => $banner['empty_action_url'] ?? url('/san-pham'),
                        ])
                        @include('pages.sanpham.partials.pagination', ['products' => $products])
                    </div>
                </div>
            </div>
        </section>

        <script>
            (function () {
                'use strict';

                const chipsTrack = document.querySelector('[data-cat-chips]');
                if (chipsTrack) {
                    const prev = document.querySelector('[data-cat-chips-prev]');
                    const next = document.querySelector('[data-cat-chips-next]');
                    const update = () => {
                        if (prev) prev.disabled = chipsTrack.scrollLeft <= 4;
                        if (next) next.disabled = chipsTrack.scrollLeft + chipsTrack.clientWidth >= chipsTrack.scrollWidth - 4;
                    };
                    if (prev) prev.addEventListener('click', () => chipsTrack.scrollBy({ left: -260, behavior: 'smooth' }));
                    if (next) next.addEventListener('click', () => chipsTrack.scrollBy({ left: 260, behavior: 'smooth' }));
                    chipsTrack.addEventListener('scroll', update, { passive: true });
                    window.addEventListener('resize', update);
                    update();
                }

                const grid = document.querySelector('[data-cat-grid]');
                document.querySelectorAll('[data-cat-view]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('[data-cat-view]').forEach((item) => item.classList.remove('is-active'));
                        btn.classList.add('is-active');
                        if (grid) grid.classList.toggle('is-list', btn.getAttribute('data-cat-view') === 'list');
                    });
                });

                document.querySelectorAll('[data-cat-filter-toggle]').forEach((toggle) => {
                    toggle.addEventListener('click', () => {
                        const target = toggle.getAttribute('data-cat-filter-toggle');
                        const expanded = toggle.getAttribute('aria-expanded') === 'true';
                        const panel = toggle.closest('.cat-x__panel');
                        if (!panel || !target) return;
                        panel.querySelectorAll(`[data-cat-filter-extra="${target}"]`).forEach((item) => {
                            item.hidden = expanded;
                        });
                        toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                        toggle.textContent = expanded ? 'Xem thêm' : 'Thu gọn';
                    });
                });
            })();
        </script>
    @endif
@endsection
