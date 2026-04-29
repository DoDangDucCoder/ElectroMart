@extends('layout')

@section('body_class', 'g-home')

@section('content')
    <div class="features_items"><!--features_items-->
        @php($storefrontLabel = \App\Support\StorefrontLabel::class)

        <div class="category-tab"><!--category-tab-->
            <div class="col-sm-12">
                <ul class="nav nav-tabs">
                    @php
                        $i = 0;
                    @endphp


                    @foreach ($cate_pro_tabs as $key => $cat_tab)
                        @php
                            $i++;
                        @endphp
                        <li class="tabs_pro {{ $i == 1 ? 'active' : '' }}" data-id="{{ $cat_tab->category_id }}"><a
                                href="#tshirt" data-toggle="tab">{{ $storefrontLabel::category($cat_tab->category_name, $cat_tab->slug_category_product ?? null) }}</a></li>
                    @endforeach

                </ul>
            </div>

            <div id="tabs_product"></div>

        </div>


        <h2 class="title text-center">Sản phẩm mới nhất</h2>

        <div id="all_product"></div>

        <div id="cart_session"></div>


    </div><!--features_items-->
    <style type="text/css">
        div#quick-cart {
            margin-top: 60px;
        }
    </style>

    <!-- Modal giỏ hàng -->
    <div class="modal fade" id="quick-cart" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Giỏ hàng của bạn</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="show_quick_cart_alert"></div>
                    <div id="show_quick_cart"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>

                </div>
            </div>
        </div>
    </div>
    <!-- Modal xem nhanh-->
    <div class="modal fade" style="margin-top:50px" id="xemnhanh" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title product_quickview_title">
                        <span id="product_quickview_title"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="product_quickview_body">
                    {{-- Content injected via AJAX --}}
                    <div class="text-center p-5">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                        <p>Đang tải thông tin sản phẩm...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-default redirect-cart">Đi tới giỏ hàng</button>
                </div>
            </div>
        </div>
    </div>
    {{--  <ul class="pagination pagination-sm m-t-none m-b-none">
                       {!!$all_product->links()!!}
                      </ul> --}}
    <!--/recommended_items-->
@endsection
