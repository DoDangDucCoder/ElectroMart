@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-product-attribute-page">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Sản phẩm / Biến thể</span>
                <h1>Thuộc tính sản phẩm</h1>
                <p>Quản lý biến thể của sản phẩm đang chọn trên cùng một màn hình.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $product->product_name }}</strong>
                    <span>Sản phẩm đang cấu hình</span>
                </article>
                <article>
                    <strong>{{ $product_attribute->count() }}</strong>
                    <span>Biến thể hiện có</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @if ($errors->has('name'))
            <div class="alert alert-danger">
                {{ $errors->first('name') }}
            </div>
        @endif

        <div class="g-admin-panel-grid two-col">
            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Thêm thuộc tính mới</strong>
                        <p>Tạo biến thể mới cho sản phẩm đang chọn.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <form action="{{ route('product-attribute.store') }}" method="post" enctype="multipart/form-data"
                        class="g-admin-site-info-form">
                        @csrf
                        <div class="form-group">
                            <label>Sản phẩm</label>
                            <input type="hidden" value="{{ $product->product_id }}" name="product_id">
                            <input type="text" class="form-control" name="product_name" readonly
                                value="{{ $product->product_name }}">
                        </div>
                        <div class="form-group">
                            <label for="variant_name">Tên thuộc tính</label>
                            <input type="text" required class="form-control" name="name" id="variant_name"
                                placeholder="Ví dụ: Màu đen 512GB">
                        </div>
                        <div class="form-group">
                            <label for="variant_price">Giá</label>
                            <input type="number" required min="1" class="form-control" name="price"
                                id="variant_price" placeholder="Giá bán">
                        </div>
                        <div class="form-group">
                            <label for="variant_quantity">Số lượng</label>
                            <input type="number" required min="1" class="form-control" name="quantity"
                                id="variant_quantity" placeholder="Số lượng">
                        </div>
                        <div class="form-group">
                            <label for="variant_image">Hình ảnh thuộc tính</label>
                            <input type="file" required name="image" class="form-control" id="variant_image">
                        </div>
                        <div class="form-group">
                            <label for="variant_description">Mô tả thuộc tính</label>
                            <textarea style="resize:none" rows="8" class="form-control" name="description" id="variant_description"
                                required placeholder="Mô tả thuộc tính"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="attribute_id">Thuộc tính nền</label>
                            <select name="attribute_id" id="attribute_id" class="form-control input-sm m-bot15" required>
                                @foreach ($attribute as $val)
                                    <option value="{{ $val->id }}">{{ $val->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" name="add_attribute" class="btn btn-info">Thêm thuộc tính</button>
                    </form>
                </div>
            </section>

            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Danh sách thuộc tính hiện có</strong>
                        <p>Biến thể đã tạo cho sản phẩm này.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped g-admin-data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Tên thuộc tính</th>
                                    <th>Thuộc tính</th>
                                    <th>Hình ảnh</th>
                                    <th>Số lượng</th>
                                    <th>Giá</th>
                                    <th>Quản lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($product_attribute as $key => $pro_attr)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $pro_attr->product->product_name }}</td>
                                        <td><strong>{{ $pro_attr->name }}</strong></td>
                                        <td>{{ $pro_attr->attribute->name }}</td>
                                        <td>
                                            <img src="{{ asset('/uploads/attribute/' . $pro_attr->image) }}"
                                                alt="{{ $pro_attr->name }}" style="width:88px;height:88px;">
                                        </td>
                                        <td>{{ $pro_attr->quantity }}</td>
                                        <td>{{ number_format($pro_attr->price, 0, ',', '.') }}đ</td>
                                        <td>
                                            <form action="{{ route('product-attribute.delete', $pro_attr->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa biến thể này không?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="g-admin-empty-state">Sản phẩm này chưa có thuộc tính nào.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
