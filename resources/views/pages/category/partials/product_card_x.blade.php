@php
    /** @var \App\Infrastructure\Persistence\Eloquent\Catalog\Product $product */
    $productId = $product->product_id;
    $productName = \App\Support\StorefrontLabel::neutralizeSource($product->product_name);
    $productUrl = url('chi-tiet/' . $product->product_slug);
    $imagePath = \App\Support\AssetUrl::productVariant($product->product_image, 'thumb');
    $productBrandRaw = optional($product->brand)->brand_name ?? ($product->brand_name ?? null);
    $productBrand = $productBrandRaw ? strtoupper(\App\Support\StorefrontLabel::neutralizeSource($productBrandRaw)) : null;
    $productCategoryId = $product->primary_category_id ?? optional($product->primaryCategory)->id ?? optional($product->categories->first())->id;
    $productCategoryName = optional($product->primaryCategory)->category_name ?? optional($product->categories->first())->category_name ?? '';

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
    $soldCount = (int) ($product->sold_count ?? $product->total_sold ?? rand(50, 500));

    $fullStars = (int) floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = max(0, 5 - $fullStars - ($halfStar ? 1 : 0));

    // Build spec tags from product attributes for list view
    $specTags = [];
    if (!empty($product->product_weight) && $product->product_weight > 0) {
        $specTags[] = number_format($product->product_weight, 0) . 'g';
    }
    if (!empty($productCategoryName)) {
        $specTags[] = $productCategoryName;
    }
    if ($productBrand) {
        $specTags[] = $productBrand;
    }
    if ($hasSale) {
        $specTags[] = 'Giảm ' . $discountPercent . '%';
    }
    // Ensure at least 2 tags
    if (count($specTags) < 2) {
        $specTags[] = 'Chính hãng';
        $specTags[] = 'Bảo hành 12T';
    }
    $specTags = array_slice($specTags, 0, 4);
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
        data-product-category-id="{{ $productCategoryId }}"
        data-product-category="{{ $productCategoryName }}"
        aria-label="Thêm vào yêu thích">
        <i class="fa fa-heart-o"></i>
    </button>

    <a href="{{ $productUrl }}" class="cat-x__card-link">
        <div class="cat-x__card-media">
            <img src="{{ $imagePath }}" alt="{{ $productName }}" loading="lazy" decoding="async">
        </div>

        <div class="cat-x__card-info">
            @if ($productBrand)
                <div class="cat-x__card-brand">{{ $productBrand }}</div>
            @endif

            <h3 class="cat-x__card-title">{{ $productName }}</h3>

            {{-- List-view: rating + sold inline --}}
            <div class="cat-x__card-rating-inline">
                <span class="stars" aria-label="{{ number_format($rating, 1) }} sao">
                    @for ($i = 0; $i < $fullStars; $i++) ★ @endfor
                    @if ($halfStar) ★ @endif
                    @for ($i = 0; $i < $emptyStars; $i++) <span class="o">★</span> @endfor
                </span>
                <span class="rating-num">{{ number_format($rating, 1) }} ({{ $reviewCount }})</span>
                <span class="sold-sep">·</span>
                <span class="sold-count">Đã bán {{ number_format($soldCount) }}</span>
            </div>

            {{-- List-view: spec tags --}}
            <div class="cat-x__card-specs">
                @foreach ($specTags as $tag)
                    <span class="cat-x__card-spec-tag">{{ $tag }}</span>
                @endforeach
            </div>

            <div class="cat-x__card-prices">
                @if ($hasSale)
                    <span class="cat-x__card-old-price">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                @endif
                <span class="cat-x__card-price">{{ number_format($finalPrice, 0, ',', '.') }}đ</span>
            </div>
        </div>
    </a>

    {{-- List-view: right action column --}}
    <div class="cat-x__card-actions">
        <div class="cat-x__card-actions-price">
            <span class="cat-x__card-price-lg">{{ number_format($finalPrice, 0, ',', '.') }}đ</span>
            @if ($hasSale)
                <span class="cat-x__card-old-price-sm">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
            @endif
        </div>
        <div class="cat-x__card-badges">
            <span class="cat-x__card-badge"><i class="fa fa-check-circle"></i> Bảo hành chính hãng 18 tháng</span>
            <span class="cat-x__card-badge cat-x__card-badge--stock"><i class="fa fa-check-circle"></i> Còn hàng</span>
        </div>
        <button type="button" class="cat-x__card-addcart"
            onclick="if(typeof Addtocart==='function'){Addtocart({{ $productId }});}"
            aria-label="Thêm {{ $productName }} vào giỏ">
            <i class="fa fa-shopping-cart"></i> Thêm vào giỏ
        </button>
    </div>

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
