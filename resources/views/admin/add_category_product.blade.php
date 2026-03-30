@extends('admin_layout')

@section('admin_content')
    @php
        $rootCategories = isset($category) ? $category : ($rootCategories ?? collect());
    @endphp

    <div class="g-admin-page-shell g-admin-category-product-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Danh mục sản phẩm</span>
                <h1>Thêm danh mục sản phẩm</h1>
                <p>Tạo danh mục mới để sắp xếp sản phẩm theo nhóm hiển thị và hỗ trợ điều hướng trên website.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($rootCategories->count()) }}</strong>
                    <span>Danh mục cha có thể chọn</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin danh mục</strong>
                    <p>Điền tên, slug, mô tả và chọn danh mục cha nếu muốn tạo cấp con.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                    {{ session()->forget('message') }}
                @endif

                <form role="form" action="{{ URL::to('/save-category-product') }}" method="post"
                    class="g-admin-site-info-form">
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
                            <option value="0">--- Danh mục cha ---</option>
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

                    <div class="g-admin-inline-actions">
                        <button type="submit" name="add_category_product" class="btn btn-info">Thêm danh mục</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
