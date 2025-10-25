@extends('admin_layout')
@section('admin_content')
    @php
        $rootCategories = isset($category) ? $category : ($rootCategories ?? collect());
    @endphp
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Thêm danh mục sản phẩm
                </header>
                @if (session('message'))
                    <span class="text-alert">{{ session('message') }}</span>
                    {{ session()->forget('message') }}
                @endif
                <div class="panel-body">
                    <div class="position-center">
                        <form role="form" action="{{ URL::to('/save-category-product') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="category_product_name">Tên danh mục</label>
                                <input type="text" class="form-control" onkeyup="ChangeToSlug();"
                                    name="category_product_name" id="category_product_name" placeholder="Tên danh mục">
                            </div>
                            <div class="form-group">
                                <label for="convert_slug">Slug</label>
                                <input type="text" name="slug_category_product" class="form-control" id="convert_slug"
                                    placeholder="slug-danh-muc">
                            </div>
                            <div class="form-group">
                                <label for="category_product_desc">Mô tả danh mục</label>
                                <textarea style="resize: none" rows="4" class="form-control" name="category_product_desc"
                                    id="category_product_desc" placeholder="Mô tả danh mục"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="category_product_keywords">Từ khóa danh mục</label>
                                <textarea style="resize: none" rows="3" class="form-control" name="category_product_keywords"
                                    id="category_product_keywords" placeholder="Từ khóa"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="category_parent">Thuộc danh mục</label>
                                <select name="category_parent" id="category_parent" class="form-control input-sm m-bot15">
                                    <option value="0">---Danh mục cha---</option>
                                    @foreach ($rootCategories as $val)
                                        <option value="{{ $val->category_id }}">{{ $val->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="category_product_status">Hiển thị</label>
                                <select name="category_product_status" id="category_product_status"
                                    class="form-control input-sm m-bot15">
                                    <option value="0">Hiển thị</option>
                                    <option value="1">Ẩn</option>
                                </select>
                            </div>

                            <button type="submit" name="add_category_product" class="btn btn-info">Thêm danh mục</button>
                        </form>
                    </div>

                </div>
            </section>
        </div>
@endsection
