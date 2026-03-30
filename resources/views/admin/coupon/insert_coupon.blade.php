@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-coupon-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Coupon</span>
                <h1>Thêm mã giảm giá</h1>
                <p>Tạo mã giảm giá mới cho các chiến dịch khuyến mãi của cửa hàng.</p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>2 kiểu</strong>
                    <span>Giảm theo % hoặc theo tiền</span>
                </article>
                <article>
                    <strong>Hiệu lực</strong>
                    <span>Cấu hình theo ngày bắt đầu và kết thúc</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Thông tin coupon</div>
                    <p>Điền đầy đủ phạm vi hiệu lực, mã và quota trước khi gửi cho khách hàng.</p>
                </div>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                <form action="{{ URL::to('/insert-coupon-code') }}" method="post" class="g-admin-form-grid">
                    @csrf

                    <div class="form-group">
                        <label for="coupon_name">Tên mã giảm giá</label>
                        <input type="text" name="coupon_name" class="form-control" id="coupon_name" placeholder="Ví dụ: Flash Sale cuối tuần">
                    </div>

                    <div class="form-group">
                        <label for="coupon_code">Mã giảm giá</label>
                        <input type="text" name="coupon_code" class="form-control" id="coupon_code" placeholder="Ví dụ: WEEKEND50">
                    </div>

                    <div class="form-group">
                        <label for="start_coupon">Ngày bắt đầu</label>
                        <input type="text" name="coupon_date_start" class="form-control" id="start_coupon" placeholder="YYYY-MM-DD">
                    </div>

                    <div class="form-group">
                        <label for="end_coupon">Ngày kết thúc</label>
                        <input type="text" name="coupon_date_end" class="form-control" id="end_coupon" placeholder="YYYY-MM-DD">
                    </div>

                    <div class="form-group">
                        <label for="coupon_time">Số lượng mã</label>
                        <input type="text" name="coupon_time" class="form-control" id="coupon_time" placeholder="Ví dụ: 100">
                    </div>

                    <div class="form-group">
                        <label for="coupon_condition">Tính năng mã</label>
                        <select name="coupon_condition" id="coupon_condition" class="form-control input-sm m-bot15">
                            <option value="0">----Chọn-----</option>
                            <option value="1">Giảm theo phần trăm</option>
                            <option value="2">Giảm theo tiền</option>
                        </select>
                    </div>

                    <div class="form-group g-admin-form-span-2">
                        <label for="coupon_number">Nhập số % hoặc tiền giảm</label>
                        <input type="text" name="coupon_number" class="form-control" id="coupon_number" placeholder="Ví dụ: 10 hoặc 50000">
                    </div>

                    <div class="g-admin-inline-actions g-admin-form-span-2">
                        <button type="submit" name="add_coupon" class="btn btn-info">Thêm mã</button>
                        <a href="{{ url('/list-coupon') }}" class="btn btn-default">Xem danh sách mã giảm giá</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
