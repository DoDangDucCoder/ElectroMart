@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-slider-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Nội dung / Trình chiếu</span>
                <h1>Thêm trình chiếu</h1>
                <p>Tạo ảnh trình chiếu cho trang chủ và các khu vực cần hiển thị nổi bật trên website.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>Nổi bật</strong>
                    <span>Khối trình chiếu chính của giao diện cửa hàng</span>
                </article>
                <article>
                    <strong>Hình ảnh</strong>
                    <span>Ảnh và mô tả ngắn gọn</span>
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
                {{ $errors->first() }}
            </div>
        @endif

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin trình chiếu</strong>
                    <p>Ảnh trình chiếu nên đúng tỷ lệ giao diện cửa hàng hiện tại để tránh bị cắt ngoài ý muốn.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <form action="{{ url('/insert-slider') }}" method="post" enctype="multipart/form-data"
                    class="g-admin-site-info-form">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="slider_name">Tên slide</label>
                        <input type="text" name="slider_name" class="form-control" id="slider_name"
                            value="{{ old('slider_name') }}" placeholder="Tên slide">
                    </div>
                    <div class="form-group">
                        <label for="slider_alt">Văn bản thay thế / tiêu đề nổi bật</label>
                        <input type="text" name="slider_alt" class="form-control" id="slider_alt"
                            value="{{ old('slider_alt') }}"
                            placeholder="Ví dụ: iPhone mới lên kệ với ưu đãi tốt trong tuần">
                        <span class="g-admin-cell-note">
                            Dùng làm tiêu đề và văn bản thay thế cho ảnh trình chiếu. Nên viết ngắn, đúng chiến dịch và dễ đọc trên giao diện cửa hàng.
                        </span>
                    </div>
                    <div class="form-group">
                        <label for="slider_image">Hình ảnh</label>
                        <input type="file" name="slider_image" class="form-control" id="slider_image">
                    </div>
                    <div class="form-group">
                        <label for="slider_link_url">Liên kết khi bấm banner</label>
                        <input type="url" name="slider_link_url" class="form-control" id="slider_link_url"
                            value="{{ old('slider_link_url') }}" placeholder="https://example.com hoặc bỏ trống">
                    </div>
                    <div class="form-group">
                        <label for="slider_display_order">Thứ tự hiển thị</label>
                        <input type="number" name="slider_display_order" class="form-control" id="slider_display_order"
                            value="{{ old('slider_display_order') }}" min="0" placeholder="Tự động nếu bỏ trống">
                    </div>
                    <div class="form-group">
                        <label for="slider_desc">Mô tả chiến dịch</label>
                        <textarea style="resize:none" rows="8" class="form-control" name="slider_desc" id="slider_desc"
                            placeholder="Mô tả ngắn sẽ hiển thị dưới tiêu đề nổi bật">{{ old('slider_desc') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="slider_tone">Phong cách hiển thị</label>
                        <select name="slider_tone" id="slider_tone" class="form-control input-sm m-bot15">
                            <option value="auto" @selected(old('slider_tone', 'auto') === 'auto')>Tự động theo nội dung</option>
                            <option value="mobile" @selected(old('slider_tone') === 'mobile')>Điện thoại / di động</option>
                            <option value="gaming" @selected(old('slider_tone') === 'gaming')>Hiệu năng / chơi game</option>
                            <option value="lifestyle" @selected(old('slider_tone') === 'lifestyle')>Khuyến mãi / phong cách sống</option>
                            <option value="accessory" @selected(old('slider_tone') === 'accessory')>Phụ kiện / bộ kèm</option>
                        </select>
                        <span class="g-admin-cell-note">
                            Phong cách giúp giao diện cửa hàng tự chọn lớp phủ, nhãn phụ và lời dẫn phù hợp với từng ảnh trình chiếu.
                        </span>
                    </div>
                    <div class="form-group">
                        <label for="slider_status">Trạng thái hiển thị</label>
                        <select name="slider_status" id="slider_status" class="form-control input-sm m-bot15">
                            <option value="1" @selected(old('slider_status', '1') === '1')>Hiển thị ngay trên giao diện cửa hàng</option>
                            <option value="0" @selected(old('slider_status') === '0')>Ẩn tạm thời</option>
                        </select>
                        <span class="g-admin-cell-note">
                            Trình chiếu chỉ xuất hiện ở giao diện cửa hàng khi trạng thái là <strong>Hiển thị</strong>.
                        </span>
                    </div>
                    <button type="submit" name="add_slider" class="btn btn-info">Thêm trình chiếu</button>
                </form>
            </div>
        </section>
    </div>
@endsection
