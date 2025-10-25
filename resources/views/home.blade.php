@extends('layout')

@section('slider')
    @include('pages.include.slider')
@endsection

@section('sidebar')
    @include('pages.include.sidebar')
@endsection

@section('content')
    <div class="features_items"><!--features_items-->

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
                                href="#tshirt" data-toggle="tab">{{ $cat_tab->category_name }}</a></li>
                    @endforeach

                </ul>
            </div>

            <div id="tabs_product"></div>

        </div>

        <h2 class="title text-center">Sản phẩm mới nhất</h2>

        <div id="all_product"></div>

        <div id="cart_session"></div>

    </div><!--features_items-->
@endsection
