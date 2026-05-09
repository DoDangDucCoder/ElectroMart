@php
    $formatPrice = static fn ($amount) => number_format((float) $amount, 0, ',', '.') . 'đ';
    $compact = $compact ?? false;
    $productName = (string) ($product['name'] ?? 'Sản phẩm công nghệ');
    $productId = (string) ($product['id'] ?? \Illuminate\Support\Str::slug($productName));
    $productUrl = $product['url'] ?? '#';
    $productImage = $product['image'] ?? asset('uploads/product/no-image.svg');
    $productBrand = $product['brand'] ?? 'DangDuc';
    $originalPrice = (float) ($product['original_price'] ?? 0);
    $price = (float) ($product['price'] ?? 0);

    if ($price <= 0) {
        $price = $originalPrice;
    }

    $discountPercent = (int) ($product['discount_percent'] ?? 0);

    if ($discountPercent <= 0 && $originalPrice > 0 && $price > 0 && $price < $originalPrice) {
        $discountPercent = (int) round((($originalPrice - $price) / $originalPrice) * 100);
    }
@endphp

<article class="tech-product-card {{ $compact ? 'tech-product-card-compact' : '' }}" data-product-url="{{ $productUrl }}">
    @if (!empty($product['is_hot']))
        <span class="tech-hot-ribbon">Nổi bật</span>
    @endif

    @if ($compact && $discountPercent > 0)
        <span class="tech-flash-sale-badge">-{{ $discountPercent }}%</span>
    @endif

    <button type="button"
        class="tech-wishlist-btn"
        aria-label="Yêu thích {{ $productName }}"
        aria-pressed="false"
        data-tech-wishlist
        data-product-id="{{ $productId }}"
        data-product-name="{{ $productName }}"
        data-product-price="{{ $formatPrice($price) }}"
        data-product-image="{{ $productImage }}"
        data-product-url="{{ $productUrl }}">
        <i class="fa fa-heart-o"></i>
    </button>

    <a href="{{ $productUrl }}" class="tech-product-media">
        @if (!empty($productImage))
            <img src="{{ $productImage }}" alt="{{ $productName }}">
        @else
            <span class="tech-product-placeholder">{{ $product['brand_initials'] ?? 'SW' }}</span>
        @endif
    </a>

    <div class="tech-product-body">
        <div class="tech-product-brand-row">
            <span class="tech-brand-chip">
                @if (!empty($product['brand_logo']))
                    <img src="{{ $product['brand_logo'] }}" alt="{{ $productBrand }}">
                @else
                    <span>{{ $product['brand_initials'] ?? 'SW' }}</span>
                @endif
            </span>
            <strong>{{ $productBrand }}</strong>
        </div>

        <h3>
            <a href="{{ $productUrl }}">{{ $productName }}</a>
        </h3>

        <div class="tech-product-rating">
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star-o"></i>
            <span>(128)</span>
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
                aria-label="Thêm {{ $productName }} vào giỏ">
                <i class="fa fa-shopping-cart"></i>
            </button>
        </div>
    </div>
</article>
