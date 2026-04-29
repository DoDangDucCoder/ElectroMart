@php
    $storefrontLabel = \App\Support\StorefrontLabel::class;
@endphp

<style type="text/css">
    .quickview-modal-content img {
        width: 100%;
    }
    @media screen and (min-width: 768px) {
        #xemnhanh .modal-dialog {
            width: 700px;
        }
    }
    @media screen and (min-width: 992px) {
        #xemnhanh .modal-lg {
            width: 1200px;
        }
    }
</style>

<div class="row quickview-modal-content">
    <div class="col-md-5">
        {{-- Main Image --}}
        <span id="product_quickview_image">
            <p><img width="100%" src="{{ \App\Support\AssetUrl::product($product->product_image) }}" alt="Hình ảnh sản phẩm"></p>
        </span>
        
        {{-- Gallery --}}
        <span id="product_quickview_gallery">
            @if ($mediaItems->isNotEmpty())
                @foreach ($mediaItems as $media)
                    @php
                        $imagePath = $media->file_path ?? $fallbackPath;
                        $imageUrl = \App\Support\AssetUrl::product($imagePath);
                        $imageAlt = $storefrontLabel::neutralizeSource($media->gallery_name ?? 'Hình ảnh sản phẩm');
                    @endphp
                    @if ($imagePath)
                        <p><img width="100%" src="{{ $imageUrl }}" alt="{{ $imageAlt }}"></p>
                    @endif
                @endforeach
            @else
                <p><img width="100%" src="{{ $fallbackImage }}" alt="Hình ảnh sản phẩm"></p>
            @endif
        </span>
    </div>
    
    <form>
        @csrf
        {{-- Hidden fields for Cart --}}
        <div id="product_quickview_value">
            <input type="hidden" value="{{ $product->product_id }}" class="cart_product_id_{{ $product->product_id }}">
            <input type="hidden" value="{{ $storefrontLabel::neutralizeSource($product->product_name) }}" class="cart_product_name_{{ $product->product_id }}">
            <input type="hidden" value="{{ $product->product_quantity }}" class="cart_product_quantity_{{ $product->product_id }}">
            <input type="hidden" value="{{ \App\Support\AssetUrl::product($product->product_image) }}" class="cart_product_image_{{ $product->product_id }}">
            <input type="hidden" value="{{ $product->final_price }}" class="cart_product_price_{{ $product->product_id }}">
            {{-- This hidden qty field is kept for backward compatibility with JS that looks for it, 
                 but we'll also update the visible input to have the same class. --}}
            <input type="hidden" value="1" class="cart_product_qty_{{ $product->product_id }}">
        </div>

        <div class="col-md-7">
            <h2>{{ $storefrontLabel::neutralizeSource($product->product_name) }}</h2>
            <p>Mã ID: <span id="product_quickview_id">{{ $product->product_id }}</span></p>
            <p style="font-size: 20px; color: brown;font-weight: bold;">
                Giá sản phẩm : 
                <span id="product_quickview_price">
                    @include('partials.storefront.product-price', [
                        'product' => $product,
                        'currency' => 'VND',
                        'showExplanation' => true,
                        'compact' => true,
                    ])
                </span>
            </p>

            <label>Số lượng:</label>
            {{-- Added the dynamic class cart_product_qty_{id} to the visible input so JS can pick up the actual value --}}
            <input name="qty" type="number" min="1" class="cart_product_qty_ cart_product_qty_{{ $product->product_id }}" value="1" />

            <h4 style="font-size: 20px; color: brown;font-weight: bold;">Mô tả sản phẩm</h4>
            <hr>
            <p><span id="product_quickview_desc">{!! $storefrontLabel::neutralizeSourceHtml($product->product_desc) !!}</span></p>
            <p><span id="product_quickview_content">{!! $storefrontLabel::neutralizeSourceHtml($product->product_content) !!}</span></p>

            <div id="product_quickview_button">
                <input type="button" value="Mua ngay" class="btn btn-primary btn-sm add-to-cart-quickview" id="buy_quickview"
                    data-id_product="{{ $product->product_id }}" name="add-to-cart">
            </div>
            <div id="beforesend_quickview"></div>
        </div>
    </form>
</div>
