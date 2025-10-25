@php
    $emptyMessage = $emptyMessage ?? 'Hiện chưa có sản phẩm nào.';
    $emptyMessageClasses = $emptyMessageClasses ?? 'text-center text-muted';
@endphp

@forelse ($products as $product)
    @php
        $productId = $product->product_id;
        $productName = $product->product_name;
        $productUrl = url('chi-tiet/' . $product->product_slug);
        $imagePath = $product->product_image ? asset($product->product_image) : asset('frontend/images/no-image.png');
        $priceFormatted = number_format($product->product_price, 0, ',', '.') . ' VND';
    @endphp
    <div class="col-sm-3">
        <div class="product-image-wrapper">
            <div class="single-products">
                <div class="productinfo text-center">
                    <form>
                        @csrf
                        <input type="hidden" value="{{ $productId }}" class="cart_product_id_{{ $productId }}">
                        <input type="hidden" id="wishlist_productname{{ $productId }}" value="{{ $productName }}"
                            class="cart_product_name_{{ $productId }}">
                        <input type="hidden" value="{{ $product->product_image }}"
                            class="cart_product_image_{{ $productId }}">
                        <input type="hidden" value="{{ $product->product_quantity }}"
                            class="cart_product_quantity_{{ $productId }}">
                        <input type="hidden" id="wishlist_productprice{{ $productId }}" value="{{ $priceFormatted }}">
                        <input type="hidden" value="{{ $product->product_price }}"
                            class="cart_product_price_{{ $productId }}">
                        <input type="hidden" value="1" class="cart_product_qty_{{ $productId }}">
                        <a id="wishlist_producturl{{ $productId }}" href="{{ $productUrl }}"></a>
                        <a href="{{ $productUrl }}">
                            <img src="{{ $imagePath }}" alt="{{ $productName }}" />
                            <h2>{{ $priceFormatted }}</h2>
                            <p class="tieude-product_name">{{ $productName }}</p>
                        </a>
                        <button type="button" class="btn btn-default" data-id_product="{{ $productId }}"
                            onclick="Addtocart({{ $productId }});">
                            <i class="fa fa-shopping-cart"></i>Thêm giỏ hàng
                        </button>
                    </form>
                </div>
            </div>
            <div class="choose">
                <ul class="nav nav-pills nav-justified">
                    <li><a href="#"><i class="fa fa-plus-square"></i>Yêu thích</a></li>
                    <li><a href="#"><i class="fa fa-plus-square"></i>So sánh</a></li>
                </ul>
            </div>
        </div>
    </div>
@empty
    <div class="col-sm-12">
        <p class="{{ $emptyMessageClasses }}">{{ $emptyMessage }}</p>
    </div>
@endforelse
