@extends('layout')

{{-- @section('attribute')
  @include('pages.include.attribute')
@endsection --}}

@section('content_category')
    <div class="features_items"><!--features_items-->
        <div class="fb-share-button" data-href="http://localhost/tutorial_youtube/shopbanhanglaravel"
            data-layout="button_count" data-size="small"><a target="_blank"
                href="https://www.facebook.com/sharer/sharer.php?u={{ $url_canonical }}&amp;src=sdkpreparse"
                class="fb-xfbml-parse-ignore">Chia sẻ</a></div>
        <div class="fb-like" data-href="{{ $url_canonical }}" data-width="" data-layout="button_count" data-action="like"
            data-size="small" data-share="false"></div>

        @foreach ($category_name as $key => $name)
            <h2 class="title text-center">{{ $name->category_name }}</h2>
        @endforeach
        <div class="row">

            <div class="col-md-4">

                <label for="amount">Sắp xếp theo</label>

                <form>
                    @csrf

                    <select name="sort" id="sort" class="form-control">
                        <option value="{{ Request::url() }}" {{ $selected_sort === 'none' ? 'selected' : '' }}>--Lọc theo--
                        </option>
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'tang_dan']) }}"
                            {{ $selected_sort === 'tang_dan' ? 'selected' : '' }}>--Giá tăng dần--</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'giam_dan']) }}"
                            {{ $selected_sort === 'giam_dan' ? 'selected' : '' }}>--Giá giảm dần--</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'kytu_az']) }}"
                            {{ $selected_sort === 'kytu_az' ? 'selected' : '' }}>Lọc theo tên A đến Z</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'kytu_za']) }}"
                            {{ $selected_sort === 'kytu_za' ? 'selected' : '' }}>Lọc theo tên Z đến A</option>
                    </select>

                </form>

            </div>

            <div class="col-md-4">

                <label for="amount">Lọc giá theo</label>

                <form>
                    <div id="slider-range"></div>
                    <style type="text/css">
                        .style-range p {
                            float: left;
                            width: 25%;
                        }
                    </style>
                    <div class="style-range">
                        <p><input type="text" id="amount_start" readonly value="{{ $filter_start_price }}"
                                style="border:0; color:#f6931f; font-weight:bold;"></p>
                        <p><input type="text" id="amount_end" readonly value="{{ $filter_end_price }}"
                                style="border:0; color:#f6931f; font-weight:bold;"></p>
                    </div>
                    <input type="hidden" name="start_price" id="start_price" value="{{ $filter_start_price }}">
                    <input type="hidden" name="end_price" id="end_price" value="{{ $filter_end_price }}">

                    <br>
                    <div class="clearfix"></div>
                    <input type="submit" name="filter_price" value="Lọc giá" class="btn btn-sm btn-default">
                </form>

            </div>



        </div>
        @include('pages.category.partials.product_grid', [
            'products' => $category_by_id,
            'emptyMessage' => 'Hiện chưa có sản phẩm nào trong danh mục này.',
        ])
    </div><!--features_items-->
    {{--  <ul class="pagination pagination-sm m-t-none m-b-none">
                       {!!$category_by_id->links()!!}
                    </ul> --}}

    <!--/recommended_items-->
@endsection


