@extends('admin_layout')

@section('admin_content')
    @php
        $rootCategories = isset($category) ? $category : ($rootCategories ?? collect());
        $registeredUris = collect(app('router')->getRoutes()->getRoutes())->map(fn ($route) => $route->uri());
        $hasCategoryStoreRoute = $registeredUris->contains('save-category-product');
        $categoryLegacyReadonly = ! $hasCategoryStoreRoute;
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
                    <p>Điền tên, đường dẫn tĩnh (slug), mô tả và chọn danh mục cha nếu muốn tạo cấp con.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                @unless ($hasCategoryStoreRoute)
                    <div class="alert alert-warning">
                        Tác vụ lưu danh mục sản phẩm phiên bản cũ hiện chưa được nối route trong bản này. Màn hình chỉ dùng để tham chiếu cấu trúc dữ liệu cũ.
                    </div>
                @endunless

                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                    {{ session()->forget('message') }}
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form role="form" action="{{ $hasCategoryStoreRoute ? URL::to('/save-category-product') : '#' }}" method="post"
                    class="g-admin-site-info-form">
                    @csrf
                    <fieldset @disabled($categoryLegacyReadonly)>
                        <div class="form-group">
                            <label for="category_product_name">Tên danh mục</label>
                            <input type="text" class="form-control" onkeyup="ChangeToSlug();"
                                name="category_product_name" id="category_product_name" placeholder="Tên danh mục"
                                value="{{ old('category_product_name') }}">
                        </div>
                        <div class="form-group">
                            <label for="convert_slug">Đường dẫn tĩnh (slug)</label>
                            <input type="text" name="slug_category_product" class="form-control" id="convert_slug"
                                placeholder="duong-dan-tinh-danh-muc" value="{{ old('slug_category_product') }}">
                        </div>
                        <div class="form-group">
                            <label for="category_product_desc">Mô tả danh mục</label>
                            <textarea style="resize: none" rows="4" class="form-control" name="category_product_desc"
                                id="category_product_desc" placeholder="Mô tả danh mục">{{ old('category_product_desc') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="category_product_keywords">Từ khóa danh mục</label>
                            <textarea style="resize: none" rows="3" class="form-control" name="category_product_keywords"
                                id="category_product_keywords" placeholder="Từ khóa">{{ old('category_product_keywords') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="category_parent">Thuộc danh mục</label>
                            <select name="category_parent" id="category_parent" class="form-control input-sm m-bot15">
                                <option value="0">--- Danh mục cha ---</option>
                                @foreach ($rootCategories as $val)
                                    <option value="{{ $val->category_id }}"
                                        {{ (int) old('category_parent') === (int) $val->category_id ? 'selected' : '' }}>
                                        {{ $val->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category_product_status">Hiển thị</label>
                            <select name="category_product_status" id="category_product_status"
                                class="form-control input-sm m-bot15">
                                <option value="0" {{ old('category_product_status', '0') === '0' ? 'selected' : '' }}>Hiển thị</option>
                                <option value="1" {{ old('category_product_status') === '1' ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>

                        <div class="g-admin-inline-actions">
                            <button type="submit" name="add_category_product" class="btn btn-info" @disabled(! $hasCategoryStoreRoute)>Thêm danh mục</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </section>
    </div>
@endsection
