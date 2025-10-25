@extends('admin_layout')

@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Cập nhật thương hiệu
                </header>
                <div class="panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
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

                    <div class="position-center">
                        <form role="form" action="{{ url('/update-brand-product/' . $brand->brand_id) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="brand_product_name">Tên thương hiệu</label>
                                <input type="text" name="brand_product_name" id="brand_product_name" class="form-control"
                                    value="{{ old('brand_product_name', $brand->brand_name) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="brand_product_slug">Slug</label>
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
                                <label for="brand_product_status">Trạng thái</label>
                                <select name="brand_product_status" id="brand_product_status"
                                    class="form-control input-sm m-bot15">
                                    <option value="0"
                                        {{ (string) old('brand_product_status', $brand->brand_status) === '0' ? 'selected' : '' }}>
                                        Hiển thị</option>
                                    <option value="1"
                                        {{ (string) old('brand_product_status', $brand->brand_status) === '1' ? 'selected' : '' }}>
                                        Ẩn</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info">Cập nhật thương hiệu</button>
                            <a href="{{ url('/all-brand-product') }}" class="btn btn-default">Quay lại danh sách</a>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
