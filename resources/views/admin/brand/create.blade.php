@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-brand-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Thương hiệu</span>
                <h1>Thêm thương hiệu sản phẩm</h1>
                <p>Tạo thương hiệu mới để gắn cho sản phẩm và sắp xếp hiển thị trên website.</p>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin thương hiệu</strong>
                    <p>Điền tên, đường dẫn tĩnh (slug), mô tả và thứ tự hiển thị trước khi lưu thương hiệu mới.</p>
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

                <form role="form" action="{{ url('/save-brand-product') }}" method="post" class="g-admin-site-info-form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="brand_product_name">Tên thương hiệu</label>
                        <input type="text" name="brand_product_name" id="brand_product_name" class="form-control"
                            value="{{ old('brand_product_name') }}" placeholder="Nhập tên thương hiệu" required>
                    </div>
                    <div class="form-group">
                        <label for="brand_slug">Đường dẫn tĩnh (slug)</label>
                        <input type="text" name="brand_slug" id="brand_slug" class="form-control"
                            value="{{ old('brand_slug') }}" placeholder="Nhập đường dẫn tĩnh (slug) (tùy chọn)">
                    </div>
                    <div class="form-group">
                        <label for="brand_product_desc">Mô tả</label>
                        <textarea name="brand_product_desc" id="brand_product_desc" rows="5" class="form-control"
                            placeholder="Nhập mô tả thương hiệu">{{ old('brand_product_desc') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="display_order">Thứ tự hiển thị</label>
                        <input type="number" name="display_order" id="display_order" class="form-control" min="0"
                            value="{{ old('display_order', 0) }}">
                    </div>
                    <div class="form-group">
                        <label for="logo">Logo thương hiệu</label>
                        <input type="file" name="logo" id="logo" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                        <small class="form-text text-muted">Chấp nhận: jpg, jpeg, png, webp. Dung lượng tối đa 2MB.</small>
                    </div>
                    <div class="form-group">
                        <label for="brand_product_status">Trạng thái</label>
                        <select name="brand_product_status" id="brand_product_status" class="form-control input-sm m-bot15">
                            <option value="0" {{ old('brand_product_status', '0') === '0' ? 'selected' : '' }}>Hiển thị</option>
                            <option value="1" {{ old('brand_product_status') === '1' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>
                    <div class="g-admin-inline-actions">
                        <button type="submit" class="btn btn-info">Thêm thương hiệu</button>
                        <a href="{{ route('admin.brands.management') }}" class="btn btn-default">Danh sách thương hiệu</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
