@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-slider-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Nội dung / Slider</span>
                <h1>Thêm slider</h1>
                <p>Tạo banner cho trang chủ và các khu vực cần hiển thị nổi bật trên website.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>Hero</strong>
                    <span>Banner chính storefront</span>
                </article>
                <article>
                    <strong>Media</strong>
                    <span>Ảnh và mô tả ngắn</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin slider</strong>
                    <p>Ảnh slider nên đúng tỷ lệ storefront hiện tại để tránh crop không mong muốn.</p>
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
                        <label for="slider_alt">Alt text / tiêu đề hero</label>
                        <input type="text" name="slider_alt" class="form-control" id="slider_alt"
                            value="{{ old('slider_alt') }}"
                            placeholder="Ví dụ: iPhone mới lên kệ với ưu đãi tốt trong tuần">
                        <span class="g-admin-cell-note">
                            Dùng làm headline và alt text cho banner. Nên viết ngắn, đúng chiến dịch và dễ đọc trên storefront.
                        </span>
                    </div>
                    <div class="form-group">
                        <label for="slider_image">Hình ảnh</label>
                        <input type="file" name="slider_image" class="form-control" id="slider_image">
                    </div>
                    <div class="form-group">
                        <label for="slider_desc">Mô tả chiến dịch</label>
                        <textarea style="resize:none" rows="8" class="form-control" name="slider_desc" id="slider_desc"
                            placeholder="Mô tả ngắn sẽ hiển thị dưới headline trong hero">{{ old('slider_desc') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="slider_tone">Tone hiển thị</label>
                        <select name="slider_tone" id="slider_tone" class="form-control input-sm m-bot15">
                            <option value="auto" @selected(old('slider_tone', 'auto') === 'auto')>Tự động theo nội dung</option>
                            <option value="mobile" @selected(old('slider_tone') === 'mobile')>Điện thoại / di động</option>
                            <option value="gaming" @selected(old('slider_tone') === 'gaming')>Gaming / hiệu năng</option>
                            <option value="lifestyle" @selected(old('slider_tone') === 'lifestyle')>Khuyến mãi / lifestyle</option>
                            <option value="accessory" @selected(old('slider_tone') === 'accessory')>Phụ kiện / combo</option>
                        </select>
                        <span class="g-admin-cell-note">
                            Tone giúp storefront tự chọn overlay, meta chip và ngữ điệu copy phù hợp với từng banner.
                        </span>
                    </div>
                    <div class="form-group">
                        <label for="slider_status">Hiển thị</label>
                        <select name="slider_status" id="slider_status" class="form-control input-sm m-bot15">
                            <option value="0" @selected(old('slider_status', '0') === '0')>Hiển thị</option>
                            <option value="1" @selected(old('slider_status') === '1')>Ẩn</option>
                        </select>
                    </div>
                    <button type="submit" name="add_slider" class="btn btn-info">Thêm slider</button>
                </form>
            </div>
        </section>
    </div>
@endsection
