@extends('admin_layout')

@section('admin_content')
    @php
        /** @var \Illuminate\Support\Collection $edit_product */
        $product = $edit_product->first();
    @endphp

    <div class="g-admin-page-shell g-admin-product-edit">
        @if ($product)
            <section class="g-admin-page-hero">
                <div>
                    <span class="g-admin-page-eyebrow">Sản phẩm</span>
                    <h1>Cập nhật sản phẩm</h1>
                    <p>Điều chỉnh thông tin bán hàng, nội dung, hình ảnh và tài liệu của sản phẩm đang chọn.</p>
                </div>
                <div class="g-admin-page-stats">
                    <article>
                        <strong>{{ number_format($product->product_quantity) }}</strong>
                        <span>Số lượng hiện tại</span>
                    </article>
                    <article>
                        <strong>{{ (string) $product->product_status === '0' ? 'Hiển thị' : 'Ẩn' }}</strong>
                        <span>Trạng thái sản phẩm</span>
                    </article>
                </div>
            </section>
        @endif

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin sản phẩm</strong>
                    <p>Cập nhật dữ liệu hiển thị, hình ảnh và tệp đính kèm cho sản phẩm này.</p>
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

                <div id="document_notice" class="alert alert-info" hidden></div>

                @if ($product)
                    <form role="form" action="{{ URL::to('/update-product/' . $product->product_id) }}" method="post"
                        enctype="multipart/form-data" class="g-admin-site-info-form">
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
                            <label for="product_slug">Đường dẫn tĩnh (slug)</label>
                            <input type="text" name="product_slug" id="product_slug"
                                class="form-control @error('product_slug') is-invalid @enderror"
                                value="{{ old('product_slug', $product->product_slug) }}">
                        </div>

                        @include('admin.product.partials.pricing-fields', ['product' => $product])

                        <div class="form-group">
                            <label for="product_image">Hình ảnh sản phẩm</label>
                            <input type="file" name="product_image" id="product_image"
                                class="form-control-file @error('product_image') is-invalid @enderror" accept="image/*">
                            @if ($product->product_image)
                                <div class="g-admin-surface" style="margin-top:12px;">
                                    <h3>Hình ảnh hiện tại</h3>
                                    <img src="{{ \App\Support\AssetUrl::product($product->product_image) }}" alt="{{ $product->product_name }}" height="120">
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="document">Tài liệu</label>
                            <input type="file" name="document" id="document"
                                class="form-control-file @error('document') is-invalid @enderror"
                                accept=".pdf,.doc,.docx">
                            <p class="text-muted" style="margin-top:12px;" data-document-empty-state
                                {{ $product->product_file ? 'hidden' : '' }}>
                                Không có tài liệu đính kèm.
                            </p>
                            @if ($product->product_file)
                                @php
                                    $documentPath = asset('uploads/document/' . $product->product_file);
                                @endphp
                                <div class="g-admin-surface" style="margin-top:12px;" data-document-card>
                                    <h3>Tài liệu hiện tại</h3>
                                    <p class="mb-0">
                                        <a target="_blank" href="{{ $documentPath }}">{{ $product->product_file }}</a>
                                        <button type="button" data-document_id="{{ $product->product_id }}"
                                            class="btn btn-sm btn-danger btn-delete-document">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </p>
                                </div>
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
                            <label for="product_cate">Danh mục sản phẩm</label>
                            <select name="product_cate" id="product_cate"
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
                            <label for="product_brand">Thương hiệu</label>
                            <select name="product_brand" id="product_brand"
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
                            <label for="product_tags">Nhãn sản phẩm</label>
                            <input type="text" data-role="tagsinput" name="product_tags" id="product_tags"
                                class="form-control @error('product_tags') is-invalid @enderror"
                                value="{{ old('product_tags', $product->product_tags) }}">
                        </div>

                        <div class="form-group">
                            <label for="product_status">Hiển thị</label>
                            <select name="product_status" id="product_status"
                                class="form-control input-sm m-bot15 @error('product_status') is-invalid @enderror"
                                required>
                                <option value="0" {{ old('product_status', $product->product_status) == 0 ? 'selected' : '' }}>Hiển thị</option>
                                <option value="1" {{ old('product_status', $product->product_status) == 1 ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>

                        <div class="g-admin-inline-actions">
                            <button type="submit" name="add_product" class="btn btn-info">Cập nhật sản phẩm</button>
                            <a href="{{ url('/all-product') }}" class="btn btn-default">Quay lại danh sách</a>
                        </div>
                    </form>
                @else
                    <div class="g-admin-empty-state">Không tìm thấy sản phẩm cần cập nhật.</div>
                @endif
            </div>
        </section>
    </div>

    @include('admin.product.partials.pricing-preview-script')
@endsection
