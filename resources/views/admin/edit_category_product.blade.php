@extends('admin_layout')

@section('admin_content')
    @php
        $currentCategory = collect($edit_category_product)->first();
    @endphp

    <div class="g-admin-page-shell g-admin-category-product-edit">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Danh mục sản phẩm</span>
                <h1>Cập nhật danh mục sản phẩm</h1>
                <p>Điều chỉnh tên, slug, mô tả và vị trí của danh mục trong cây sản phẩm.</p>
            </div>
            @if ($currentCategory)
                <div class="g-admin-page-stats">
                    <article>
                        <strong>#{{ $currentCategory->category_id }}</strong>
                        <span>Mã danh mục</span>
                    </article>
                </div>
            @endif
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin danh mục</strong>
                    <p>Cập nhật dữ liệu hiển thị để danh mục đồng bộ với cây điều hướng sản phẩm.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                    {{ Session::put('message', null) }}
                @endif

                @if ($currentCategory)
                    <form role="form" action="{{ URL::to('/update-category-product/' . $currentCategory->category_id) }}"
                        method="post" class="g-admin-site-info-form">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="slug">Tên danh mục</label>
                            <input type="text" value="{{ $currentCategory->category_name }}" onkeyup="ChangeToSlug();"
                                name="category_product_name" class="form-control" id="slug">
                        </div>
                        <div class="form-group">
                            <label for="convert_slug">Slug</label>
                            <input type="text" value="{{ $currentCategory->slug_category_product }}"
                                name="slug_category_product" class="form-control" id="convert_slug">
                        </div>
                        <div class="form-group">
                            <label for="category_product_desc">Mô tả danh mục</label>
                            <textarea style="resize: none" rows="8" class="form-control" name="category_product_desc"
                                id="category_product_desc">{{ $currentCategory->category_desc }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="category_product_keywords">Từ khóa danh mục</label>
                            <textarea style="resize: none" rows="8" class="form-control" name="category_product_keywords"
                                id="category_product_keywords" placeholder="Mô tả danh mục">{{ $currentCategory->meta_keywords }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="category_parent">Thuộc danh mục</label>
                            <select name="category_parent" id="category_parent" class="form-control input-sm m-bot15">
                                <option value="0">--- Danh mục cha ---</option>
                                @foreach ($category as $val)
                                    @if ($val->category_parent == 0)
                                        <option {{ $val->category_id == $currentCategory->category_id ? 'selected' : '' }}
                                            value="{{ $val->category_id }}">{{ $val->category_name }}</option>
                                    @endif

                                    @foreach ($category as $val2)
                                        @if ($val2->category_parent == $val->category_id)
                                            <option
                                                {{ $val2->category_id == $currentCategory->category_id ? 'selected' : '' }}
                                                value="{{ $val2->category_id }}">---{{ $val2->category_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="g-admin-inline-actions">
                            <button type="submit" name="update_category_product" class="btn btn-info">Cập nhật danh mục</button>
                        </div>
                    </form>
                @else
                    <div class="g-admin-empty-state">Không tìm thấy danh mục cần cập nhật.</div>
                @endif
            </div>
        </section>
    </div>
@endsection
