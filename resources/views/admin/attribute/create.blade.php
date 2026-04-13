@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-attribute-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Sản phẩm / Thuộc tính</span>
                <h1>Thêm thuộc tính</h1>
                <p>Tạo thuộc tính nền để tái sử dụng cho các biến thể sản phẩm.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>Dùng chung</strong>
                    <span>Dùng lại cho nhiều sản phẩm</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @elseif (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="list-unstyled mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @unless ($attributeTableReady ?? true)
            <div class="alert alert-warning">Bảng thuộc tính nền chưa sẵn sàng trên môi trường này. Form đang được khóa để tránh lỗi ghi dữ liệu.</div>
        @endunless

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin thuộc tính</strong>
                    <p>Tên nên ngắn gọn, mô tả đủ rõ để team merch/product hiểu khi gán vào sản phẩm.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <form action="{{ route('attribute.store') }}" method="post" class="g-admin-site-info-form">
                    @csrf
                    <div class="form-group">
                        <label for="attribute_name">Tên thuộc tính</label>
                        <input type="text" required class="form-control" name="name" id="attribute_name"
                            value="{{ old('name') }}"
                            placeholder="Ví dụ: Màu sắc" @disabled(! ($attributeTableReady ?? true))>
                    </div>
                    <div class="form-group">
                        <label for="attribute_description">Mô tả thuộc tính</label>
                        <textarea style="resize:none" rows="8" class="form-control" name="description" id="attribute_description"
                            required placeholder="Mô tả thuộc tính" @disabled(! ($attributeTableReady ?? true))>{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" name="add_attribute" class="btn btn-info"
                        @disabled(! ($attributeTableReady ?? true))>
                        Thêm thuộc tính mới
                    </button>
                </form>
            </div>
        </section>
    </div>
@endsection
