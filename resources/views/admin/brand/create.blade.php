@extends('admin_layout')

@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Thêm thương hiệu sản phẩm
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
                        <form role="form" action="{{ url('/save-brand-product') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="brand_product_name">Tên thương hiệu</label>
                                <input type="text" name="brand_product_name" id="brand_product_name" class="form-control"
                                    value="{{ old('brand_product_name') }}" placeholder="Nhập tên thương hiệu" required>
                            </div>
                            <div class="form-group">
                                <label for="brand_slug">Slug</label>
                                <input type="text" name="brand_slug" id="brand_slug" class="form-control"
                                    value="{{ old('brand_slug') }}" placeholder="Nhập slug (tùy chọn)">
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
                                <label for="brand_product_status">Trạng thái</label>
                                <select name="brand_product_status" id="brand_product_status"
                                    class="form-control input-sm m-bot15">
                                    <option value="0" {{ old('brand_product_status', '0') === '0' ? 'selected' : '' }}>Hiển
                                        thị</option>
                                    <option value="1" {{ old('brand_product_status') === '1' ? 'selected' : '' }}>Ẩn
                                    </option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info">Thêm thương hiệu</button>
                            <a href="{{ url('/all-brand-product') }}" class="btn btn-default">Danh sách thương hiệu</a>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
