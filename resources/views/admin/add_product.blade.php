@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-product-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Sản phẩm</span>
                <h1>Thêm sản phẩm</h1>
                <p>Tạo sản phẩm mới với thông tin bán hàng, nội dung mô tả, hình ảnh và tài liệu đính kèm.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($cate_product->count()) }}</strong>
                    <span>Danh mục có thể chọn</span>
                </article>
                <article>
                    <strong>{{ number_format($brand_product->count()) }}</strong>
                    <span>Thương hiệu có thể chọn</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin sản phẩm</strong>
                    <p>Điền đầy đủ thông tin cơ bản trước khi thêm thư viện ảnh, biến thể và nội dung chi tiết.</p>
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

                <form role="form" action="{{ URL::to('/save-product') }}" method="post" enctype="multipart/form-data"
                    class="g-admin-site-info-form">
                    @csrf

                    <div class="form-group">
                        <label for="product_name">Tên sản phẩm</label>
                        <input type="text" name="product_name" id="product_name"
                            class="form-control @error('product_name') is-invalid @enderror"
                            value="{{ old('product_name') }}" placeholder="Tên sản phẩm" required>
                    </div>

                    <div class="form-group">
                        <label for="product_quantity">Số lượng</label>
                        <input type="number" min="0" name="product_quantity" id="product_quantity"
                            class="form-control @error('product_quantity') is-invalid @enderror"
                            value="{{ old('product_quantity') }}" placeholder="Nhập số lượng" required>
                    </div>

                    <div class="form-group">
                        <label for="product_slug">Đường dẫn tĩnh (slug)</label>
                        <input type="text" name="product_slug" id="product_slug"
                            class="form-control @error('product_slug') is-invalid @enderror"
                            value="{{ old('product_slug') }}" placeholder="duong-dan-tinh-san-pham">
                    </div>

                    @include('admin.product.partials.pricing-fields')

                    <div class="form-group">
                        <label for="product_image">Hình ảnh sản phẩm</label>
                        <input type="file" name="product_image" id="product_image"
                            class="form-control-file @error('product_image') is-invalid @enderror" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label for="document">Tài liệu đính kèm</label>
                        <input type="file" name="document" id="document"
                            class="form-control-file @error('document') is-invalid @enderror"
                            accept=".pdf,.docx,.doc">
                    </div>

                    <div class="form-group">
                        <label for="product_desc">Mô tả sản phẩm</label>
                        <textarea style="resize: none" rows="6" class="form-control" name="product_desc" id="product_desc"
                            placeholder="Mô tả ngắn">{{ old('product_desc') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="product_content">Nội dung sản phẩm</label>
                        <textarea style="resize: none" rows="8" class="form-control" name="product_content" id="product_content"
                            placeholder="Thông tin chi tiết">{{ old('product_content') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="product_cate">Danh mục sản phẩm</label>
                        <select name="product_cate" id="product_cate"
                            class="form-control input-sm m-bot15 @error('product_cate') is-invalid @enderror" required>
                            @foreach ($cate_product as $cate)
                                @if ($cate->category_parent == 0)
                                    <option value="{{ $cate->category_id }}"
                                        {{ (int) old('product_cate') === (int) $cate->category_id ? 'selected' : '' }}>
                                        {{ $cate->category_name }}
                                    </option>
                                    @foreach ($cate_product as $cate_sub)
                                        @if ($cate_sub->category_parent != 0 && $cate_sub->category_parent == $cate->category_id)
                                            <option value="{{ $cate_sub->category_id }}"
                                                {{ (int) old('product_cate') === (int) $cate_sub->category_id ? 'selected' : '' }}>
                                                --- {{ $cate_sub->category_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="product_brand">Thương hiệu</label>
                        <select name="product_brand" id="product_brand"
                            class="form-control input-sm m-bot15 @error('product_brand') is-invalid @enderror">
                            <option value="">-- Không chọn --</option>
                            @foreach ($brand_product as $brand)
                                <option value="{{ $brand->brand_id }}"
                                    {{ (int) old('product_brand') === (int) $brand->brand_id ? 'selected' : '' }}>
                                    {{ $brand->brand_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="product_tags">Nhãn sản phẩm</label>
                        <input type="text" data-role="tagsinput" name="product_tags" id="product_tags"
                            class="form-control @error('product_tags') is-invalid @enderror"
                            value="{{ old('product_tags') }}">
                    </div>

                    <div class="form-group">
                        <label for="product_status">Hiển thị</label>
                        <select name="product_status" id="product_status"
                            class="form-control input-sm m-bot15 @error('product_status') is-invalid @enderror" required>
                            <option value="0" {{ old('product_status', '0') == '0' ? 'selected' : '' }}>Hiển thị</option>
                            <option value="1" {{ old('product_status') == '1' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>

                    <div class="g-admin-inline-actions">
                        <button type="submit" name="add_product" class="btn btn-info">Thêm sản phẩm</button>
                        <a href="{{ url('/all-product') }}" class="btn btn-default">Xem danh sách sản phẩm</a>
                    </div>
                </form>
            </div>
        </section>
    </div>

    @include('admin.product.partials.pricing-preview-script')
@endsection
