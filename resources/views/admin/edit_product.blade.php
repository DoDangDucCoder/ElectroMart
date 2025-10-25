@extends('admin_layout')
@section('admin_content')
    @php
        /** @var \Illuminate\Support\Collection $edit_product */
        $product = $edit_product->first();
    @endphp

    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Cập nhật sản phẩm
                </header>

                <div class="panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
                        {{ Session::put('message', null) }}
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

                    @if ($product)
                        <div class="position-center">
                            <form role="form" action="{{ URL::to('/update-product/' . $product->product_id) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="product_name">Tên sản phẩm</label>
                                    <input type="text" name="product_name" id="product_name"
                                        class="form-control @error('product_name') is-invalid @enderror"
                                        value="{{ old('product_name', $product->product_name) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="product_quantity">Số lượng</label>
                                    <input type="number" min="0" name="product_quantity" id="product_quantity"
                                        class="form-control @error('product_quantity') is-invalid @enderror"
                                        value="{{ old('product_quantity', $product->product_quantity) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="product_slug">Slug</label>
                                    <input type="text" name="product_slug" id="product_slug"
                                        class="form-control @error('product_slug') is-invalid @enderror"
                                        value="{{ old('product_slug', $product->product_slug) }}">
                                </div>

                                <div class="form-group">
                                    <label for="product_price">Giá bán (VND)</label>
                                    <input type="text" name="product_price" id="product_price"
                                        class="form-control @error('product_price') is-invalid @enderror price_format"
                                        value="{{ old('product_price', $product->product_price) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="price_cost">Giá gốc (VND)</label>
                                    <input type="text" name="price_cost" id="price_cost"
                                        class="form-control @error('price_cost') is-invalid @enderror price_format"
                                        value="{{ old('price_cost', $product->price_cost) }}">
                                </div>

                                <div class="form-group">
                                    <label for="product_image">Hình ảnh sản phẩm</label>
                                    <input type="file" name="product_image" id="product_image"
                                        class="form-control-file @error('product_image') is-invalid @enderror"
                                        accept="image/*">
                                    @if ($product->product_image)
                                        <div class="mt-2">
                                            <img src="{{ asset($product->product_image) }}" alt="{{ $product->product_name }}"
                                                height="120">
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="document">Tài liệu</label>
                                    <input type="file" name="document" id="document"
                                        class="form-control-file @error('document') is-invalid @enderror"
                                        accept=".pdf,.doc,.docx">
                                    @if ($product->product_file)
                                        @php
                                            $documentPath = asset('uploads/document/' . $product->product_file);
                                        @endphp
                                        <p class="mt-2 mb-0">
                                            <a target="_blank" href="{{ $documentPath }}">
                                                {{ $product->product_file }}
                                            </a>
                                            <button type="button" data-document_id="{{ $product->product_id }}"
                                                class="btn btn-sm btn-danger btn-delete-document">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </p>
                                    @else
                                        <p class="mt-2 text-muted">Không có tài liệu đính kèm.</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="product_desc">Mô tả sản phẩm</label>
                                    <textarea style="resize: none" rows="6" class="form-control" name="product_desc" id="product_desc">{{ old('product_desc', $product->product_desc) }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="product_content">Nội dung sản phẩm</label>
                                    <textarea style="resize: none" rows="8" class="form-control" name="product_content"
                                        id="product_content">{{ old('product_content', $product->product_content) }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Danh mục sản phẩm</label>
                                    <select name="product_cate"
                                        class="form-control input-sm m-bot15 @error('product_cate') is-invalid @enderror"
                                        required>
                                        @foreach ($cate_product as $cate)
                                            <option value="{{ $cate->category_id }}"
                                                {{ (int) old('product_cate', $product->category_id) === (int) $cate->category_id ? 'selected' : '' }}>
                                                {{ $cate->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Thương hiệu</label>
                                    <select name="product_brand"
                                        class="form-control input-sm m-bot15 @error('product_brand') is-invalid @enderror">
                                        <option value="">-- Không chọn --</option>
                                        @foreach ($brand_product as $brand)
                                            <option value="{{ $brand->brand_id }}"
                                                {{ (int) old('product_brand', $product->brand_id) === (int) $brand->brand_id ? 'selected' : '' }}>
                                                {{ $brand->brand_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tags sản phẩm</label>
                                    <input type="text" data-role="tagsinput" name="product_tags"
                                        class="form-control @error('product_tags') is-invalid @enderror"
                                        value="{{ old('product_tags', $product->product_tags) }}">
                                </div>

                                <div class="form-group">
                                    <label>Hiển thị</label>
                                    <select name="product_status"
                                        class="form-control input-sm m-bot15 @error('product_status') is-invalid @enderror"
                                        required>
                                        <option value="0"
                                            {{ old('product_status', $product->product_status) == 0 ? 'selected' : '' }}>Hiển
                                            thị</option>
                                        <option value="1"
                                            {{ old('product_status', $product->product_status) == 1 ? 'selected' : '' }}>Ẩn
                                        </option>
                                    </select>
                                </div>

                                <button type="submit" name="add_product" class="btn btn-info">Cập nhật sản phẩm</button>
                            </form>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
