@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-brand-edit">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Thương hiệu</span>
                <h1>Cập nhật thương hiệu</h1>
                <p>Điều chỉnh tên, mô tả, thứ tự hiển thị và trạng thái của thương hiệu đang chọn.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>#{{ $brand->brand_id }}</strong>
                    <span>Mã thương hiệu</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin thương hiệu</strong>
                    <p>Cập nhật dữ liệu hiển thị để thương hiệu đồng bộ với danh mục sản phẩm đang bán.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form role="form" action="{{ url('/update-brand-product/' . $brand->brand_id) }}" method="post"
                    class="g-admin-site-info-form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="brand_product_name">Tên thương hiệu</label>
                        <input type="text" name="brand_product_name" id="brand_product_name" class="form-control"
                            value="{{ old('brand_product_name', $brand->brand_name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="brand_product_slug">Đường dẫn tĩnh (slug)</label>
                        <input type="text" name="brand_product_slug" id="brand_product_slug" class="form-control"
                            value="{{ old('brand_product_slug', $brand->brand_slug) }}">
                    </div>
                    <div class="form-group">
                        <label for="brand_product_desc">Mô tả</label>
                        <textarea name="brand_product_desc" id="brand_product_desc" rows="5" class="form-control">{{ old('brand_product_desc', $brand->brand_desc) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="display_order">Thứ tự hiển thị</label>
                        <input type="number" name="display_order" id="display_order" class="form-control" min="0"
                            value="{{ old('display_order', $brand->display_order) }}">
                    </div>
                    <div class="form-group">
                        <label for="logo">Logo thương hiệu</label>
                        @if ($brand->logo_path)
                            <div class="mb-2">
                                <img src="{{ asset($brand->logo_path) }}" alt="Logo hiện tại" style="max-height:80px;max-width:200px;border:1px solid #ddd;padding:4px;border-radius:4px;">
                                <small class="d-block text-muted mt-1">Logo hiện tại</small>
                            </div>
                        @endif
                        <input type="file" name="logo" id="logo" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                        <small class="form-text text-muted">Chấp nhận: jpg, jpeg, png, webp. Tối đa 2MB. Để trống nếu không muốn thay logo.</small>
                    </div>
                    <div class="form-group">
                        <label for="brand_product_status">Trạng thái</label>
                        <select name="brand_product_status" id="brand_product_status" class="form-control input-sm m-bot15">
                            <option value="0"
                                {{ (string) old('brand_product_status', $brand->brand_status) === '0' ? 'selected' : '' }}>
                                Hiển thị</option>
                            <option value="1"
                                {{ (string) old('brand_product_status', $brand->brand_status) === '1' ? 'selected' : '' }}>
                                Ẩn</option>
                        </select>
                    </div>
                    <div class="g-admin-inline-actions">
                        <button type="submit" class="btn btn-info">Cập nhật thương hiệu</button>
                        <a href="{{ route('admin.brands.management') }}" class="btn btn-default">Quay lại danh sách</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
