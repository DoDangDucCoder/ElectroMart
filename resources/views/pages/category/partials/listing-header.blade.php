@include('partials.storefront.breadcrumb', [
    'class' => 'cat-x__breadcrumb',
    'items' => [
        [
            'label' => 'Sản phẩm',
            'url' => url('/san-pham'),
        ],
        [
            'label' => $activeCategoryLabel,
            'current' => true,
        ],
    ],
])

<header class="cat-x__title-block">
    <h1>{{ $activeCategoryHeading ?? $activeCategoryLabel }}</h1>
    <p>Khám phá {{ number_format($productCount) }} sản phẩm chính hãng với giá tốt, bảo hành uy tín</p>
</header>

@if ($sidebarCategories->isNotEmpty())
    <div class="cat-x__chips-wrap">
        <button type="button" class="cat-x__chips-nav is-prev" aria-label="Cuộn trái" data-cat-chips-prev>
            <i class="fa fa-angle-left"></i>
        </button>
        <div class="cat-x__chips" data-cat-chips>
            @php
                $firstLabel = 'Tất cả ' . ($activeCategoryLabel);
                $isFirstActive = !request()->has('child');
            @endphp
            <a href="{{ url('/danh-muc/' . optional($activeCategory)->slug_category_product) }}"
                class="cat-x__chip {{ $isFirstActive ? 'is-active' : '' }}">
                <span class="cat-x__chip-icon">
                    <i class="fa {{ $resolveIcon($activeCategoryLabel, optional($activeCategory)->slug_category_product) }}"></i>
                </span>
                <span class="cat-x__chip-text">
                    <strong>{{ $firstLabel }}</strong>
                    <span>{{ number_format($productCount) }}</span>
                </span>
            </a>
            @foreach ($sidebarCategories as $cat)
                @continue((int) $cat->category_id === $activeId)
                @php
                    $catLabel = $storefrontLabel::category($cat->category_name, $cat->slug_category_product);
                @endphp
                <a href="{{ url('/danh-muc/' . $cat->slug_category_product) }}"
                    class="cat-x__chip">
                    <span class="cat-x__chip-icon">
                        <i class="fa {{ $resolveIcon($cat->category_name, $cat->slug_category_product) }}"></i>
                    </span>
                    <span class="cat-x__chip-text">
                        <strong>{{ $catLabel }}</strong>
                        <span>—</span>
                    </span>
                </a>
            @endforeach
        </div>
        <button type="button" class="cat-x__chips-nav is-next" aria-label="Cuộn phải" data-cat-chips-next>
            <i class="fa fa-angle-right"></i>
        </button>
    </div>
@endif

<div class="cat-x__promo" @if (!empty($bannerImage)) style="background-image: linear-gradient(120deg, rgba(11, 59, 79, .92), rgba(9, 79, 100, .86)), url('{{ $bannerImage }}');" @endif>
    <div class="cat-x__promo-copy">
        <h3>{{ $promoTitle }}</h3>
        <p>{{ $promoText }}</p>
        <a href="{{ url('/lien-he') }}" class="cat-x__promo-cta">Xem ngay</a>
    </div>
    <div class="cat-x__promo-zero">{{ $promoBadge }}</div>
    <div class="cat-x__promo-warranty">
        <i class="fa fa-shield"></i>
        <span>{!! $promoBadgeLabel !!}</span>
    </div>
</div>
