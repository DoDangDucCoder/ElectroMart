@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-attribute-edit">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Sản phẩm / Thuộc tính</span>
                <h1>Cập nhật thuộc tính</h1>
                <p>Cập nhật thuộc tính đang được dùng cho biến thể và cấu hình sản phẩm.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $attribute->name }}</strong>
                    <span>Thuộc tính đang chỉnh</span>
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

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin thuộc tính</strong>
                    <p>Điều chỉnh tên và mô tả để team quản trị dùng thống nhất hơn.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <form action="{{ route('attribute.update', $attribute->id) }}" method="post" class="g-admin-site-info-form">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Tên thuộc tính</label>
                        <input type="text" class="form-control" name="name" id="name"
                            value="{{ old('name', $attribute->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea class="form-control" name="description" id="description" rows="8" required>{{ old('description', $attribute->description) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </form>
            </div>
        </section>
    </div>
@endsection
