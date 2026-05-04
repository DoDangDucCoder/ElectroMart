@php
    /** @var \App\Infrastructure\Persistence\Eloquent\Catalog\Product $product */
    $productId = $product->product_id;
    $productName = \App\Support\StorefrontLabel::neutralizeSource($product->product_name);
    $productUrl = url('chi-tiet/' . $product->product_slug);
    $imagePath = \App\Support\AssetUrl::product($product->product_image);
    $productBrandRaw = optional($product->brand)->brand_name ?? ($product->brand_name ?? null);
    $productBrand = $productBrandRaw ? strtoupper(\App\Support\StorefrontLabel::neutralizeSource($productBrandRaw)) : null;

    $finalPrice = (float) ($product->final_price ?? $product->product_price);
    $originalPrice = (float) ($product->product_price ?? $finalPrice);
    $saleAmount = (float) ($product->sale_amount ?? 0);
    $saleType = $product->sale_type ?? null;
    $saleValue = $product->sale_value ?? null;
    $hasSale = $saleAmount > 0 && $originalPrice > $finalPrice;

    if ($hasSale && ($saleType === 'percent' && $saleValue)) {
        $discountPercent = (int) round((float) $saleValue);
    } elseif ($hasSale && $originalPrice > 0) {
        $discountPercent = (int) round((($originalPrice - $finalPrice) / $originalPrice) * 100);
    } else {
        $discountPercent = 0;
    }

    $rating = max(0, min(5, (float) ($product->rating_average ?? $product->avg_rating ?? 4.5)));
    $reviewCount = (int) ($product->review_count ?? $product->ratings_count ?? rand(15, 130));

    $fullStars = (int) floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = max(0, 5 - $fullStars - ($halfStar ? 1 : 0));
@endphp

<article class="cat-x__card" data-product-id="{{ $productId }}">
    @if ($discountPercent > 0)
        <span class="cat-x__card-discount">-{{ $discountPercent }}%</span>
    @endif

    <button type="button" class="cat-x__card-wishlist"
        data-tech-wishlist
        data-product-id="{{ $productId }}"
        data-product-name="{{ $productName }}"
        data-product-price="{{ number_format($finalPrice, 0, ',', '.') }}đ"
        data-product-image="{{ $imagePath }}"
        data-product-url="{{ $productUrl }}"
        aria-label="Thêm vào yêu thích">
        <i class="fa fa-heart-o"></i>
    </button>

    <a href="{{ $productUrl }}" class="cat-x__card-link">
        <div class="cat-x__card-media">
            <img src="{{ $imagePath }}" alt="{{ $productName }}" loading="lazy">
        </div>

        @if ($productBrand)
            <div class="cat-x__card-brand">{{ $productBrand }}</div>
        @endif

        <h3 class="cat-x__card-title">{{ $productName }}</h3>

        <div class="cat-x__card-prices">
            @if ($hasSale)
                <span class="cat-x__card-old-price">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
            @endif
            <span class="cat-x__card-price">{{ number_format($finalPrice, 0, ',', '.') }}đ</span>
        </div>
    </a>

    <div class="cat-x__card-foot">
        <span class="cat-x__card-rating">
            <span class="stars" aria-label="{{ number_format($rating, 1) }} sao">
                @for ($i = 0; $i < $fullStars; $i++)
                    ★
                @endfor

                @if ($halfStar)
                    ★
                @endif

                @for ($i = 0; $i < $emptyStars; $i++)
                    <span class="o">★</span>
                @endfor
            </span>
            <span>({{ $reviewCount }})</span>
        </span>
        <button type="button" class="cat-x__card-cart"
            onclick="if(typeof Addtocart==='function'){Addtocart({{ $productId }});}"
            aria-label="Thêm {{ $productName }} vào giỏ">
            <i class="fa fa-shopping-cart"></i>
        </button>
    </div>
</article>
