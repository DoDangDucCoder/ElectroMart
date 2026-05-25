@php
    $formatPrice = static fn ($amount) => number_format((float) $amount, 0, ',', '.') . 'đ';
    $compact = $compact ?? false;

    $value = static function ($key, $default = null) use ($product) {
        return data_get($product, $key, $default);
    };

    $productId = (string) ($value('id') ?? $value('product_id') ?? \Illuminate\Support\Str::slug((string) ($value('name') ?? $value('product_name') ?? 'product')));
    $productName = (string) ($value('name') ?? $value('product_name') ?? 'Sản phẩm công nghệ');
    $productName = \App\Support\StorefrontLabel::neutralizeSource($productName);

    $slug = $value('product_slug') ?? $value('slug');
    $productUrl = $value('url') ?? ($slug ? url('/chi-tiet/' . $slug) : '#');

    $rawImage = $value('image') ?? $value('product_image') ?? $value('thumbnail') ?? $value('thumbnail_path');
    $productImage = \App\Support\AssetUrl::productVariant($rawImage, 'thumb');

    $brandRaw = $value('brand') ?? $value('brand.brand_name') ?? $value('brand.name') ?? $value('brand_name') ?? 'DangDuc';
    if (is_object($brandRaw) || is_array($brandRaw)) {
        $brandRaw = data_get($brandRaw, 'brand_name') ?? data_get($brandRaw, 'name') ?? 'DangDuc';
    }
    $productBrand = \App\Support\StorefrontLabel::neutralizeSource((string) $brandRaw);
    $brandInitials = (string) ($value('brand_initials') ?? mb_strtoupper(mb_substr($productBrand ?: 'DD', 0, 2)));

    $productCategoryId = $value('category_id') ?? $value('primary_category_id') ?? $value('primaryCategory.id') ?? $value('categories.0.id') ?? '';
    $productCategoryName = $value('category') ?? $value('primaryCategory.category_name') ?? $value('categories.0.category_name') ?? '';

    $rawFinalPrice = $value('final_price');
    $salePrice = (float) ($value('sale_price') ?? 0);
    $arrayPrice = $value('price');
    $productPrice = $value('product_price');

    $originalPrice = (float) ($value('original_price') ?? $productPrice ?? $arrayPrice ?? $rawFinalPrice ?? $salePrice);
    $price = (float) ($rawFinalPrice ?? (($salePrice > 0) ? $salePrice : ($arrayPrice ?? $productPrice ?? $originalPrice)));

    if ($price <= 0) {
        $price = $originalPrice;
    }
    if ($originalPrice <= 0) {
        $originalPrice = $price;
    }

    $discountPercent = (int) ($value('discount_percent') ?? 0);
    if ($discountPercent <= 0 && $originalPrice > 0 && $price > 0 && $price < $originalPrice) {
        $discountPercent = (int) round((($originalPrice - $price) / $originalPrice) * 100);
    }

    $rating = max(0, min(5, (float) ($value('rating') ?? $value('rating_average') ?? $value('avg_rating') ?? 4.5)));
    $reviewCount = (int) ($value('review_count') ?? $value('ratings_count') ?? 128);
    $fullStars = (int) floor($rating);
    $emptyStars = max(0, 5 - $fullStars);

    $stockValue = $value('product_quantity') ?? $value('stock') ?? null;
    $isActive = $value('is_active');
    $isOutOfStock = ($stockValue !== null && (int) $stockValue <= 0) || ($isActive !== null && ! (bool) $isActive);
    $isHot = (bool) $value('is_hot', false);
@endphp

<article class="tech-product-card {{ $compact ? 'tech-product-card-compact' : '' }}" data-product-url="{{ $productUrl }}">
    @if ($isHot)
        <span class="tech-hot-ribbon">Nổi bật</span>
    @endif

    @if ($discountPercent > 0)
        <span class="tech-flash-sale-badge">-{{ $discountPercent }}%</span>
    @endif

    <button type="button"
        class="tech-wishlist-btn"
        aria-label="Yêu thích {{ $productName }}"
        aria-pressed="false"
        onpointerdown="event.preventDefault(); event.stopPropagation();"
        onmousedown="event.preventDefault(); event.stopPropagation();"
        onclick="event.preventDefault(); event.stopPropagation();"
        data-tech-wishlist
        data-product-id="{{ $productId }}"
        data-product-name="{{ $productName }}"
        data-product-price="{{ $formatPrice($price) }}"
        data-product-image="{{ $productImage }}"
        data-product-url="{{ $productUrl }}"
        data-product-category-id="{{ $productCategoryId }}"
        data-product-category="{{ $productCategoryName }}">
        <i class="fa fa-heart-o"></i>
    </button>

    <a href="{{ $productUrl }}" class="tech-product-media">
        @if (!empty($productImage))
            <img src="{{ $productImage }}" alt="{{ $productName }}" loading="lazy" decoding="async">
        @else
            <span class="tech-product-placeholder">{{ $brandInitials }}</span>
        @endif
    </a>

    <div class="tech-product-body">
        <div class="tech-product-brand-row">
            <span class="tech-brand-chip"><span>{{ $brandInitials }}</span></span>
            <strong>{{ $productBrand ?: 'DANGDUC' }}</strong>
        </div>

        <h3>
            <a href="{{ $productUrl }}">{{ $productName }}</a>
        </h3>

        <div class="tech-product-rating">
            @for ($i = 0; $i < $fullStars; $i++)
                <i class="fa fa-star"></i>
            @endfor
            @for ($i = 0; $i < $emptyStars; $i++)
                <i class="fa fa-star-o"></i>
            @endfor
            <span>({{ $reviewCount }})</span>
        </div>

        <div class="tech-product-footer">
            <div class="tech-price-block">
                @if ($originalPrice > $price)
                    <span class="tech-old-price">{{ $formatPrice($originalPrice) }}</span>
                @endif
                <div class="tech-sale-price">{{ $formatPrice($price) }}</div>
            </div>

            <button type="button"
                class="tech-add-cart-btn"
                data-cart-product-id="{{ $productId }}"
                @disabled($isOutOfStock)
                aria-label="{{ $isOutOfStock ? 'Sản phẩm tạm hết hàng' : 'Thêm ' . $productName . ' vào giỏ' }}">
                <i class="fa {{ $isOutOfStock ? 'fa-ban' : 'fa-shopping-cart' }}"></i>
            </button>
        </div>
    </div>
</article>
