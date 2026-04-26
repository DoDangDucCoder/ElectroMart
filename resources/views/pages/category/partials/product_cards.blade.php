@php
    $emptyMessage = $emptyMessage ?? 'Hiện chưa có sản phẩm nào.';
    $emptyMessageClasses = $emptyMessageClasses ?? 'text-center text-muted';
    $emptyStateIcon = $emptyStateIcon ?? 'fa fa-cubes';
    $emptyStateActionText = $emptyStateActionText ?? 'Về trang chủ';
    $emptyStateActionUrl = $emptyStateActionUrl ?? url('/');
    $emptyStateClass = $emptyStateClass ?? 'shopw-product-empty';
    $cardColumnClass = $cardColumnClass ?? 'col-sm-3';
    $showQuickView = $showQuickView ?? false;
    $showCartToggle = $showCartToggle ?? false;
    $showWishlist = $showWishlist ?? true;
    $showCompare = $showCompare ?? true;
@endphp

@forelse ($products as $product)
    @php
        $productId = $product->product_id;
        $productName = \App\Support\StorefrontLabel::neutralizeSource($product->product_name);
        $productUrl = url('chi-tiet/' . $product->product_slug);
        $imagePath = \App\Support\AssetUrl::product($product->product_image);
        $priceFormatted = number_format($product->final_price ?? $product->product_price, 0, ',', '.') . 'đ';
        $productBrandRaw = optional($product->brand)->brand_name ?? ($product->brand_name ?? null);
        $productBrand = \App\Support\StorefrontLabel::neutralizeSource($productBrandRaw);
    @endphp
    <div class="{{ $cardColumnClass }}">
        <div class="product-image-wrapper shopw-product-card">
            <div class="single-products">
                <div class="productinfo text-center shopw-product-shell">
                    <form class="shopw-product-form">
                        @csrf
                        <input type="hidden" value="{{ $productId }}" class="cart_product_id_{{ $productId }}">
                        <input type="hidden" id="wishlist_productname{{ $productId }}" value="{{ $productName }}"
                            class="cart_product_name_{{ $productId }}">
                        <input type="hidden" value="{{ $imagePath }}"
                            class="cart_product_image_{{ $productId }}">
                        <input type="hidden" value="{{ $product->product_quantity }}"
                            class="cart_product_quantity_{{ $productId }}">
                        <input type="hidden" id="wishlist_productprice{{ $productId }}" value="{{ $priceFormatted }}">
                        <input type="hidden" value="{{ $product->product_price }}"
                            class="cart_product_price_{{ $productId }}">
                        <input type="hidden" value="1" class="cart_product_qty_{{ $productId }}">
                        <a id="wishlist_producturl{{ $productId }}" href="{{ $productUrl }}" class="hidden"
                            aria-hidden="true"></a>

                        @if ($showWishlist)
                            <button type="button" class="shopw-wishlist-float"
                                onclick="add_wishlist({{ $productId }})"
                                aria-label="Thêm {{ $productName }} vào danh sách yêu thích">
                                <i class="fa fa-heart-o"></i>
                            </button>
                        @endif

                        @if ($product->sale_amount > 0)
                            <div class="shopw-product-badge-saving">
                                <span>TIẾT KIỆM</span>
                                {{ number_format($product->sale_amount, 0, ',', '.') }}đ
                            </div>
                        @endif

                        <a href="{{ $productUrl }}" class="shopw-product-link">
                            <span class="shopw-product-media">
                                <img id="wishlist_productimage{{ $productId }}" src="{{ $imagePath }}"
                                    alt="{{ $productName }}" />
                            </span>
                            <span class="shopw-product-copy">
                                @if ($productBrand)
                                    <span class="shopw-product-brand-label">{{ $productBrand }}</span>
                                @endif
                                <span class="shopw-product-title">{{ $productName }}</span>
                                
                                <div class="shopw-product-price-row">
                                    @include('partials.storefront.product-price', [
                                        'product' => $product,
                                        'currency' => 'VND',
                                        'showExplanation' => true,
                                        'compact' => true,
                                    ])
                                </div>
                                
                                @if(isset($product->meta['specs_preview']) && is_array($product->meta['specs_preview']))
                                    <span class="shopw-product-specs-preview">
                                        @foreach($product->meta['specs_preview'] as $spec)
                                            <span class="shopw-product-spec-item"><i class="fa {{ $spec['icon'] ?? 'fa-info-circle' }}"></i> {{ \App\Support\StorefrontLabel::neutralizeSource($spec['label'] ?? '') }}</span>
                                        @endforeach
                                    </span>
                                @endif
                            </span>
                        </a>

                        @if(isset($product->meta['specs']) && is_array($product->meta['specs']))
                            <div class="shopw-product-hover-info">
                                <div class="shopw-hover-info-title">Thông số kỹ thuật</div>
                                <ul class="shopw-hover-info-list">
                                    @foreach($product->meta['specs'] as $spec)
                                        @php
                                            $specLabel = \App\Support\StorefrontLabel::neutralizeSource($spec['label'] ?? '');
                                            $specValue = $spec['value'] ?? '';
                                            if (is_array($specValue)) {
                                                $specValue = implode(', ', array_filter(array_map(static fn($value) => is_scalar($value) ? (string) $value : '', $specValue)));
                                            }
                                            $specValue = \App\Support\StorefrontLabel::neutralizeSource((string) $specValue);
                                        @endphp
                                        <li class="shopw-hover-info-item">
                                            <i class="fa {{ $spec['icon'] ?? 'fa-check' }}"></i>
                                            <span>{{ $specLabel }}: {{ $specValue }}</span>
                                        </li>
                                    @endforeach
                                    <li class="shopw-hover-info-item">
                                        <i class="fa fa-info-circle"></i>
                                        <span>{{ Str::limit(\App\Support\StorefrontLabel::neutralizeSource(strip_tags((string) $product->product_desc)), 60) }}</span>
                                    </li>
                                </ul>
                            </div>
                        @endif
                        <div class="shopw-card-actions">
                            <button type="button" class="btn btn-default shopw-card-btn shopw-card-btn-outlined"
                                data-id_product="{{ $productId }}" onclick="Addtocart({{ $productId }});">
                                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                <span>Thêm vào giỏ</span>
                            </button>
                            @if ($showCompare)
                                <button type="button" class="btn btn-default shopw-card-btn shopw-card-btn-ghost"
                                    onclick="add_compare({{ $productId }});"
                                    aria-label="Thêm {{ $productName }} vào danh sách so sánh">
                                    <i class="fa fa-exchange" aria-hidden="true"></i>
                                    <span>So sánh</span>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-sm-12">
        @include('partials.storefront.empty-state', [
            'title' => $emptyMessage,
            'message' => 'Hãy thử chuyển danh mục, thay đổi bộ lọc hoặc quay lại trang chủ để khám phá thêm sản phẩm.',
            'icon' => $emptyStateIcon,
            'compact' => true,
            'class' => trim($emptyStateClass . ' ' . $emptyMessageClasses),
            'actionText' => $emptyStateActionText,
            'actionUrl' => $emptyStateActionUrl,
        ])
    </div>
@endforelse
