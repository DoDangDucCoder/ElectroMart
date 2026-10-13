@extends('admin_layout')

@section('admin_content')
    @php
        $meta = is_array($slider->meta) ? $slider->meta : [];
        $imagePath = trim((string) $slider->image_path);
    @endphp

    <div class="g-admin-page-shell g-admin-slider-edit">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Nội dung / Trình chiếu</span>
                <h1>Chỉnh sửa trình chiếu</h1>
                <p>Cập nhật nội dung, trạng thái, link và ảnh banner đang hiển thị từ bảng banners.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>SL{{ str_pad((string) $slider->id, 4, '0', STR_PAD_LEFT) }}</strong>
                    <span>Mã banner</span>
                </article>
                <article>
                    <strong>{{ $slider->is_active ? 'Đang hoạt động' : 'Tạm ẩn' }}</strong>
                    <span>Trạng thái</span>
                </article>
            </div>
        </section>

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

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin trình chiếu</strong>
                    <p>Upload ảnh mới chỉ khi cần thay thế ảnh hiện tại.</p>
                </div>
                <a href="{{ url('/manage-slider') }}" class="btn btn-default btn-sm">Quay lại danh sách</a>
            </header>
            <div class="g-admin-panel-body">
                <form action="{{ url('/update-slider/' . $slider->id) }}" method="post" enctype="multipart/form-data"
                    class="g-admin-site-info-form">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="slider_name">Tên slide</label>
                        <input type="text" name="slider_name" class="form-control" id="slider_name"
                            value="{{ old('slider_name', $slider->title) }}">
                    </div>

                    <div class="form-group">
                        <label for="slider_alt">Văn bản thay thế / tiêu đề nổi bật</label>
                        <input type="text" name="slider_alt" class="form-control" id="slider_alt"
                            value="{{ old('slider_alt', $meta['slider_alt'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Ảnh hiện tại</label>
                        <div>
                            <img src="{{ $imagePath !== '' && file_exists(public_path($imagePath)) ? asset($imagePath) : asset('uploads/product/no-image.svg') }}"
                                alt="{{ $slider->title }}" height="120" width="240">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slider_image">Thay ảnh mới</label>
                        <input type="file" name="slider_image" class="form-control" id="slider_image">
                    </div>

                    <div class="form-group">
                        <label for="slider_link_url">Liên kết khi bấm banner</label>
                        <input type="url" name="slider_link_url" class="form-control" id="slider_link_url"
                            value="{{ old('slider_link_url', $slider->link_url) }}" placeholder="https://example.com hoặc bỏ trống">
                    </div>

                    <div class="form-group">
                        <label for="slider_display_order">Thứ tự hiển thị</label>
                        <input type="number" name="slider_display_order" class="form-control" id="slider_display_order"
                            value="{{ old('slider_display_order', $slider->display_order) }}" min="0">
                    </div>

                    <div class="form-group">
                        <label for="slider_desc">Mô tả chiến dịch</label>
                        <textarea style="resize:none" rows="8" class="form-control" name="slider_desc" id="slider_desc">{{ old('slider_desc', $meta['slider_desc'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="slider_tone">Phong cách hiển thị</label>
                        <select name="slider_tone" id="slider_tone" class="form-control input-sm m-bot15">
                            @php($currentTone = old('slider_tone', $meta['slider_tone'] ?? 'auto'))
                            <option value="auto" @selected($currentTone === 'auto')>Tự động theo nội dung</option>
                            <option value="mobile" @selected($currentTone === 'mobile')>Điện thoại / di động</option>
                            <option value="gaming" @selected($currentTone === 'gaming')>Hiệu năng / chơi game</option>
                            <option value="lifestyle" @selected($currentTone === 'lifestyle')>Khuyến mãi / phong cách sống</option>
                            <option value="accessory" @selected($currentTone === 'accessory')>Phụ kiện / bộ kèm</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="slider_status">Trạng thái hiển thị</label>
                        <select name="slider_status" id="slider_status" class="form-control input-sm m-bot15">
                            <option value="1" @selected((string) old('slider_status', (int) $slider->is_active) === '1')>Đang hoạt động</option>
                            <option value="0" @selected((string) old('slider_status', (int) $slider->is_active) === '0')>Tạm ẩn</option>
                        </select>
                    </div>

                    <div class="g-admin-inline-actions">
                        <button type="submit" class="btn btn-info">Cập nhật trình chiếu</button>
                        <a href="{{ url('/manage-slider') }}" class="btn btn-default">Hủy</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
