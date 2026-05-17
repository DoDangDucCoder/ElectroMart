@php
    $chipCategories = $rootCategories ?? collect();
    $activeCategoryLabel = $activeCategoryLabel ?? null;
    $selectedCategory = $selectedCategory ?? null;
    $pageMode = $pageMode ?? 'search';
    $pageTitle = $pageTitle ?? 'Sản phẩm';
    $pageSummary = $pageSummary ?? null;
    $formAction = $formAction ?? url('/san-pham');
    $clearUrl = $clearUrl ?? $formAction;
    $productCount = $productCount ?? 0;
    $storefrontLabel = $storefrontLabel ?? \App\Support\StorefrontLabel::class;
    $bannerImage = $bannerImage ?? null;
    $promoTitle = $promoTitle ?? 'TRẢ GÓP 0% - DỄ DÀNG SỞ HỮU';
    $promoText = $promoText ?? 'Hỗ trợ trả góp qua thẻ tín dụng & CMND/CCCD';
    $promoBadge = $promoBadge ?? '0%';
    $promoBadgeLabel = $promoBadgeLabel ?? 'BẢO HÀNH<br>CHÍNH HÃNG';
    $explicitBreadcrumbItems = $breadcrumbItems ?? [];
@endphp

@include('partials.storefront.breadcrumb', [
    'class' => 'cat-x__breadcrumb',
    'items' => ! empty($explicitBreadcrumbItems) ? $explicitBreadcrumbItems : array_filter([
        [
            'label' => 'Sản phẩm',
            'url' => $pageMode === 'all_products' ? null : url('/san-pham'),
            'current' => $pageMode === 'all_products',
        ],
        $pageMode === 'search' ? [
            'label' => 'Tìm kiếm',
            'current' => true,
        ] : null,
        $pageMode === 'best_selling' ? [
            'label' => 'Bán chạy',
            'current' => true,
        ] : null,
        $activeCategoryLabel ? [
            'label' => $activeCategoryLabel,
            'current' => true,
        ] : null,
    ]),
])

<header class="cat-x__title-block">
    <h1>{{ $pageTitle }}</h1>
    <p>{{ $pageSummary }}</p>
</header>

@if ($chipCategories->isNotEmpty())
    <div class="cat-x__chips-wrap">
        <button type="button" class="cat-x__chips-nav is-prev" aria-label="Cuộn trái" data-cat-chips-prev>
            <i class="fa fa-angle-left"></i>
        </button>
        <div class="cat-x__chips" data-cat-chips>
            <a href="{{ $clearUrl }}" class="cat-x__chip {{ ! $selectedCategory ? 'is-active' : '' }}">
                <span class="cat-x__chip-icon"><i class="fa fa-th-large"></i></span>
                <span class="cat-x__chip-text">
                    <strong>Tất cả sản phẩm</strong>
                    <span>{{ number_format($productCount) }}</span>
                </span>
            </a>
            @foreach ($chipCategories->take(10) as $cat)
                @php
                    $catLabel = $storefrontLabel::category($cat->category_name, $cat->slug_category_product ?? null);
                    $isActive = (string) $selectedCategory === (string) $cat->category_id;
                    $chipUrl = $formAction . '?' . http_build_query(array_filter(array_merge(request()->except(['page']), [
                        'category_id' => $cat->category_id,
                    ]), static fn ($value) => $value !== null && $value !== ''));
                @endphp
                <a href="{{ $chipUrl }}" class="cat-x__chip {{ $isActive ? 'is-active' : '' }}">
                    <span class="cat-x__chip-icon"><i class="fa fa-laptop"></i></span>
                    <span class="cat-x__chip-text">
                        <strong>{{ $catLabel }}</strong>
                        <span>{{ number_format($cat->product_count ?? $cat->products_count ?? 0) }}</span>
                    </span>
                </a>
            @endforeach
        </div>
        <button type="button" class="cat-x__chips-nav is-next" aria-label="Cuộn phải" data-cat-chips-next>
            <i class="fa fa-angle-right"></i>
        </button>
    </div>
@endif

<div class="cat-x__promo" @if (! empty($bannerImage)) style="background-image: linear-gradient(120deg, rgba(11, 59, 79, .92), rgba(9, 79, 100, .86)), url('{{ $bannerImage }}');" @endif>
    <div class="cat-x__promo-copy">
        <h3>{{ $promoTitle }}</h3>
        <p>{{ $promoText }}</p>
        <a href="{{ $formAction }}" class="cat-x__promo-cta">Xem ngay</a>
    </div>
    <div class="cat-x__promo-zero">{{ $promoBadge }}</div>
    <div class="cat-x__promo-warranty">
        <i class="fa fa-shield"></i>
        <span>{!! $promoBadgeLabel !!}</span>
    </div>
</div>
