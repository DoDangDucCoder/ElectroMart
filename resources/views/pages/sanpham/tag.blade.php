@extends('layout')
@section('content')
    <div class="features_items"><!--features_items-->

        <h2 class="title text-center">Tag tìm kiếm : {{ $product_tag }}</h2>

        @foreach ($pro_tag as $key => $product)
            @php
                $productId = $product->product_id;
                $productUrl = URL::to('/chi-tiet/' . $product->product_slug);
                $priceFormatted = number_format($product->final_price ?? $product->product_price, 0, ',', '.') . ' VND';
            @endphp
            <div class="col-sm-4">
                <div class="product-image-wrapper">

                    <div class="single-products">
                        <div class="productinfo text-center">
                            <form>
                                @csrf
                                <input type="hidden" value="{{ $product->product_id }}"
                                    class="cart_product_id_{{ $product->product_id }}">
                                <input type="hidden" id="wishlist_productname{{ $productId }}" value="{{ $product->product_name }}"
                                    class="cart_product_name_{{ $product->product_id }}">

                                <input type="hidden" value="{{ $product->product_quantity }}"
                                    class="cart_product_quantity_{{ $product->product_id }}">
                                <input type="hidden" id="wishlist_productprice{{ $productId }}" value="{{ $priceFormatted }}">

                                <input type="hidden" value="{{ $product->product_image }}"
                                    class="cart_product_image_{{ $product->product_id }}">
                                <input type="hidden" value="{{ $product->product_price }}"
                                    class="cart_product_price_{{ $product->product_id }}">
                                <input type="hidden" value="1" class="cart_product_qty_{{ $product->product_id }}">
                                <a id="wishlist_producturl{{ $productId }}" href="{{ $productUrl }}"></a>

                                <a href="{{ $productUrl }}">
                                    <img id="wishlist_productimage{{ $productId }}" src="{{ $product->product_image ? asset($product->product_image) : '' }}"
                                        alt="" />
                                    <div class="shopw-tag-price">
                                        @include('partials.storefront.product-price', [
                                            'product' => $product,
                                            'currency' => 'VND',
                                            'showExplanation' => true,
                                            'compact' => true,
                                        ])
                                    </div>
                                    <p>{{ $product->product_name }}</p>


                                </a>
                                <input type="button" onclick="Addtocart({{ $productId }});" value="Thêm giỏ hàng" class="btn btn-default"
                                    data-id_product="{{ $product->product_id }}" name="add-to-cart">
                            </form>

                        </div>

                    </div>

                    <div class="choose">
                        <ul class="shopw-card-meta-list">
                            <li class="shopw-card-meta-item">
                                <button type="button" class="shopw-card-meta-link shopw-card-meta-link--wishlist"
                                    onclick="add_wistlist({{ $productId }})">
                                    <i class="fa fa-heart-o" aria-hidden="true"></i>
                                    <span>Yêu thích</span>
                                </button>
                            </li>
                            <li class="shopw-card-meta-item">
                                <button type="button" class="shopw-card-meta-link shopw-card-meta-link--compare"
                                    onclick="add_compare({{ $productId }})">
                                    <i class="fa fa-exchange" aria-hidden="true"></i>
                                    <span>So sánh</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div><!--features_items-->
    {{--   <ul class="pagination pagination-sm m-t-none m-b-none">
                       {!!$all_product->links()!!}
                      </ul> --}}
    <!--/recommended_items-->
@endsection


